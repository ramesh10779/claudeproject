const { test, expect } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

const fixturePath = path.join(__dirname, '../tests/fixtures/sample.xml');

test.describe('XML import renders keys', () => {
  test('shows more than four keys and expected names', async ({ page }) => {
    await page.goto('/');

    // Upload the XML via the file input to exercise real UI wiring
    await page.setInputFiles('#openXmlFileInput', fixturePath);
    // Wait for status to indicate load completed
    await expect(page.locator('#statusText')).toContainText(/Loaded XML|XML loaded/i);

    // Assert more than four rows in active environment
    const rows = page.locator('#pairsTbody tr');
    const rowCount = await rows.count();
    expect(rowCount).toBeGreaterThan(4);

    // Expect specific keys derived from scope-value/name
    await expect(page.getByRole('textbox', { name: 'Key' })).toBeVisible();
    const bodyText = await page.locator('body').innerText();
    expect(bodyText).toContain('AvailableServicesClass');
    expect(bodyText).toContain('ObjectNameMaker');

    // Switch to a managed instance environment and verify keys exist
    const envBadge = page.locator('.env-badge .name', { hasText: 'EnvironmentCheck' });
    if (await envBadge.count()) {
      await envBadge.first().click();
      const miRows = await page.locator('#pairsTbody tr').count();
      expect(miRows).toBeGreaterThan(0);
    }
  });
});

test('parser returns keys from fixture (sanity)', async ({ page }) => {
  await page.goto('/');
  const xml = fs.readFileSync(fixturePath, 'utf-8');
  const result = await page.evaluate((xmlText) => {
    return window.parseGeneratedXML(xmlText);
  }, xml);
  // Ensure dovetailManagement and managedInstance environments have keys
  expect(Object.keys(result.envs['dovetailManagement'] || {}).length).toBeGreaterThan(2);
  expect(Object.keys(result.envs['EnvironmentCheck'] || {}).length).toBeGreaterThan(0);
});
