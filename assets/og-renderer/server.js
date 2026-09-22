// Tiny Puppeteer-over-HTTP sidecar for the social share card renderer.
// One Chromium is launched at boot and reused across requests — a fresh
// browser per hit would add ~700ms of cold-start on every card miss.
const Fastify = require('fastify');
const puppeteer = require('puppeteer');

const PORT = Number(process.env.PORT || 3000);
const HOST = process.env.HOST || '0.0.0.0';
const RENDER_TIMEOUT_MS = Number(process.env.RENDER_TIMEOUT_MS || 10000);
const READY_FLAG = 'window.__ogReady === true';

const app = Fastify({ logger: true });
let browserPromise = null;

// Coalesce concurrent renders of the same URL. When many crawlers hit
// /og/fogo/{id}.png at once and every request misses the PHP disk cache,
// each one spawns a Chrome page — 300 MB × N + CPU. Keyed by url+size so
// two clients asking for the same card wait on the same rendering
// promise. Cleared on completion (success or failure).
const inFlight = new Map();

async function getBrowser() {
    if (browserPromise) return browserPromise;
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
    }).catch((err) => {
        // If launch fails, clear the memoised promise so the next request
        // retries instead of returning the same rejection forever.
        browserPromise = null;
        throw err;
    });
    return browserPromise;
}

app.get('/health', async () => ({ ok: true }));

const HARD_RENDER_CAP_MS = Number(process.env.HARD_RENDER_CAP_MS || 15000);

async function renderPngInner(log, { url, w, h, token }) {
    let page;
    try {
        const browser = await getBrowser();
        page = await browser.newPage();
        await page.setViewport({ width: w, height: h, deviceScaleFactor: 1 });

        // The rendered Blade route is gated by the OgInternal middleware,
        // which checks a matching HMAC token in X-Og-Token. Forward it so
        // the sidecar can reach the app.
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
// forever. Rejection clears the map and the next request retries fresh.
function renderPng(log, opts) {
    return Promise.race([
        renderPngInner(log, opts),
        new Promise((_, reject) => setTimeout(
            () => reject(new Error(`hard render cap ${HARD_RENDER_CAP_MS}ms exceeded`)),
            HARD_RENDER_CAP_MS
        )),
    ]);
}

app.post('/render', async (req, reply) => {
    const { url, width, height, token } = req.body || {};
    if (!url || typeof url !== 'string') {
        return reply.code(400).send({ error: 'url is required' });
    }

    const w = Number(width)  || 1200;
    const h = Number(height) ||  630;
    const key = `${url}|${w}x${h}`;

    // Coalesce identical concurrent requests onto one render.
    let promise = inFlight.get(key);
    if (!promise) {
        promise = renderPng(req.log, { url, w, h, token })
            .finally(() => inFlight.delete(key));
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
