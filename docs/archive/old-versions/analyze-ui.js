import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

async function analyzeLandingPage() {
    const browser = await chromium.launch();
    const page = await browser.newPage();

    // Set viewport to desktop size
    await page.setViewportSize({ width: 1440, height: 900 });

    try {
        // Load the optimized landing page
        const filePath = path.resolve(process.cwd(), 'therapair-optimized-landing.html');
        await page.goto(`file://${filePath}`);

        // Wait for page to load completely
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(2000);

        // Take screenshot of the full page
        await page.screenshot({
            path: 'screenshots/therapair-landing-full.png',
            fullPage: true
        });

        // Take screenshot of hero section
        const heroSection = page.locator('section').first();
        await heroSection.screenshot({
            path: 'screenshots/therapair-hero-section.png'
        });

        // Analyze UI elements
        const analysis = {
            title: await page.title(),
            heroHeading: await page.locator('h1').first().textContent(),
            ctaButtons: await page.locator('button').count(),
            emailInputs: await page.locator('input[type="email"]').count(),
            sections: await page.locator('section').count(),
            therapistCards: await page.locator('.therapist-card').count(),
            featureCards: await page.locator('.feature-card').count()
        };

        // Test mobile responsiveness
        await page.setViewportSize({ width: 375, height: 667 });
        await page.waitForTimeout(1000);

        await page.screenshot({
            path: 'screenshots/therapair-mobile.png',
            fullPage: true
        });

        // Test form functionality
        await page.setViewportSize({ width: 1440, height: 900 });
        await page.fill('#hero-email', 'test@example.com');
        await page.screenshot({
            path: 'screenshots/therapair-form-filled.png'
        });

        console.log('Landing Page Analysis:', JSON.stringify(analysis, null, 2));

        // Save analysis to file
        fs.writeFileSync('ui-analysis-report.json', JSON.stringify(analysis, null, 2));

        console.log('✅ Screenshots saved to screenshots/ directory');
        console.log('✅ Analysis report saved to ui-analysis-report.json');

    } catch (error) {
        console.error('Analysis failed:', error);
    } finally {
        await browser.close();
    }
}

// Create screenshots directory
if (!fs.existsSync('screenshots')) {
    fs.mkdirSync('screenshots');
}

analyzeLandingPage();