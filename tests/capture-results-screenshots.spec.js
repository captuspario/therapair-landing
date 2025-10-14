import { test } from '@playwright/test';

test.describe('Capture Clean Therapair Screenshots', () => {
  test('capture clean quiz question iframe content only', async ({ page }) => {
    // Navigate to the Unison Mental Health widget
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    
    // Wait for page to load
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(3000); // Wait for iframe to load

    console.log('🔍 Looking for Therapair iframe...');
    
    // Find the Therapair iframe (Typebot widget)
    const frames = page.frames();
    let therapairFrame = null;
    
    for (const frame of frames) {
      const frameUrl = frame.url();
      console.log(`Frame URL: ${frameUrl}`);
      
      // Look for typebot or therapair in the frame URL/domain
      if (frameUrl.includes('typebot') || frameUrl.includes('therapair') || frameUrl.includes('bubble')) {
        therapairFrame = frame;
        console.log('✅ Found Therapair frame!');
        break;
      }
    }

    if (therapairFrame) {
      // Wait for content to load in iframe
      await therapairFrame.waitForLoadState('networkidle');
      await page.waitForTimeout(2000);

      // Find the main content container within the iframe
      const contentContainer = therapairFrame.locator('.typebot-container, [class*="bubble"], [class*="container"]').first();
      
      if (await contentContainer.isVisible()) {
        console.log('📸 Capturing clean quiz question from iframe...');
        
        // Capture just the iframe content
        await contentContainer.screenshot({
          path: 'images/therapair-quiz-question.png',
          padding: 10 // Small padding for clean edges
        });
        
        console.log('✅ Quiz question captured from iframe!');
      } else {
        console.log('⚠️  Content container not found, capturing entire iframe');
        // Fallback: capture entire iframe
        const iframeElement = page.locator('iframe').first();
        await iframeElement.screenshot({
          path: 'images/therapair-quiz-question.png'
        });
      }
    } else {
      console.log('⚠️  No Therapair iframe found, capturing page element');
      // Fallback: look for the widget container on the page
      const widgetContainer = page.locator('[class*="typebot"], [class*="widget"], [class*="bubble"]').first();
      if (await widgetContainer.isVisible()) {
        await widgetContainer.screenshot({
          path: 'images/therapair-quiz-question.png',
          padding: 10
        });
      }
    }
  });

  test('capture clean results cards from iframe', async ({ page }) => {
    // Navigate to the Unison Mental Health widget
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    
    // Wait for page to load
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(3000);

    console.log('🎯 Navigate through quiz manually to reach results...');
    console.log('📝 Instructions:');
    console.log('   1. Answer the quiz questions');
    console.log('   2. Reach the results page with 3 therapist cards');
    console.log('   3. Use Playwright Inspector to capture iframe content');
    
    // Find the Therapair iframe
    const frames = page.frames();
    let therapairFrame = null;
    
    for (const frame of frames) {
      const frameUrl = frame.url();
      if (frameUrl.includes('typebot') || frameUrl.includes('therapair') || frameUrl.includes('bubble')) {
        therapairFrame = frame;
        console.log('✅ Found Therapair frame!');
        break;
      }
    }

    if (therapairFrame) {
      // Inject CSS to reduce white space between skills and buttons
      await therapairFrame.addStyleTag({
        content: `
          /* Reduce spacing between skills pills and book now button */
          [class*="skill"], [class*="pill"], [class*="tag"] {
            margin-bottom: 0.5rem !important;
          }
          [class*="button"], [class*="cta"], button {
            margin-top: 1rem !important;
            padding-top: 0.5rem !important;
          }
          /* Compact card layout */
          [class*="card"] {
            margin-bottom: 1rem !important;
          }
          [class*="card"] > * {
            margin-bottom: 0.75rem !important;
          }
        `
      });
      console.log('✅ CSS injected to reduce white space');
    }

    // Pause for manual navigation
    console.log('⏸️  Pausing for manual navigation...');
    console.log('💡 Once you reach results with 3 cards, run:');
    console.log('   await therapairFrame.locator(".typebot-container").screenshot({ path: "images/therapair-results-3-cards.png" })');
    
    await page.pause();
  });

  test('capture results with adjusted viewport for full card visibility', async ({ page, browser }) => {
    // Open a new context with specific viewport
    const context = await browser.newContext({
      viewport: { width: 1600, height: 2000 }, // Taller viewport to show full cards
      deviceScaleFactor: 1
    });

    const newPage = await context.newPage();
    
    // Navigate to Unison widget
    await newPage.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await newPage.waitForLoadState('networkidle');
    await newPage.waitForTimeout(3000);

    console.log('📸 Page loaded. Navigate through quiz manually, then take screenshot.');
    console.log('💡 Tip: Use Playwright Inspector to pause and manually navigate');
    console.log('💡 Run with: npx playwright test --debug');
    
    // Take initial screenshot
    await newPage.screenshot({
      path: 'images/therapair-widget-initial.png',
      fullPage: false
    });

    await context.close();
  });

  test('capture clean iframe content - quiz question', async ({ page }) => {
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(4000); // Extra wait for iframe

    // Find and target the specific iframe
    const iframe = page.frameLocator('iframe').first();
    
    try {
      // Wait for iframe content to load
      await iframe.locator('body').waitFor({ timeout: 10000 });
      
      // Look for the main typebot container
      const typebotContainer = iframe.locator('.typebot-container, [class*="bubble"], [class*="chat"]').first();
      
      if (await typebotContainer.isVisible()) {
        console.log('📸 Capturing clean iframe quiz content...');
        
        // Capture with small padding for clean edges
        await typebotContainer.screenshot({
          path: 'images/therapair-quiz-question.png',
          padding: 8
        });
        
        console.log('✅ Clean quiz question captured!');
      } else {
        // Fallback: capture the entire iframe body
        console.log('📸 Capturing iframe body as fallback...');
        await iframe.locator('body').screenshot({
          path: 'images/therapair-quiz-question.png'
        });
      }
    } catch (error) {
      console.log('⚠️  Iframe capture failed:', error.message);
      console.log('💡 Try manual capture method');
    }
  });

  test('capture clean iframe content - results cards', async ({ page }) => {
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(4000);

    console.log('🎯 Manual navigation required to reach results...');
    console.log('📝 Steps:');
    console.log('   1. Complete the quiz in the browser');
    console.log('   2. Reach the results page with 3 therapist cards');
    console.log('   3. Run the screenshot command below');
    
    // Find the iframe
    const iframe = page.frameLocator('iframe').first();
    
    try {
      // Wait for iframe content
      await iframe.locator('body').waitFor({ timeout: 10000 });
      
      // Inject CSS to reduce spacing
      await page.evaluate(() => {
        const frame = document.querySelector('iframe');
        if (frame && frame.contentDocument) {
          const style = frame.contentDocument.createElement('style');
          style.innerHTML = `
            [class*="skill"], [class*="pill"], [class*="tag"] {
              margin-bottom: 0.5rem !important;
            }
            [class*="button"], [class*="cta"], button {
              margin-top: 1rem !important;
              padding-top: 0.5rem !important;
            }
            [class*="card"] {
              margin-bottom: 1rem !important;
            }
          `;
          frame.contentDocument.head.appendChild(style);
        }
      });
      
      console.log('✅ CSS injected for compact spacing');
      
      // Pause for manual navigation
      console.log('⏸️  Pausing for manual quiz completion...');
      console.log('💡 Once at results page, run:');
      console.log('   await iframe.locator(".typebot-container").screenshot({ path: "images/therapair-results-3-cards.png" })');
      
      await page.pause();
      
    } catch (error) {
      console.log('⚠️  Iframe access failed:', error.message);
      console.log('💡 Use manual browser capture method');
    }
  });
});

// Helper test for manual screenshot capture
test.describe('Manual Screenshot Helper', () => {
  test('pause for manual navigation and screenshot', async ({ page }) => {
    await page.setViewportSize({ width: 1600, height: 2400 }); // Extra tall for full cards
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    
    console.log('');
    console.log('═══════════════════════════════════════════════════════');
    console.log('  MANUAL SCREENSHOT HELPER');
    console.log('═══════════════════════════════════════════════════════');
    console.log('');
    console.log('1. Complete the quiz manually in the browser');
    console.log('2. Ensure 3 result cards are fully visible');
    console.log('3. Press F12 to open DevTools');
    console.log('4. Run this command in console:');
    console.log('');
    console.log('   await page.screenshot({ path: "images/therapair-results-3-cards.png" })');
    console.log('');
    console.log('5. Or use Playwright Inspector commands');
    console.log('');
    console.log('═══════════════════════════════════════════════════════');
    
    // Pause for manual interaction
    await page.pause();
  });
});

// Test for capturing adjusted Unison widget
test.describe('Capture Unison Widget with Style Adjustments', () => {
  test('inject CSS to reduce white space and capture', async ({ page }) => {
    await page.setViewportSize({ width: 1600, height: 2400 });
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);

    // Inject CSS to reduce white space between skills pills and book now button
    await page.addStyleTag({
      content: `
        /* Reduce spacing in Unison widget */
        [class*="skill"], [class*="pill"], [class*="tag"] {
          margin-bottom: 0.5rem !important;
        }
        [class*="button-container"], [class*="cta"] {
          margin-top: 1rem !important;
          padding-top: 0.5rem !important;
        }
        /* Reduce gap between elements */
        .typebot-container > * {
          margin-bottom: 0.75rem !important;
        }
      `
    });

    console.log('✅ CSS injected to reduce white space');
    console.log('📸 Ready to capture screenshot');
    
    // Wait a moment for styles to apply
    await page.waitForTimeout(500);
    
    await page.screenshot({
      path: 'images/therapair-widget-adjusted.png',
      fullPage: false
    });
  });
});

