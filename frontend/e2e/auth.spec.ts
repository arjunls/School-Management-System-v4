import { test, expect } from '@playwright/test'
import { loginAs } from './helpers'

test.describe('Authentication Flow', () => {
  test('login page loads and shows form', async ({ page }) => {
    await page.goto('/login')
    await expect(page.locator('input[type="email"]')).toBeVisible({ timeout: 10000 })
    await expect(page.locator('input[type="password"]')).toBeVisible()
    await expect(page.locator('button[type="submit"]')).toContainText('Masuk')
  })

  test('shows error on invalid credentials', async ({ page }) => {
    await page.goto('/login')
    await page.fill('input[type="email"]', 'wrong@example.com')
    const passwordField = page.locator('input[type="password"]')
    await passwordField.fill('wrongpass')
    await page.click('button[type="submit"]')

    await expect(page.locator('text=Email atau password salah')).toBeVisible({ timeout: 10000 })
  })

  test('successful login redirects to dashboard', async ({ page }) => {
    await loginAs(page, 'admin@school.com', 'password')
    await expect(page).toHaveURL(/\/dashboard/, { timeout: 15000 })
  })

  test('protected routes redirect to login when unauthenticated', async ({ page }) => {
    await page.goto('/dashboard')
    await expect(page).toHaveURL(/\/login/)
  })

  test('logout redirects to login page', async ({ page }) => {
    await loginAs(page, 'admin@school.com', 'password')
    await expect(page).toHaveURL(/\/dashboard/, { timeout: 15000 })

    await page.click('text=Keluar')
    await expect(page).toHaveURL(/\/login/, { timeout: 15000 })
  })
})
