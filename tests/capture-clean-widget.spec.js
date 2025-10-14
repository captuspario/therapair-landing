import { test } from '@playwright/test';

test.describe('Capture Clean Therapair Widget Screenshots', () => {
  
  test('capture clean quiz question from iframe only', async ({ page }) => {
    console.log('🎯 Capturing clean quiz question (iframe content only)...');
    
    // Navigate to Unison page
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(4000); // Wait for iframe to load

    // Find the Therapair iframe
    const iframe = page.frameLocator('iframe').first();
    
    try {
      // Wait for iframe content to be ready
      await iframe.locator('body').waitFor({ timeout: 15000 });
      console.log('✅ Iframe loaded successfully');

      // Look for the main typebot container
      const widgetContainer = iframe.locator('.typebot-container, [class*="bubble"], [class*="chat"], [class*="container"]').first();
      
      if (await widgetContainer.isVisible()) {
        console.log('📸 Capturing clean widget content...');
        
        // Capture just the widget content with small padding
        await widgetContainer.screenshot({
          path: 'images/therapair-quiz-question.png',
          padding: 10 // Small padding for clean edges
        });
        
        console.log('✅ Clean quiz question captured!');
        console.log('📍 Saved to: images/therapair-quiz-question.png');
      } else {
        console.log('⚠️  Widget container not found, trying fallback...');
        
        // Fallback: capture the iframe body
        await iframe.locator('body').screenshot({
          path: 'images/therapair-quiz-question.png'
        });
        
        console.log('✅ Iframe body captured as fallback');
      }
      
    } catch (error) {
      console.log('❌ Iframe capture failed:', error.message);
      console.log('💡 Try manual capture method (see guide)');
    }
  });

  test('capture clean results cards from iframe', async ({ page }) => {
    console.log('🎯 Setting up for clean results capture...');
    
    // Navigate to Unison page
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(4000);

    // Find the Therapair iframe
    const iframe = page.frameLocator('iframe').first();
    
    try {
      // Wait for iframe content
      await iframe.locator('body').waitFor({ timeout: 15000 });
      console.log('✅ Iframe loaded successfully');

      // Inject CSS to reduce white space between skills and buttons
      await page.evaluate(() => {
        const frame = document.querySelector('iframe');
        if (frame && frame.contentDocument) {
          const style = frame.contentDocument.createElement('style');
          style.innerHTML = `
            /* Reduce spacing between skills pills and book now button */
            [class*="skill"], [class*="pill"], [class*="tag"] {
              margin-bottom: 0.5rem !important;
              margin-right: 0.5rem !important;
            }
            [class*="button"], [class*="cta"], button[class*="book"] {
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
            /* Ensure cards fit nicely */
            [class*="container"] {
              max-height: none !important;
              overflow: visible !important;
            }
          `;
          frame.contentDocument.head.appendChild(style);
          console.log('CSS injected for compact spacing');
        }
      });
      
      console.log('✅ CSS injected to reduce white space');
      
      // Instructions for manual navigation
      console.log('');
      console.log('═══════════════════════════════════════════════════════');
      console.log('  MANUAL QUIZ NAVIGATION REQUIRED');
      console.log('═══════════════════════════════════════════════════════');
      console.log('');
      console.log('📝 Next Steps:');
      console.log('   1. Complete the quiz in the browser window');
      console.log('   2. Answer all questions to reach the results page');
      console.log('   3. Ensure 3 therapist cards are visible');
      console.log('   4. Run the screenshot command below');
      console.log('');
      console.log('💡 Screenshot Command (copy & paste in console):');
      console.log('');
      console.log('   await iframe.locator(".typebot-container").screenshot({');
      console.log('     path: "images/therapair-results-3-cards.png",');
      console.log('     padding: 10');
      console.log('   })');
      console.log('');
      console.log('🎯 Alternative (if above doesn\'t work):');
      console.log('   await iframe.locator("body").screenshot({');
      console.log('     path: "images/therapair-results-3-cards.png"');
      console.log('   })');
      console.log('');
      console.log('═══════════════════════════════════════════════════════');
      
      // Pause for manual navigation
      await page.pause();
      
    } catch (error) {
      console.log('❌ Iframe setup failed:', error.message);
      console.log('💡 Use manual browser capture method (see guide)');
    }
  });

  test('quick iframe inspection', async ({ page }) => {
    console.log('🔍 Inspecting iframe structure...');
    
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(4000);

    // Find all frames
    const frames = page.frames();
    console.log(`Found ${frames.length} frames`);
    
    for (let i = 0; i < frames.length; i++) {
      const frame = frames[i];
      const url = frame.url();
      console.log(`Frame ${i}: ${url}`);
      
      try {
        // Try to access frame content
        const bodyText = await frame.locator('body').textContent();
        console.log(`  Body preview: ${bodyText.substring(0, 100)}...`);
        
        // Look for common selectors
        const selectors = ['.typebot-container', '[class*="bubble"]', '[class*="chat"]', '[class*="container"]'];
        for (const selector of selectors) {
          const element = frame.locator(selector).first();
          if (await element.isVisible().catch(() => false)) {
            console.log(`  ✅ Found element: ${selector}`);
          }
        }
      } catch (error) {
        console.log(`  ❌ Cannot access frame content: ${error.message}`);
      }
    }
  });
});

// Quick helper test for manual capture
test.describe('Manual Capture Helper', () => {
  test('pause and provide instructions', async ({ page }) => {
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(4000);

    console.log('');
    console.log('🎯 MANUAL CAPTURE MODE');
    console.log('');
    console.log('1. Complete the quiz to reach results with 3 cards');
    console.log('2. Use DevTools to inspect iframe content');
    console.log('3. Take screenshot of just the widget content');
    console.log('');
    console.log('💡 DevTools Method:');
    console.log('   - Right-click on the widget');
    console.log('   - Inspect Element');
    console.log('   - Right-click on the widget container');
    console.log('   - "Capture node screenshot"');
    console.log('');
    console.log('⏸️  Pausing for manual capture...');
    
    await page.pause();
  });
});

