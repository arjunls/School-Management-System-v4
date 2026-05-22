import { test, expect } from '@playwright/test'
import { loginAs } from './helpers'

test.describe('Authentication Flow', () => {
  test('login page loads and shows form', async ({ page }) => {
    await page.goto('/login')
    await expect(page.locator('h2')).toContainText('Sign In')
    await expect(page.locator('input[type="email"]')).toBeVisible()
    await expect(page.locator('input[type="password"]')).toBeVisible()
    await expect(page.locator('button[type="submit"]')).toContainText('Sign In')
  })

  test('shows error on invalid credentials', async ({ page }) => {
    await page.goto('/login')
    await page.fill('input[type="email"]', 'wrong@example.com')
    await page.fill('input[type="password"]', 'wrongpass')
    await page.click('button[type="submit"]')

    await expect(page.locator('text=Invalid credentials')).toBeVisible({ timeout: 10000 })
  })

  test('successful login redirects to dashboard', async ({ page }) => {
    await loginAs(page, 'admin@school.com', 'password')
    await expect(page).toHaveURL(/\/dashboard/, { timeout: 10000 })
  })

  test('protected routes redirect to login when unauthenticated', async ({ page }) => {
    await page.goto('/dashboard')
    await expect(page).toHaveURL(/\/login/)
  })

  test('logout redirects to login page', async ({ page }) => {
    await loginAs(page, 'admin@school.com', 'password')
    await expect(page).toHaveURL(/\/dashboard/, { timeout: 10000 })

    await page.click('text=Sign Out')
    await expect(page).toHaveURL(/\/login/, { timeout: 10000 })
  })
})
