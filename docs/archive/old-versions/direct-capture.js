import { chromium } from 'playwright';

async function directCapture() {
  console.log('🎯 Direct Therapair widget capture...');
  
  const browser = await chromium.launch({ 
    headless: false,
    slowMo: 1000
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
    
    // Get all frames
    const frames = page.frames();
    console.log(`Found ${frames.length} frames`);
    
    // Find the Therapair widget frame (Frame 1)
    let therapairFrame = null;
    for (let i = 0; i < frames.length; i++) {
      const frame = frames[i];
      const url = frame.url();
      console.log(`Frame ${i}: ${url}`);
      
      if (url.includes('therapair-widget')) {
        therapairFrame = frame;
        console.log(`✅ Found Therapair frame at index ${i}!`);
        break;
      }
    }
    
    if (!therapairFrame) {
      console.log('❌ Therapair frame not found');
      return;
    }
    
    console.log('🎨 Injecting CSS for compact spacing...');
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
    
    console.log('');
    console.log('🎯 THERAPIST WIDGET READY!');
    console.log('');
    console.log('📝 Instructions:');
    console.log('   1. Complete the quiz in the browser window');
    console.log('   2. Answer all questions to reach results page');
    console.log('   3. Ensure 3 therapist cards are visible');
    console.log('   4. Open browser console (F12) and run:');
    console.log('');
    console.log('   // For widget container:');
    console.log('   document.querySelector("iframe").contentDocument.querySelector(".typebot-container").style.border = "2px solid red";');
    console.log('   // Then take screenshot of the red-bordered area');
    console.log('');
    console.log('   // Or use DevTools:');
    console.log('   // Right-click widget → Inspect → Right-click container → "Capture node screenshot"');
    console.log('');
    console.log('Press Ctrl+C when done...');
    
    // Keep browser open indefinitely
    await new Promise(() => {});
    
  } catch (error) {
    console.log('❌ Error:', error.message);
  } finally {
    await browser.close();
  }
}

directCapture().catch(console.error);

