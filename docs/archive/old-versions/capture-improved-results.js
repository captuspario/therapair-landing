import { chromium } from 'playwright';

async function captureImprovedResults() {
  console.log('🎯 Capturing improved results cards with better spacing...\n');
  
  const browser = await chromium.launch({ 
    headless: false,
    slowMo: 1000
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
    
    // Find the Therapair widget frame
    const frames = page.frames();
    let therapairFrame = null;
    
    for (const frame of frames) {
      if (frame.url().includes('therapair-widget')) {
        therapairFrame = frame;
        console.log('✅ Found Therapair widget frame!');
        break;
      }
    }
    
    if (!therapairFrame) {
      console.log('❌ Therapair frame not found');
      return;
    }
    
    // Inject CSS to improve spacing following UI best practices
    await therapairFrame.addStyleTag({
      content: `
        /* Global UI Design Principles - Reduce excessive white space */
        
        /* Target the result cards container */
        [class*="result"], [class*="match"], [class*="card"] {
          padding-bottom: 0.5rem !important;
        }
        
        /* Reduce spacing between skills pills and buttons */
        [class*="skill"], [class*="pill"], [class*="tag"], [class*="badge"] {
          margin-bottom: 0.25rem !important;
          margin-right: 0.5rem !important;
        }
        
        /* Compact button spacing */
        button, [class*="button"], [class*="cta"], [class*="book"] {
          margin-top: 0.75rem !important;
          padding: 0.75rem 1.5rem !important;
        }
        
        /* Reduce card internal padding */
        [class*="card"] > * {
          margin-bottom: 0.5rem !important;
        }
        
        /* Specific targeting for therapist cards */
        [class*="therapist"], [class*="profile"] {
          padding-bottom: 1rem !important;
        }
        
        /* Ensure skills section has minimal bottom margin */
        [class*="skill"]:last-child, [class*="pill"]:last-child {
          margin-bottom: 0.5rem !important;
        }
        
        /* Button container spacing */
        [class*="button"]:before, [class*="cta"]:before {
          margin-top: 0.5rem !important;
        }
        
        /* Global spacing optimization */
        * {
          box-sizing: border-box;
        }
        
        /* Ensure consistent spacing rhythm */
        [class*="card"] {
          display: flex !important;
          flex-direction: column !important;
          gap: 0.5rem !important;
        }
      `
    });
    
    console.log('🎨 CSS injected for improved spacing following UI best practices');
    
    console.log('\n📝 **MANUAL STEPS REQUIRED:**\n');
    console.log('1. Complete the quiz in the browser window');
    console.log('2. Answer all questions until you reach the RESULTS page');
    console.log('3. Verify the spacing looks better between skills pills and "Book Now" buttons');
    console.log('4. Make sure you can see 3 THERAPIST CARDS with improved spacing');
    console.log('5. Once satisfied with the spacing, come back here and press ENTER\n');
    
    // Wait for user to complete quiz and verify spacing
    await new Promise((resolve) => {
      process.stdin.once('data', () => {
        console.log('\n📸 Capturing improved results now...\n');
        resolve();
      });
    });
    
    // Give time for any animations to settle
    await page.waitForTimeout(2000);
    
    // Capture the iframe content
    try {
      const iframeElement = await page.$('iframe');
      if (iframeElement) {
        await iframeElement.screenshot({
          path: 'images/therapair-results-3-cards.png',
          type: 'png'
        });
        console.log('✅ Improved screenshot saved: images/therapair-results-3-cards.png');
      } else {
        console.log('❌ Could not find iframe element');
      }
    } catch (error) {
      console.log('❌ Screenshot failed:', error.message);
    }
    
    console.log('\n🎉 Process complete! Browser will stay open for 5 seconds...');
    await page.waitForTimeout(5000);
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    console.log('\n💡 Keeping browser open for manual inspection...');
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
console.log('  Therapair Results Cards - Improved Spacing Capture      ');
console.log('═══════════════════════════════════════════════════════════\n');

captureImprovedResults().catch(console.error);
