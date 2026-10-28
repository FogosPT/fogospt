const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
    testDir: '.',
    timeout: 60000,
    retries: 0,
    workers: 4,
    outputDir: '../../test-results/smoke',
    use: {
        baseURL: process.env.SMOKE_URL || 'http://127.0.0.1:8091',
        headless: true,
        viewport: { width: 1366, height: 800 },
    },
});
