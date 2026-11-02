// Tiny Puppeteer-over-HTTP sidecar for the social share card renderer.
// One Chromium is launched at boot and reused across requests — a fresh
// browser per hit would add ~700ms of cold-start on every card miss.
const Fastify = require('fastify');
const puppeteer = require('puppeteer');

const PORT = Number(process.env.PORT || 3000);
const HOST = process.env.HOST || '0.0.0.0';
const READY_FLAG = 'window.__ogReady === true';
// The Blade renders in PHP and is POSTed here as raw HTML — no navigation,
// no network I/O beyond the sidecar's own paint. Sub-second on a warm
// browser in isolation. The cap exists only as a backstop for a wedged
// Chrome; under healthy load it should never fire.
const HARD_RENDER_CAP_MS = Number(process.env.HARD_RENDER_CAP_MS || 15000);
const MAX_CONCURRENT_RENDERS = Number(process.env.MAX_CONCURRENT_RENDERS || 8);
const QUEUE_WAIT_MS = Number(process.env.QUEUE_WAIT_MS || 5000);
// Recycle the browser periodically. A single Chromium instance held open
// under sustained render churn eventually degrades even on a well-resourced
// box (accumulated renderer processes, GPU cache, IPC queues) — pages start
// hanging on waitForFunction or screenshot while CPU and RAM sit idle. We
// bound that state by closing the browser after either threshold, but only
// while there are no active renders so we never yank a session mid-flight.
const RECYCLE_AFTER_RENDERS = Number(process.env.RECYCLE_AFTER_RENDERS || 500);
const RECYCLE_AFTER_MS = Number(process.env.RECYCLE_AFTER_MS || 30 * 60 * 1000);
// If we get this many consecutive hard-cap failures with zero success in
// between, Chrome is wedged — force a browser restart even if there are
// still active renders. Those active renders would have hit the cap anyway,
// so we're not throwing away work, just failing them faster and letting
// the next request find a healthy browser.
const CAP_FIRES_BEFORE_FORCE_RECYCLE = Number(process.env.CAP_FIRES_BEFORE_FORCE_RECYCLE || 3);

const app = Fastify({
    logger: true,
    // The Blade HTML can push past Fastify's 1 MB default when the timeline
    // and status labels are long. Give ourselves headroom without going wild.
    bodyLimit: 4 * 1024 * 1024,
});

// Memoised browser. Cleared on disconnect so the next getBrowser() call
// launches a fresh instance instead of handing out a dead reference.
let browserPromise = null;
let browserRenderCount = 0;
let browserLaunchedAt = 0;
let capFiresSinceSuccess = 0;

function browserIsAlive(b) {
    if (!b) return false;
    // Puppeteer v22+ exposes `connected` as a getter; older versions had
    // an `isConnected()` method. Handle either without exploding.
    if (typeof b.connected === 'boolean') return b.connected;
    if (typeof b.isConnected === 'function') {
        try { return b.isConnected(); } catch (e) { return false; }
    }
    return true;
}

async function getBrowser() {
    if (browserPromise) {
        const b = await browserPromise.catch(() => null);
        if (browserIsAlive(b)) return b;
        // Stale — fall through to a fresh launch.
        browserPromise = null;
    }
    const launch = puppeteer.launch({
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--font-render-hinting=medium',
        ],
    }).then((b) => {
        browserLaunchedAt = Date.now();
        browserRenderCount = 0;
        // If Chromium ever dies (OOM, segfault, docker kill), forget it so
        // the next request rebuilds. Guard against nulling browserPromise
        // when a fresh browser has already replaced this one — the
        // recycle path swaps browserPromise before closing the old browser,
        // and its disconnect fires later; without the identity check we'd
        // nuke the new browser's promise.
        b.on('disconnected', () => {
            app.log.warn('browser disconnected — will relaunch on next request');
            if (browserPromise === launch) {
                browserPromise = null;
            }
        });
        return b;
    }).catch((err) => {
        if (browserPromise === launch) browserPromise = null;
        throw err;
    });
    browserPromise = launch;
    return browserPromise;
}

// Swap the browser reference out and close the old one asynchronously.
// The disconnect handler on the old browser is identity-guarded so it
// won't null the new promise when it eventually fires.
function swapBrowser(log, reason) {
    if (!browserPromise) return;
    const rendered = browserRenderCount;
    const ageMs = browserLaunchedAt > 0 ? Date.now() - browserLaunchedAt : 0;
    const stale = browserPromise;
    browserPromise = null;
    browserRenderCount = 0;
    browserLaunchedAt = 0;
    capFiresSinceSuccess = 0;
    log.info({ rendered, ageMs, ...reason }, 'recycling browser');
    stale.then((b) => b.close().catch(() => {})).catch(() => {});
}

// Called when a render completes and the slot pool drops to idle. Recycle
// if the browser has crossed either quiet-threshold (render count or age).
function maybeRecycleBrowser(log) {
    if (activeRenders !== 0 || !browserPromise) return;
    const dueByCount = browserRenderCount >= RECYCLE_AFTER_RENDERS;
    const dueByTime = browserLaunchedAt > 0 && Date.now() - browserLaunchedAt >= RECYCLE_AFTER_MS;
    if (!dueByCount && !dueByTime) return;
    swapBrowser(log, { dueByCount, dueByTime });
}

// Counting semaphore. Caps concurrent Chrome pages so a MISS storm can't
// swamp memory (each page holds ~150-250 MB of Chromium state). Waiters
// are woken FIFO; anyone that waits more than QUEUE_WAIT_MS gives up so
// the caller can fall back to the static card instead of piling up.
let activeRenders = 0;
const waiters = [];

function acquireSlot() {
    if (activeRenders < MAX_CONCURRENT_RENDERS) {
        activeRenders++;
        return Promise.resolve();
    }
    return new Promise((resolve, reject) => {
        const t = setTimeout(() => {
            const i = waiters.indexOf(entry);
            if (i !== -1) waiters.splice(i, 1);
            const err = new Error(`queue wait ${QUEUE_WAIT_MS}ms exceeded (${activeRenders}/${MAX_CONCURRENT_RENDERS} slots busy)`);
            err.statusCode = 503;
            reject(err);
        }, QUEUE_WAIT_MS);
        const entry = { resolve, timeout: t };
        waiters.push(entry);
    });
}

function releaseSlot() {
    const next = waiters.shift();
    if (next) {
        clearTimeout(next.timeout);
        next.resolve();
    } else {
        activeRenders--;
    }
}

app.get('/health', async () => ({
    ok: true,
    activeRenders,
    queued: waiters.length,
    browserRenderCount,
    browserAgeMs: browserLaunchedAt > 0 ? Date.now() - browserLaunchedAt : 0,
    capFiresSinceSuccess,
}));

async function renderPngInner(log, state, { html, w, h }) {
    try {
        const browser = await getBrowser();
        state.page = await browser.newPage();
        await state.page.setViewport({ width: w, height: h, deviceScaleFactor: 1 });

        // setContent parses and commits the DOM without any navigation —
        // no origin fetch, no TLS handshake, no dependency on the PHP-FPM
        // pool. The Blade must therefore inline every asset it needs
        // (SVGs, fonts) because relative URLs resolve against about:blank.
        await state.page.setContent(html, {
            waitUntil: 'domcontentloaded',
            timeout: 5000,
        });

        // Blade sets __ogReady on the second requestAnimationFrame, so
        // this normally resolves within one frame. Any timeout here now
        // is a real bug (script never ran) — but still fall through and
        // capture rather than 500 the crawler.
        try {
            await state.page.waitForFunction(READY_FLAG, { timeout: 2000 });
        } catch (e) {
            log.warn({ err: e.message }, 'ogReady flag not set within 2s, capturing anyway');
        }

        return await state.page.screenshot({ type: 'png', clip: { x: 0, y: 0, width: w, height: h } });
    } finally {
        // If the hard cap already forced a close, skip — page.close() on an
        // already-closed page rejects and we'd swallow it anyway.
        if (state.page && !state.pageClosedByCap) {
            await state.page.close().catch(() => {});
        }
    }
}

// Wrap the actual render in a hard cap so a hung page can never pin an
// inFlight entry forever. On cap fire we close JUST the current page —
// killing the whole browser would yank sessions out from under sibling
// renders (they'd die with "Session closed"). Closing the page unblocks
// any await inside renderPngInner so its finally can run and stop the
// Chrome tab from leaking.
//
// A run of consecutive cap fires with no success in between is treated as
// a wedged-browser signal — we swap the whole browser out (see swapBrowser).
// Under sustained load activeRenders may never hit 0, so idle-only
// recycling wouldn't fire and the wedge would persist.
function renderPng(log, opts) {
    const state = { page: null, pageClosedByCap: false };
    let timeoutId;
    const inner = renderPngInner(log, state, opts);
    return Promise.race([
        inner.then((v) => {
            clearTimeout(timeoutId);
            capFiresSinceSuccess = 0;
            return v;
        }),
        new Promise((_, reject) => {
            timeoutId = setTimeout(() => {
                log.error(`hard render cap ${HARD_RENDER_CAP_MS}ms exceeded`);
                if (state.page) {
                    state.pageClosedByCap = true;
                    state.page.close().catch(() => {});
                }
                capFiresSinceSuccess++;
                if (capFiresSinceSuccess >= CAP_FIRES_BEFORE_FORCE_RECYCLE) {
                    swapBrowser(log, { reason: 'consecutive-cap-fires', fires: capFiresSinceSuccess });
                }
                reject(new Error(`hard render cap ${HARD_RENDER_CAP_MS}ms exceeded`));
            }, HARD_RENDER_CAP_MS);
        }),
    ]);
}

// Coalesce concurrent renders of the same payload. When many crawlers hit
// /og/fogo/{id}.png at once and every request misses the PHP disk cache,
// each one would spawn a Chrome page — 200 MB × N + CPU. Keyed by a hash
// of the HTML so two clients asking for the same card wait on the same
// rendering promise. Cleared on completion (success or failure).
const inFlight = new Map();

function coalesceKey(html, w, h) {
    // Cheap FNV-1a-ish rolling hash over the HTML — we don't need
    // cryptographic strength, just something that collides on identical
    // payloads. Full-string keys would blow the map on long timelines.
    let h1 = 0x811c9dc5;
    for (let i = 0; i < html.length; i++) {
        h1 ^= html.charCodeAt(i);
        h1 = (h1 * 0x01000193) >>> 0;
    }
    return `${h1.toString(16)}|${w}x${h}`;
}

app.post('/render', async (req, reply) => {
    const { html, width, height } = req.body || {};
    if (!html || typeof html !== 'string') {
        return reply.code(400).send({ error: 'html is required' });
    }

    const w = Number(width)  || 1200;
    const h = Number(height) ||  630;
    const key = coalesceKey(html, w, h);

    let promise = inFlight.get(key);
    if (!promise) {
        promise = (async () => {
            await acquireSlot();
            browserRenderCount++;
            try {
                return await renderPng(req.log, { html, w, h });
            } finally {
                releaseSlot();
                maybeRecycleBrowser(req.log);
            }
        })().finally(() => inFlight.delete(key));
        inFlight.set(key, promise);
    } else {
        req.log.info({ key }, 'coalesced onto in-flight render');
    }

    try {
        const png = await promise;
        reply.header('Content-Type', 'image/png');
        return reply.send(png);
    } catch (err) {
        const status = err.statusCode || 500;
        req.log.error({ err: err.message }, 'render failed');
        return reply.code(status).send({ error: err.message });
    }
});

app.listen({ port: PORT, host: HOST }).catch((err) => {
    app.log.error(err);
    process.exit(1);
});
