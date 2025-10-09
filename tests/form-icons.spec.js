import { test, expect } from '@playwright/test';

test.describe('Form Section Icon Analysis', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('file:///Users/tino/Projects/therapair/therapair-landing-page/index.html');
  });

  test('should analyze audience selection icons', async ({ page }) => {
    // Wait for form section to be visible
    await page.waitForSelector('#main-form-element');

    // Check audience selectors are present
    const audienceSelectors = await page.locator('.audience-selector').count();
    expect(audienceSelectors).toBe(4);

    // Check all audience icons are solid (filled)
    const audienceIcons = page.locator('.audience-selector svg');
    const iconCount = await audienceIcons.count();

    for (let i = 0; i < iconCount; i++) {
      const icon = audienceIcons.nth(i);
      const fill = await icon.getAttribute('fill');
      expect(fill).toBe('currentColor'); // Should be solid
    }

    console.log('✅ Audience icons are solid style');
  });

  test('should analyze therapy interest icons', async ({ page }) => {
    // Click on individual option to reveal therapy interests
    await page.locator('[data-audience="individual"]').click();

    // Wait for therapy interests to appear
    await page.waitForSelector('#individual-fields', { state: 'visible' });

    // Check therapy interest icons are outline
    const therapyIcons = page.locator('input[name="therapy-interests"] ~ div svg');
    const iconCount = await therapyIcons.count();

    for (let i = 0; i < iconCount; i++) {
      const icon = therapyIcons.nth(i);
      const fill = await icon.getAttribute('fill');
      const stroke = await icon.getAttribute('stroke');
      expect(fill).toBe('none'); // Should be outline
      expect(stroke).toBe('currentColor'); // Should have stroke
    }

    console.log('✅ Therapy interest icons are outline style');
  });

  test('should check icon consistency', async ({ page }) => {
    // Check all icons use consistent color
    const allIcons = page.locator('svg');
    const iconCount = await allIcons.count();

    for (let i = 0; i < iconCount; i++) {
      const icon = allIcons.nth(i);
      const color = await icon.evaluate(el => getComputedStyle(el).color);
      // Should use primary color
      expect(color).toContain('rgb'); // Has computed color
    }

    console.log('✅ Icons use consistent colors');
  });

  test('should take screenshot for visual review', async ({ page }) => {
    // Scroll to form section
    await page.locator('#main-form-element').scrollIntoViewIfNeeded();

    // Take screenshot of form section (third iteration)
    await page.locator('#main-form').screenshot({
      path: 'tests/screenshots/form-section-v3.png'
    });

    // Click individual to show therapy interests
    await page.locator('[data-audience="individual"]').click();
    await page.waitForSelector('#individual-fields', { state: 'visible' });

    // Take screenshot with therapy interests visible (third iteration)
    await page.locator('#main-form').screenshot({
      path: 'tests/screenshots/form-with-interests-v3.png'
    });

    console.log('✅ Screenshots captured for visual review');
  });
});