import { chromium } from 'playwright';

async function capture3ResultCards() {
  console.log('🎯 Opening browser to capture 3 result cards...\n');
  
  const browser = await chromium.launch({ 
    headless: false,
    slowMo: 500
  });
  
  const context = await browser.newContext({
    viewport: { width: 1600, height: 2400 }
  });
  
  const page = await context.newPage();
  
  try {
    console.log('🌐 Navigating to Unison page...');
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/', {
      waitUntil: 'networkidle',
      timeout: 30000
    });
    
    console.log('⏳ Waiting for page to fully load...');
    await page.waitForTimeout(4000);
    
    // Find all iframes
    const frames = page.frames();
    let therapairFrame = null;
    
    console.log(`📦 Found ${frames.length} frames on page`);
    
    for (const frame of frames) {
      const frameUrl = frame.url();
      console.log(`   - Frame URL: ${frameUrl}`);
      if (frameUrl.includes('therapair') || frameUrl.includes('typebot')) {
        therapairFrame = frame;
        console.log('✅ Found Therapair widget frame!');
        break;
      }
    }
    
    if (!therapairFrame) {
      // Try frame locator approach
      console.log('🔍 Trying frame locator approach...');
      await page.waitForTimeout(2000);
      const iframeElement = page.frameLocator('iframe').first();
      
      console.log('\n📝 **MANUAL STEPS REQUIRED:**\n');
      console.log('1. Complete the quiz in the browser window');
      console.log('2. Answer all questions until you reach the RESULTS page');
      console.log('3. Make sure you can see 3 THERAPIST CARDS with:');
      console.log('   - Profile pictures at the top');
      console.log('   - Therapist names and specialties');
      console.log('   - "Book Now" buttons at the bottom');
      console.log('4. Once you see the results, come back here and press ENTER\n');
      
      // Wait for user to complete quiz
      await new Promise((resolve) => {
        process.stdin.once('data', () => {
          console.log('\n📸 Capturing results now...\n');
          resolve();
        });
      });
      
      // Give a moment for any animations to settle
      await page.waitForTimeout(2000);
      
      // Try to inject CSS for compact spacing
      try {
        await page.evaluate(() => {
          const style = document.createElement('style');
          style.textContent = `
            iframe {
              transform: scale(1);
            }
            [class*="card"] {
              margin-bottom: 1rem !important;
            }
            [class*="skill"], [class*="pill"] {
              margin-bottom: 0.5rem !important;
            }
            button, [class*="button"] {
              margin-top: 1rem !important;
            }
          `;
          document.head.appendChild(style);
        });
        console.log('✅ CSS injected for compact spacing');
      } catch (error) {
        console.log('⚠️ Could not inject CSS, continuing anyway...');
      }
      
      await page.waitForTimeout(1000);
      
      // Capture the entire iframe content
      try {
        // Find the iframe element and capture it
        const iframeHandle = await page.$('iframe');
        if (iframeHandle) {
          await iframeHandle.screenshot({
            path: 'images/therapair-results-3-cards.png',
            type: 'png'
          });
          console.log('✅ Screenshot saved: images/therapair-results-3-cards.png');
        } else {
          console.log('❌ Could not find iframe element');
        }
      } catch (error) {
        console.log('❌ Screenshot failed:', error.message);
        console.log('\n💡 Try using browser DevTools to manually capture the iframe');
      }
      
      console.log('\n🎉 Process complete! Browser will stay open for 5 seconds...');
      await page.waitForTimeout(5000);
      
    } else {
      // We found the frame directly
      console.log('✅ Using direct frame access');
      
      console.log('\n📝 **MANUAL STEPS REQUIRED:**\n');
      console.log('1. Complete the quiz in the browser window');
      console.log('2. Answer all questions until you reach the RESULTS page');
      console.log('3. Make sure you can see 3 THERAPIST CARDS with:');
      console.log('   - Profile pictures at the top');
      console.log('   - Therapist names and specialties');
      console.log('   - "Book Now" buttons at the bottom');
      console.log('4. Once you see the results, come back here and press ENTER\n');
      
      // Wait for user
      await new Promise((resolve) => {
        process.stdin.once('data', () => {
          console.log('\n📸 Capturing results now...\n');
          resolve();
        });
      });
      
      // Inject CSS for compact spacing
      try {
        await therapairFrame.addStyleTag({
          content: `
            [class*="card"] {
              margin-bottom: 1rem !important;
            }
            [class*="skill"], [class*="pill"] {
              margin-bottom: 0.5rem !important;
            }
            button, [class*="button"] {
              margin-top: 1rem !important;
            }
          `
        });
        console.log('✅ CSS injected');
      } catch (error) {
        console.log('⚠️ CSS injection failed');
      }
      
      await therapairFrame.waitForTimeout(1000);
      
      // Capture the frame content
      const frameElement = await page.$('iframe');
      if (frameElement) {
        await frameElement.screenshot({
          path: 'images/therapair-results-3-cards.png',
          type: 'png'
        });
        console.log('✅ Screenshot saved: images/therapair-results-3-cards.png');
      }
      
      console.log('\n🎉 Complete! Browser will close in 5 seconds...');
      await page.waitForTimeout(5000);
    }
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    console.log('\n💡 Keeping browser open for manual capture...');
    console.log('Press ENTER to close browser');
    await new Promise((resolve) => {
      process.stdin.once('data', resolve);
    });
  } finally {
    await browser.close();
    console.log('\n✅ Browser closed. Check images/therapair-results-3-cards.png');
  }
}

console.log('═══════════════════════════════════════════════════════════');
console.log('  Therapair Results Cards Capture (3x Therapist Profiles)  ');
console.log('═══════════════════════════════════════════════════════════\n');

capture3ResultCards().catch(console.error);
