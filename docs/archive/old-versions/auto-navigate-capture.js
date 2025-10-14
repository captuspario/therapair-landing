import { chromium } from 'playwright';

async function autoNavigateCapture() {
  console.log('🎯 Auto-navigating through quiz to capture results...');
  
  const browser = await chromium.launch({ 
    headless: false,
    slowMo: 1500 // Slow enough to see progression
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
    
    // Capture initial quiz question
    console.log('📸 Capturing initial quiz question...');
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
    
    // Auto-navigate through the quiz
    console.log('🎯 Auto-navigating through quiz...');
    
    let questionCount = 0;
    const maxQuestions = 10; // Safety limit
    
    while (questionCount < maxQuestions) {
      try {
        console.log(`📝 Processing question ${questionCount + 1}...`);
        
        // Wait for buttons to be visible
        await therapairFrame.waitForTimeout(1000);
        
        // Look for clickable buttons/options
        const buttons = therapairFrame.locator('button, [role="button"], [class*="button"], [class*="option"]').filter({ hasText: /.+/ });
        const buttonCount = await buttons.count();
        
        console.log(`Found ${buttonCount} buttons/options`);
        
        if (buttonCount === 0) {
          console.log('No more buttons found, checking if we reached results...');
          break;
        }
        
        // Click the first available button/option
        const firstButton = buttons.first();
        if (await firstButton.isVisible()) {
          console.log('🖱️ Clicking first option...');
          await firstButton.click();
          await therapairFrame.waitForTimeout(2000); // Wait for next question
          questionCount++;
        } else {
          console.log('Button not visible, breaking...');
          break;
        }
        
        // Check if we've reached results (look for therapist cards)
        const resultCards = therapairFrame.locator('[class*="therapist"], [class*="card"], [class*="profile"]');
        const cardCount = await resultCards.count();
        
        if (cardCount >= 3) {
          console.log(`✅ Found ${cardCount} result cards!`);
          break;
        }
        
      } catch (error) {
        console.log(`⚠️ Error on question ${questionCount + 1}:`, error.message);
        questionCount++; // Continue anyway
      }
    }
    
    // Wait a bit more for results to fully load
    await therapairFrame.waitForTimeout(3000);
    
    // Capture results
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
    
    console.log('🎉 Auto-navigation complete!');
    console.log('💡 Browser will stay open for 10 seconds for inspection...');
    await page.waitForTimeout(10000);
    
  } catch (error) {
    console.log('❌ Error:', error.message);
  } finally {
    await browser.close();
  }
}

autoNavigateCapture().catch(console.error);

