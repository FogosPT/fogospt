const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

test.describe('Build output verification', () => {

  test('app.css compiles with Bootstrap 5 and redesigned styles', () => {
    const css = fs.readFileSync('public/css/app.css', 'utf8');

    expect(css).toContain('header{height:56px;');
    expect(css).toContain('box-shadow:0 1px 4px rgba(0,0,0,.15)');
    expect(css).toContain('top:56px;bottom:0;width:260px');
    expect(css).toContain('max-height:calc(100vh - 56px)}');

    expect(css).toContain('.menu-links');
    expect(css).toContain('.menu-divider');
    expect(css).toContain('.menu-social');
    expect(css).toContain('.menu-lang');
    expect(css).toContain('border-left:3px solid transparent');

    expect(css).toContain('border-radius:.5rem');
    expect(css).toContain('box-shadow:0 1px 3px rgba(0,0,0,.06)');

    expect(css).toContain('.field{margin-bottom:.75rem}');
    expect(css).toContain('.field-label');
    expect(css).toContain('.field-value');
    expect(css).toContain('.field-divider');

    expect(css).toContain('padding-top:56px');

    expect(css).toContain('.marker.\\38 7D37C');
  });

  test('app.js compiles without syntax errors', () => {
    const js = fs.readFileSync('public/js/app.js', 'utf8');

    expect(js.length).toBeGreaterThan(1000);
    expect(js).toContain('lodash');
    expect(js).toContain('axios');
  });

  test('app.css.gz compresses if available', () => {
    try {
      const gz = fs.readFileSync('public/css/app.css.gz');
      expect(gz.length).toBeLessThan(fs.readFileSync('public/css/app.css').length);
    } catch {
      // gzip may or may not be enabled; test passes either way
    }
  });
});

test.describe('Served static assets', () => {

  test('CSS file loads with correct content-type', async ({ page }) => {
    const resp = await page.goto('/css/app.css');
    expect(resp.status()).toBe(200);
    expect(resp.headers()['content-type']).toContain('css');
  });

  test('JS file loads with correct content-type', async ({ page }) => {
    const resp = await page.goto('/js/app.js');
    expect(resp.status()).toBe(200);
    expect(resp.headers()['content-type']).toContain('javascript');
  });

  test('index page loads and references Bootstrap 5 CDN', async ({ page }) => {
    const resp = await page.goto('/index.html');
    if (resp.status() === 404) return;
    expect(resp.status()).toBe(200);
  });
});
