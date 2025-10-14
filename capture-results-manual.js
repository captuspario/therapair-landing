import { chromium } from 'playwright';

async function captureCleanResults() {
  console.log('🎯 Starting clean results capture...');
  
  const browser = await chromium.launch({ 
    headless: false, // Show browser window
    slowMo: 1000 // Slow down for visibility
  });
  
  const context = await browser.newContext({
    viewport: { width: 1600, height: 2400 } // Tall viewport for 3 cards
  });
  
  const page = await context.newPage();
  
  try {
    // Navigate to Unison page
    console.log('🌐 Navigating to Unison Mental Health...');
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(4000);

    // Find the Therapair iframe
    console.log('🔍 Looking for Therapair iframe...');
    const iframe = page.frameLocator('iframe').first();
    
    // Wait for iframe content
    await iframe.locator('body').waitFor({ timeout: 15000 });
    console.log('✅ Iframe loaded successfully');

    // Inject CSS to reduce white space
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
        `;
        frame.contentDocument.head.appendChild(style);
        console.log('✅ CSS injected for compact spacing');
      }
    });

    console.log('');
    console.log('═══════════════════════════════════════════════════════');
    console.log('  MANUAL QUIZ COMPLETION REQUIRED');
    console.log('═══════════════════════════════════════════════════════');
    console.log('');
    console.log('📝 Instructions:');
    console.log('   1. Complete the quiz in the browser window');
    console.log('   2. Answer all questions to reach results page');
    console.log('   3. Ensure 3 therapist cards are visible');
    console.log('   4. Press ENTER in this terminal when ready');
    console.log('');
    console.log('💡 The browser window will stay open for you to navigate');
    console.log('═══════════════════════════════════════════════════════');
    
    // Wait for user to complete quiz manually
    await new Promise(resolve => {
      process.stdin.once('data', () => resolve());
    });

    console.log('');
    console.log('📸 Capturing results screenshot...');
    
    // Try to capture the widget container
    try {
      const widgetContainer = iframe.locator('.typebot-container, [class*="bubble"], [class*="chat"], [class*="container"]').first();
      
      if (await widgetContainer.isVisible()) {
        await widgetContainer.screenshot({
          path: 'images/therapair-results-3-cards.png',
          padding: 10
        });
        console.log('✅ Results captured from widget container!');
      } else {
        // Fallback: capture iframe body
        await iframe.locator('body').screenshot({
          path: 'images/therapair-results-3-cards.png'
        });
        console.log('✅ Results captured from iframe body!');
      }
      
      console.log('📍 Saved to: images/therapair-results-3-cards.png');
      
    } catch (error) {
      console.log('❌ Screenshot capture failed:', error.message);
      console.log('💡 Try taking a manual screenshot of the iframe content');
    }

  } catch (error) {
    console.log('❌ Error:', error.message);
  } finally {
    console.log('');
    console.log('🎉 Capture process complete!');
    console.log('💡 Browser will close in 5 seconds...');
    await page.waitForTimeout(5000);
    await browser.close();
  }
}

// Run the capture
captureCleanResults().catch(console.error);
