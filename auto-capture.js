import { chromium } from 'playwright';

async function autoCapture() {
  console.log('🎯 Auto-capturing clean screenshots...');
  
  const browser = await chromium.launch({ 
    headless: false,
    slowMo: 2000 // Slow down to see what's happening
  });
  
  const page = await browser.newPage();
  
  try {
    console.log('🌐 Navigating to Unison page...');
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/', {
      waitUntil: 'networkidle',
      timeout: 30000
    });
    
    console.log('✅ Page loaded, waiting for frames...');
    await page.waitForTimeout(3000);
    
    // Find the Therapair widget frame
    const frames = page.frames();
    let therapairFrame = null;
    
    for (const frame of frames) {
      if (frame.url().includes('therapair-widget')) {
        therapairFrame = frame;
        console.log('✅ Found Therapair frame!');
        break;
      }
    }
    
    if (!therapairFrame) {
      console.log('❌ Therapair frame not found');
      return;
    }
    
    // Inject CSS for compact spacing
    await therapairFrame.addStyleTag({
      content: `
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
      `
    });
    
    console.log('🎨 CSS injected for compact spacing');
    
    // Wait for widget to be ready
    await therapairFrame.locator('body').waitFor();
    await page.waitForTimeout(2000);
    
    // Capture quiz question
    console.log('📸 Capturing quiz question...');
    try {
      const quizContainer = therapairFrame.locator('.typebot-container, [class*="container"]').first();
      if (await quizContainer.isVisible()) {
        await quizContainer.screenshot({
          path: 'images/therapair-quiz-question.png',
          padding: 10
        });
        console.log('✅ Quiz question captured!');
      }
    } catch (error) {
      console.log('⚠️ Quiz capture failed:', error.message);
    }
    
    console.log('');
    console.log('🎯 MANUAL QUIZ COMPLETION REQUIRED');
    console.log('');
    console.log('📝 Next steps:');
    console.log('   1. Complete the quiz manually in the browser');
    console.log('   2. Navigate to the results page with 3 therapist cards');
    console.log('   3. Press ENTER in this terminal when ready to capture results');
    console.log('');
    
    // Wait for user input
    await new Promise(resolve => {
      process.stdin.once('data', () => resolve());
    });
    
    console.log('📸 Capturing results...');
    try {
      const resultsContainer = therapairFrame.locator('.typebot-container, [class*="container"]').first();
      if (await resultsContainer.isVisible()) {
        await resultsContainer.screenshot({
          path: 'images/therapair-results-3-cards.png',
          padding: 10
        });
        console.log('✅ Results captured!');
      }
    } catch (error) {
      console.log('⚠️ Results capture failed:', error.message);
      console.log('💡 Try manual capture using DevTools');
    }
    
  } catch (error) {
    console.log('❌ Error:', error.message);
  } finally {
    console.log('🎉 Capture complete! Browser closing in 3 seconds...');
    await page.waitForTimeout(3000);
    await browser.close();
  }
}

autoCapture().catch(console.error);

