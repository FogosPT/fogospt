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

async function getBrowser() {
    if (browserPromise) return browserPromise;
    browserPromise = puppeteer.launch({
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--font-render-hinting=medium',
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

app.post('/render', async (req, reply) => {
    const { url, width, height, token } = req.body || {};
    if (!url || typeof url !== 'string') {
        return reply.code(400).send({ error: 'url is required' });
    }

    const w = Number(width)  || 1200;
    const h = Number(height) ||  630;

    let page;
    try {
        const browser = await getBrowser();
        page = await browser.newPage();
        await page.setViewport({ width: w, height: h, deviceScaleFactor: 1 });

        // The rendered Blade route is gated by the OgInternal middleware,
        // which checks either loopback IP or a matching HMAC token in
        // X-Og-Token. Forward the token so the sidecar can reach the app.
        if (token && typeof token === 'string') {
            await page.setExtraHTTPHeaders({ 'X-Og-Token': token });
        }

        const resp = await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: RENDER_TIMEOUT_MS,
        });
        if (!resp || !resp.ok()) {
            const status = resp ? resp.status() : 0;
            return reply.code(502).send({ error: `origin returned ${status}` });
        }

        // Wait a bit longer for the Blade to declare it settled tiles/KML.
        // Fall through if the flag never appears — the map may fail to load
        // remote tiles under network pressure and we still want *something*.
        try {
            await page.waitForFunction(READY_FLAG, { timeout: 4000 });
        } catch (e) {
            req.log.warn({ err: e.message }, 'ogReady flag not set within 4s, capturing anyway');
        }

        const png = await page.screenshot({ type: 'png', clip: { x: 0, y: 0, width: w, height: h } });
        reply.header('Content-Type', 'image/png');
        return reply.send(png);
    } catch (err) {
        req.log.error({ err: err.message }, 'render failed');
        return reply.code(500).send({ error: err.message });
    } finally {
        if (page) await page.close().catch(() => {});
    }
});

app.listen({ port: PORT, host: HOST }).catch((err) => {
    app.log.error(err);
    process.exit(1);
});
