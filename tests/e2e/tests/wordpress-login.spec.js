
const { test, expect } = require('@playwright/test');

test.describe('WordPress Smoke Tests', () => {
  test('@smoke WordPress login page should load', async ({ page }) => {
    await page.goto('/wp-login.php');

    await expect(page.locator('#user_login')).toBeVisible();
    await expect(page.locator('#user_pass')).toBeVisible();
    await expect(page.locator('#wp-submit')).toBeVisible();
  });
});
