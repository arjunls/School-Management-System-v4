import { FullConfig } from '@playwright/test'

async function globalSetup(_config: FullConfig) {
  const { execSync } = await import('child_process')
  const dbPath = '../backend/database/e2e.sqlite'

  // Remove old E2E database if exists
  try {
    const { unlinkSync } = await import('fs')
    unlinkSync(dbPath)
  } catch { /* ignore if not exists */ }

  const env = { ...process.env, APP_ENV: 'e2e', DB_CONNECTION: 'sqlite', DB_DATABASE: dbPath }
  execSync('php artisan migrate --force', { cwd: '../backend', stdio: 'inherit', env })
  execSync('php artisan db:seed --force', { cwd: '../backend', stdio: 'inherit', env })
}

export default globalSetup
