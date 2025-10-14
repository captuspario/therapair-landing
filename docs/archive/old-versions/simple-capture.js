import { chromium } from 'playwright';

async function simpleCapture() {
  console.log('🎯 Starting simple clean capture...');
  
  const browser = await chromium.launch({ 
    headless: false,
    slowMo: 500
  });
  
  const page = await browser.newPage();
  
  try {
    console.log('🌐 Navigating to Unison page...');
    await page.goto('https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/', {
      waitUntil: 'networkidle',
      timeout: 30000
    });
    
    console.log('✅ Page loaded, waiting for iframe...');
    await page.waitForTimeout(5000);
    
    // Find iframe
    const iframe = page.frameLocator('iframe').first();
    
    console.log('🔍 Looking for iframe content...');
    await iframe.locator('body').waitFor({ timeout: 15000 });
    console.log('✅ Iframe found!');
    
    // Inject CSS to reduce white space
    console.log('🎨 Injecting CSS for compact spacing...');
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
    
    console.log('');
    console.log('🎯 Browser is ready! Complete the quiz manually.');
    console.log('💡 When you reach results with 3 cards, run this in console:');
    console.log('');
    console.log('await iframe.locator(".typebot-container").screenshot({');
    console.log('  path: "images/therapair-results-3-cards.png",');
    console.log('  padding: 10');
    console.log('})');
    console.log('');
    console.log('Press Ctrl+C to close when done...');
    
    // Keep browser open
    await new Promise(() => {}); // Infinite wait
    
  } catch (error) {
    console.log('❌ Error:', error.message);
  } finally {
    await browser.close();
  }
}

simpleCapture().catch(console.error);

