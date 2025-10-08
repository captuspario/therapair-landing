import { chromium } from 'playwright';
import fs from 'fs';

async function competitiveAnalysis() {
    const browser = await chromium.launch();
    const page = await browser.newPage();

    // Set desktop viewport
    await page.setViewportSize({ width: 1440, height: 900 });

    const competitors = [
        {
            name: 'BetterHelp',
            url: 'https://www.betterhelp.com',
            filename: 'betterhelp-homepage'
        },
        {
            name: 'Psychology Today',
            url: 'https://www.psychologytoday.com',
            filename: 'psychology-today-homepage'
        },
        {
            name: 'Talkspace',
            url: 'https://www.talkspace.com',
            filename: 'talkspace-homepage'
        },
        {
            name: 'MDLIVE',
            url: 'https://www.mdlive.com/therapy',
            filename: 'mdlive-homepage'
        }
    ];

    const analysis = {
        timestamp: new Date().toISOString(),
        competitors: [],
        therapairAdvantages: [
            'Inclusive, culturally competent matching',
            'LGBTQ+ and neurodiversity focused',
            'Clean, accessible UI design',
            'Clear value proposition',
            'Professional therapist profiles',
            'Multiple conversion points',
            'Trust indicators and social proof',
            'Mobile-optimized experience'
        ]
    };

    try {
        for (const competitor of competitors) {
            console.log(`📸 Capturing ${competitor.name}...`);

            try {
                await page.goto(competitor.url, {
                    waitUntil: 'networkidle',
                    timeout: 30000
                });

                // Wait for page to load
                await page.waitForTimeout(3000);

                // Take screenshot
                await page.screenshot({
                    path: `screenshots/competitor-${competitor.filename}.png`,
                    fullPage: false // Just above-the-fold
                });

                // Analyze key elements
                const competitorAnalysis = {
                    name: competitor.name,
                    url: competitor.url,
                    title: await page.title(),
                    hasHeroSection: await page.locator('h1').count() > 0,
                    ctaButtons: await page.locator('button, .btn, [class*="button"]').count(),
                    emailInputs: await page.locator('input[type="email"]').count(),
                    forms: await page.locator('form').count(),
                    mainHeading: '',
                    primaryCTA: ''
                };

                // Try to capture main heading
                try {
                    const h1 = await page.locator('h1').first();
                    if (h1) {
                        competitorAnalysis.mainHeading = await h1.textContent();
                    }
                } catch (e) {
                    console.log(`Could not capture heading for ${competitor.name}`);
                }

                // Try to capture primary CTA
                try {
                    const primaryBtn = await page.locator('button, .btn, [class*="button"]').first();
                    if (primaryBtn) {
                        competitorAnalysis.primaryCTA = await primaryBtn.textContent();
                    }
                } catch (e) {
                    console.log(`Could not capture CTA for ${competitor.name}`);
                }

                analysis.competitors.push(competitorAnalysis);
                console.log(`✅ Captured ${competitor.name}`);

            } catch (error) {
                console.error(`❌ Failed to capture ${competitor.name}:`, error.message);
                analysis.competitors.push({
                    name: competitor.name,
                    url: competitor.url,
                    error: error.message
                });
            }

            // Wait between requests
            await page.waitForTimeout(2000);
        }

        // Create comprehensive comparison report
        const report = {
            ...analysis,
            therapairComparison: {
                uniqueValue: 'First platform specifically designed for inclusive, culturally competent therapy matching',
                designAdvantages: [
                    'Cleaner, more accessible interface',
                    'Clear focus on diversity and inclusion',
                    'Progressive questionnaire design',
                    'Professional therapist showcase',
                    'Trust-building through transparency'
                ],
                conversionOptimizations: [
                    'Multiple email capture points',
                    'Clear value proposition in hero',
                    'Social proof indicators',
                    'Interest-based segmentation',
                    'Mobile-first responsive design'
                ]
            }
        };

        // Save analysis
        fs.writeFileSync('competitive-analysis-report.json', JSON.stringify(report, null, 2));

        console.log('\n📊 COMPETITIVE ANALYSIS COMPLETE');
        console.log('=================================');
        console.log(`✅ Analyzed ${competitors.length} competitors`);
        console.log('✅ Screenshots saved to screenshots/ directory');
        console.log('✅ Full report saved to competitive-analysis-report.json');

        // Print summary
        console.log('\n🎯 THERAPAIR COMPETITIVE ADVANTAGES:');
        report.therapairAdvantages.forEach(advantage => {
            console.log(`  • ${advantage}`);
        });

    } catch (error) {
        console.error('Analysis failed:', error);
    } finally {
        await browser.close();
    }
}

// Ensure screenshots directory exists
if (!fs.existsSync('screenshots')) {
    fs.mkdirSync('screenshots');
}

competitiveAnalysis();