import { defineConfig } from '@playwright/test'

export default defineConfig({
  testDir: './e2e',
  timeout: 30000,
  retries: 1,
  globalSetup: './e2e/global-setup.ts',
  use: {
    baseURL: 'http://localhost:3000',
    headless: true,
    screenshot: 'only-on-failure',
  },
  webServer: [
    {
      command: 'php artisan serve --port=8000 --env=e2e',
      cwd: '../backend',
      port: 8000,
      reuseExistingServer: true,
      timeout: 30000,
      env: { APP_ENV: 'e2e', DB_CONNECTION: 'sqlite', DB_DATABASE: ':memory:' },
    },
    {
      command: 'npm run dev -- --port=3000',
      port: 3000,
      reuseExistingServer: true,
      timeout: 30000,
    },
  ],
})
