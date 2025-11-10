/** @type {import('@playwright/test').PlaywrightTestConfig} */
const config = {
  testDir: 'tests',
  use: {
    baseURL: 'http://localhost:8000',
    headless: true,
  },
  timeout: 30000,
};

module.exports = config;
