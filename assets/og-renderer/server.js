// Tiny Puppeteer-over-HTTP sidecar for the social share card renderer.
// One Chromium is launched at boot and reused across requests — a fresh
// browser per hit would add ~700ms of cold-start on every card miss.
const Fastify = require('fastify');
const puppeteer = require('puppeteer');

const PORT = Number(process.env.PORT || 3000);
const HOST = process.env.HOST || '0.0.0.0';
const READY_FLAG = 'window.__ogReady === true';
const HARD_RENDER_CAP_MS = Number(process.env.HARD_RENDER_CAP_MS || 18000);
const MAX_CONCURRENT_RENDERS = Number(process.env.MAX_CONCURRENT_RENDERS || 3);
const QUEUE_WAIT_MS = Number(process.env.QUEUE_WAIT_MS || 5000);

const app = Fastify({ logger: true });

// Memoised browser. Cleared on disconnect so the next getBrowser() call
// launches a fresh instance instead of handing out a dead reference.
let browserPromise = null;

async function getBrowser() {
    if (browserPromise) {
        const b = await browserPromise;
        if (b && b.isConnected()) return b;
        // Stale — fall through to a fresh launch.
        browserPromise = null;
    }
    browserPromise = puppeteer.launch({
        headless: 'new',
        // In production, https://fogos.pt is served by nginx with a
        // Cloudflare Origin CA certificate — trusted by the CF edge, but
        // not by public root stores. The sidecar navigates there via a
        // host-gateway mapping (see docker-compose.yml extra_hosts) so
        // the request never leaves the box, and it's already gated by an
        // HMAC token. Chrome refusing on cert grounds is pure friction.
        acceptInsecureCerts: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--font-render-hinting=medium',
            '--ignore-certificate-errors',
        ],
    }).then((b) => {
        // If Chromium ever dies (OOM, segfault, docker kill), forget it so
        // the next request rebuilds. Without this the memoised promise
        // hands out a stale, disconnected Browser and every subsequent
        // newPage() throws with a confusing "Protocol error" trace.
        b.on('disconnected', () => {
            app.log.warn('browser disconnected — will relaunch on next request');
            browserPromise = null;
        });
        return b;
    }).catch((err) => {
        browserPromise = null;
        throw err;
    });
    return browserPromise;
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

app.get('/health', async () => ({ ok: true, activeRenders, queued: waiters.length }));

async function renderPngInner(log, { url, w, h, token }) {
    let page;
    try {
        const browser = await getBrowser();
        page = await browser.newPage();
        await page.setViewport({ width: w, height: h, deviceScaleFactor: 1 });

        if (token && typeof token === 'string') {
            await page.setExtraHTTPHeaders({ 'X-Og-Token': token });
        }

        // `domcontentloaded` instead of `networkidle0`: the Leaflet map
        // keeps requesting tiles (and fitBounds triggers another wave)
        // long after the useful pixels are painted, so networkidle0 could
        // stretch past 20s or never fire. The Blade sets window.__ogReady
        // once tiles have loaded (via `on('load')`) with a 2.5s belt —
        // that's the authoritative "safe to screenshot" signal.
        const resp = await page.goto(url, {
            waitUntil: 'domcontentloaded',
            timeout: 6000,
        });
        if (!resp || !resp.ok()) {
            const status = resp ? resp.status() : 0;
            const err = new Error(`origin returned ${status}`);
            err.statusCode = 502;
            throw err;
        }

        // Wait for the Blade to declare tiles/KML settled. Falls through
        // on timeout — a card without perfect tile coverage still beats
        // no card at all in a share preview.
        try {
            await page.waitForFunction(READY_FLAG, { timeout: 4000 });
        } catch (e) {
            log.warn({ err: e.message }, 'ogReady flag not set within 4s, capturing anyway');
        }

        return await page.screenshot({ type: 'png', clip: { x: 0, y: 0, width: w, height: h } });
    } finally {
        if (page) await page.close().catch(() => {});
    }
}

// Wrap the actual render in a hard cap so a hung page (crashed tab, dead
// browser socket, tile CDN blackhole) can never pin an inFlight entry
// forever. On timeout we also kill the browser — if a page hung this
// long the whole Chromium instance is probably wedged.
function renderPng(log, opts) {
    let timeoutId;
    return Promise.race([
        renderPngInner(log, opts).then((v) => { clearTimeout(timeoutId); return v; }),
        new Promise((_, reject) => {
            timeoutId = setTimeout(async () => {
                log.error(`hard render cap ${HARD_RENDER_CAP_MS}ms exceeded — killing browser`);
                try {
                    const b = browserPromise ? await browserPromise : null;
                    if (b) await b.close().catch(() => {});
                } catch (e) { /* noop */ }
                browserPromise = null;
                reject(new Error(`hard render cap ${HARD_RENDER_CAP_MS}ms exceeded`));
            }, HARD_RENDER_CAP_MS);
        }),
    ]);
}

// Coalesce concurrent renders of the same URL. When many crawlers hit
// /og/fogo/{id}.png at once and every request misses the PHP disk cache,
// each one would spawn a Chrome page — 200 MB × N + CPU. Keyed by url+size
// so two clients asking for the same card wait on the same rendering
// promise. Cleared on completion (success or failure).
const inFlight = new Map();

app.post('/render', async (req, reply) => {
    const { url, width, height, token } = req.body || {};
    if (!url || typeof url !== 'string') {
        return reply.code(400).send({ error: 'url is required' });
    }

    const w = Number(width)  || 1200;
    const h = Number(height) ||  630;
    const key = `${url}|${w}x${h}`;

    let promise = inFlight.get(key);
    if (!promise) {
        promise = (async () => {
            await acquireSlot();
            try {
                return await renderPng(req.log, { url, w, h, token });
            } finally {
                releaseSlot();
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
