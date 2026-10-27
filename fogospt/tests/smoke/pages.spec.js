// Browser smoke test: every page loads without JS errors, and the main map
// can open a fire in the sidebar. Run against a live server:
//   SMOKE_URL=http://127.0.0.1:8091 npx playwright test -c tests/smoke
const { test, expect } = require('@playwright/test');

const PAGES = [
    '/pt', '/pt/madeira', '/pt/outros', '/pt/mapa', '/pt/mapa/configurar',
    '/pt/tabela', '/pt/avisos', '/pt/madeira/avisos', '/pt/estatisticas',
    '/pt/notificacoes', '/pt/parceiros', '/pt/sobre', '/pt/informacoes',
    '/pt/api', '/pt/api-termos', '/pt/privacy-policy', '/en', '/es/sobre',
];

// Noise from third parties we don't control (blocked trackers, tiles, FCM
// permission prompts in headless Chrome).
const IGNORED = [
    /Failed to load resource/i,
    /messaging\/(permission|unsupported|failed-service-worker)/i,
    /net::ERR_/i,
    /tile\.openstreetmap|arcgisonline|mapbox\.com/i,
];

// source.fogos.pt only allows the fogos.pt origin. Locally, fetch those
// requests from the test runner (no CORS) and answer with permissive headers.
const CORS = {
    'access-control-allow-origin': '*',
    'access-control-allow-headers': '*',
    'access-control-allow-methods': 'GET, OPTIONS',
};

test.beforeEach(async ({ page }) => {
    await page.route(/^https:\/\/source\.fogos\.pt\//, async (route) => {
        if (route.request().method() === 'OPTIONS') {
            return route.fulfill({ status: 204, headers: CORS });
        }
        try {
            const resp = await route.fetch({ headers: { ...route.request().headers(), origin: 'https://fogos.pt', referer: 'https://fogos.pt/' } });
            await route.fulfill({ response: resp, headers: { ...resp.headers(), ...CORS } });
        } catch (e) {
            await route.abort();
        }
    });
});

function collectErrors(page) {
    const errors = [];
    page.on('pageerror', (e) => errors.push('pageerror: ' + e.message));
    page.on('console', (m) => {
        if (m.type() !== 'error') return;
        const text = m.text();
        if (!IGNORED.some((re) => re.test(text))) errors.push('console: ' + text);
    });
    return errors;
}

for (const path of PAGES) {
    test(`loads ${path} without JS errors`, async ({ page }, info) => {
        const errors = collectErrors(page);
        const resp = await page.goto(path, { waitUntil: 'domcontentloaded' });
        expect(resp.status()).toBe(200);
        await page.waitForLoadState('networkidle', { timeout: 15000 }).catch(() => {});
        await page.waitForTimeout(1500);
        await page.screenshot({ path: info.outputPath('page.png'), fullPage: false });
        expect(errors).toEqual([]);
    });
}

test('main map opens a fire in the sidebar', async ({ page }) => {
    const errors = collectErrors(page);
    await page.goto('/pt', { waitUntil: 'domcontentloaded' });
    const marker = page.locator('.leaflet-marker-icon i.dot').first();
    const hasFires = await marker.waitFor({ timeout: 20000 }).then(() => true, () => false);
    test.skip(!hasFires, 'no active fires right now');
    // Markers can sit under the cookie banner or off-screen: dismiss the
    // banner and dispatch the click on the icon itself.
    await page.getByRole('button', { name: /rejeitar tudo/i }).click({ timeout: 3000 }).catch(() => {});
    await marker.evaluate((el) => el.closest('.leaflet-marker-icon').dispatchEvent(new MouseEvent('click', { bubbles: true })));
    await expect(page.locator('.sidebar.active')).toBeVisible({ timeout: 10000 });
    await expect(page.locator('.f-local')).not.toBeEmpty();
    await page.waitForTimeout(2000);
    await page.screenshot({ path: test.info().outputPath('sidebar.png') });
    expect(errors).toEqual([]);
});

// Needs a server whose upstream knows this fire id (e.g. a mocked
// source.fogos.pt); skipped otherwise.
test('fire detail page renders charts and sanitises KML popups', async ({ page }) => {
    const id = process.env.SMOKE_FIRE_ID;
    test.skip(!id, 'SMOKE_FIRE_ID not set');
    const errors = collectErrors(page);
    const dialogs = [];
    page.on('dialog', (d) => { dialogs.push(d.message()); d.dismiss(); });

    const resp = await page.goto(`/pt/fogo/${id}/detalhe`, { waitUntil: 'domcontentloaded' });
    expect(resp.status()).toBe(200);
    await page.waitForLoadState('networkidle', { timeout: 20000 }).catch(() => {});
    await page.getByRole('button', { name: /rejeitar tudo/i }).click({ timeout: 3000 }).catch(() => {});

    // Open the KML popup (if the fire has a perimeter) and check it's inert.
    const kml = page.locator('#mymap path.leaflet-interactive').first();
    if (await kml.count()) {
        await kml.evaluate((el) => el.dispatchEvent(new MouseEvent('click', { bubbles: true, clientX: 10, clientY: 10 })));
        const popup = page.locator('.kml-popup');
        await popup.waitFor({ timeout: 5000 }).catch(() => {});
        if (await popup.count()) {
            // Escaped text may mention "onerror"; what matters is the DOM.
            const unsafe = await popup.evaluate((el) => {
                const found = [];
                el.querySelectorAll('*').forEach((n) => {
                    if (n.tagName === 'SCRIPT') found.push('script');
                    for (const a of n.attributes) {
                        if (/^on/i.test(a.name)) found.push(a.name);
                        if (/^\s*javascript:/i.test(a.value)) found.push(a.name + '=javascript:');
                    }
                });
                return found;
            });
            expect(unsafe).toEqual([]);
        }
    }

    await page.waitForTimeout(2000);
    await page.screenshot({ path: test.info().outputPath('detail.png'), fullPage: true });
    expect(dialogs).toEqual([]);
    expect(errors).toEqual([]);
});

test('risk layer lazy-loads concelhos and draws the choropleth', async ({ page }) => {
    const errors = collectErrors(page);
    const concelhos = page.waitForRequest(/\/js\/concelhos\.js/, { timeout: 20000 });
    await page.goto('/pt?risk=1', { waitUntil: 'domcontentloaded' });
    await concelhos;
    // Drawing needs live risk values from source.fogos.pt, which CI runners
    // can't always reach — only assert the choropleth when it answers.
    const upstream = await page.request.get('https://source.fogos.pt/v1/risk-today', {
        headers: { origin: 'https://fogos.pt', referer: 'https://fogos.pt/' }, timeout: 10000,
    }).then((r) => r.ok(), () => false);
    test.skip(!upstream, 'source.fogos.pt unreachable from this runner');
    await expect(page.locator('#map.risk-layer-ready')).toHaveCount(1, { timeout: 20000 });
    expect(await page.locator('#map path.leaflet-interactive').count()).toBeGreaterThan(200);
    expect(errors).toEqual([]);
});

test('main map does not download concelhos until the risk layer is used', async ({ page }) => {
    const urls = [];
    page.on('request', (r) => urls.push(r.url()));
    await page.goto('/pt', { waitUntil: 'networkidle' }).catch(() => {});
    expect(urls.filter((u) => /\/js\/concelhos\.js/.test(u))).toEqual([]);
});
