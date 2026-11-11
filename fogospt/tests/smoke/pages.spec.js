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

// Madeira (SRPC, IP-RAM via the Fogos.pt API v2) with fixed data: one marker
// per parish with coordinates, the rest only listed, upstream text escaped,
// event panel shown when there is an event.
const MADEIRA_OCCURRENCES = [
    { id: 'a1', copies: 1, start: '2026-11-11T11:59:00Z', natureCode: '3311', nature: 'Queda de elementos de construção', firefighters: 2, vehicles: 1, parishCode: '310307', parish: 'São Martinho', municipalityCode: '3103', municipality: 'Funchal', lat: 32.6468, lng: -16.94496, location: 'parish', entities: ['CMOS'], updatedAt: '2026-11-11T12:03:49Z' },
    { id: 'a2', copies: 1, start: '2026-11-11T11:56:00Z', natureCode: '3301', nature: '<img src=x onerror="window.__xss=1">', firefighters: 2, vehicles: 1, parishCode: '310307', parish: 'São Martinho', municipalityCode: '3103', municipality: 'Funchal', lat: 32.6468, lng: -16.94496, location: 'parish', entities: [], updatedAt: '2026-11-11T11:59:52Z' },
    { id: 'b1', copies: 2, start: '2026-11-11T10:00:00Z', natureCode: '3313', nature: 'Movimento de massa', firefighters: 7, vehicles: 2, parishCode: '310803', parish: 'Caniço', municipalityCode: '3108', municipality: 'Santa Cruz', lat: 32.65485, lng: -16.8389, location: 'parish', entities: ['BMSC'], updatedAt: '2026-11-11T10:20:00Z' },
    { id: 'c1', copies: 1, start: '2026-11-11T09:00:00Z', natureCode: '3315', nature: 'Inundação', firefighters: 3, vehicles: 1, parishCode: '310403', parish: 'Machico', municipalityCode: '3104', municipality: 'Machico', entities: [], updatedAt: '2026-11-11T09:30:00Z' },
    { id: 'd1', copies: 1, start: '2026-11-11T12:09:00Z', natureCode: '3313', nature: 'Movimento de massa', firefighters: 0, vehicles: 0, parishCode: null, parish: null, municipalityCode: null, municipality: null, lat: null, lng: null, location: null, entities: [], updatedAt: '2026-11-11T12:12:51Z' },
];
const MADEIRA_EVENT = {
    id: 'e1', title: 'Depressão Teste', zone: 'Madeira e Porto Santo', status: 'ATIVO', active: true,
    start: '2026-11-10T20:00:00Z', end: '2026-11-11T12:15:00Z', untilNow: true, updatedAt: '2026-11-11T12:15:00Z',
    kpi: { occurrences: 265, activeOccurrences: 20, operationals: 668, groundAssets: 274, municipalities: 12, peak: { time: '2026-11-11T06:00:00Z', occurrences: 36 } },
    summary: 'Resumo de teste.', recommendations: ['Evite deslocações desnecessárias.'],
    municipalities: [{ name: 'Funchal', occurrences: 65, operationals: 150, groundAssets: 60, natures: [] }, { name: 'Santa Cruz', occurrences: 63, operationals: 140, groundAssets: 55, natures: [] }],
    natures: [], hourly: [],
    roadRestrictions: [{ road: 'ER101', closed: true, text: 'Derrocada', time: '2026-11-11T08:00:00Z' }, { road: 'VR1', closed: false, text: 'Uma via', time: null }],
    communiques: [{ title: 'Aviso oficial', time: '2026-11-10T15:27:00Z', text: 'Texto.', link: 'https://www.procivmadeira.pt/x.html', official: true }, { title: 'Nota', time: null, text: null, link: 'javascript:alert(1)', official: false }],
    parishes: [], entities: [], topOccurrences: [], notes: 'Dados provisórios.', source: 'SRPC-RAM',
};

async function routeMadeira(page, event) {
    const json = (body) => ({ status: 200, headers: { 'content-type': 'application/json', ...CORS }, body: JSON.stringify(body) });
    await page.route(/^https:\/\/source\.fogos\.pt\/v2\/madeira\/occurrences/, (route) =>
        route.request().method() === 'OPTIONS' ? route.fulfill({ status: 204, headers: CORS }) : route.fulfill(json({ success: true, data: MADEIRA_OCCURRENCES })));
    await page.route(/^https:\/\/source\.fogos\.pt\/v2\/madeira\/events\/current/, (route) =>
        route.request().method() === 'OPTIONS' ? route.fulfill({ status: 204, headers: CORS }) : route.fulfill(json({ success: true, data: event })));
}

test('madeira map shows SRPC occurrences per parish and the event panel', async ({ page }) => {
    const errors = collectErrors(page);
    await routeMadeira(page, MADEIRA_EVENT);
    await page.goto('/pt/madeira', { waitUntil: 'domcontentloaded' });

    const markers = page.locator('.madeira-occ-icon');
    await expect(markers).toHaveCount(2);
    await expect(page.locator('.madeira-occ-icon__count')).toHaveText(['2', '2']);
    await expect(page.locator('#madeira-occ-meta')).toHaveText('6 ocorrências em 3 freguesias nas últimas 24 horas · 2 sem localização no mapa');
    await expect(page.locator('.madeira-parishes summary')).toHaveCount(4);
    await expect(page.locator('.madeira-parishes summary').last()).toContainText('Sem freguesia');

    await expect(page.locator('#madeira-event')).toBeVisible();
    await expect(page.locator('.madeira-kpi__value')).toHaveText(['265', '20', '668', '274', '12']);
    await expect(page.locator('.madeira-roads__label')).toHaveText(['Cortadas (1)', 'Condicionadas (1)']);
    await expect(page.locator('.madeira-communiques a')).toHaveCount(1);

    await page.getByRole('button', { name: /rejeitar tudo/i }).click({ timeout: 3000 }).catch(() => {});
    await page.locator('.madeira-parishes summary', { hasText: 'São Martinho' }).locator('.madeira-parishes__locate').click();
    const popup = page.locator('.madeira-popup');
    await expect(popup).toBeVisible();
    await expect(popup).toContainText('São Martinho');
    await expect(popup).toContainText('Localização aproximada');
    await expect(popup).toContainText('<img src=x');
    expect(await page.evaluate(() => window.__xss)).toBeUndefined();

    await page.screenshot({ path: test.info().outputPath('madeira.png') });
    expect(errors).toEqual([]);
});

test('madeira event panel stays hidden without an event', async ({ page }) => {
    const errors = collectErrors(page);
    await routeMadeira(page, null);
    await page.goto('/pt/madeira', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('.madeira-occ-icon')).toHaveCount(2);
    await expect(page.locator('#madeira-event')).toBeHidden();
    expect(errors).toEqual([]);
});
