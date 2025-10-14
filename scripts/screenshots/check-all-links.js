import { chromium } from 'playwright';

async function checkAllLinks() {
    console.log('🔍 Checking all links on the landing page...\n');
    
    const browser = await chromium.launch({ 
        headless: false,
        slowMo: 500
    });
    
    const context = await browser.newContext({
        viewport: { width: 1600, height: 1200 }
    });
    
    const page = await context.newPage();
    
    try {
        // Navigate to the landing page
        console.log('🌐 Navigating to landing page...');
        await page.goto('https://therapair.com.au', {
            waitUntil: 'networkidle',
            timeout: 30000
        });
        
        // Wait for page to load
        await page.waitForTimeout(3000);
        
        // Get all links on the page
        const links = await page.evaluate(() => {
            const linkElements = document.querySelectorAll('a[href]');
            return Array.from(linkElements).map(link => ({
                text: link.textContent.trim(),
                href: link.href,
                target: link.target,
                rel: link.rel
            }));
        });
        
        console.log(`📊 Found ${links.length} links to check\n`);
        
        const linkResults = [];
        const brokenLinks = [];
        const workingLinks = [];
        
        // Check each link
        for (let i = 0; i < links.length; i++) {
            const link = links[i];
            console.log(`🔗 Checking ${i + 1}/${links.length}: "${link.text}" → ${link.href}`);
            
            try {
                // Skip mailto and tel links
                if (link.href.startsWith('mailto:') || link.href.startsWith('tel:')) {
                    console.log(`   ✅ Skipped (${link.href.startsWith('mailto:') ? 'email' : 'phone'} link)`);
                    workingLinks.push(link);
                    continue;
                }
                
                // Check if it's an external link
                if (link.href.startsWith('http') && !link.href.includes('therapair.com.au')) {
                    console.log(`   🌐 External link - checking accessibility...`);
                    
                    // Try to fetch the external link
                    const response = await page.goto(link.href, {
                        waitUntil: 'domcontentloaded',
                        timeout: 10000
                    });
                    
                    if (response && response.status() < 400) {
                        console.log(`   ✅ Working (Status: ${response.status()})`);
                        workingLinks.push(link);
                    } else {
                        console.log(`   ❌ Broken (Status: ${response ? response.status() : 'No response'})`);
                        brokenLinks.push(link);
                    }
                    
                    // Go back to main page
                    await page.goto('https://therapair.com.au', { waitUntil: 'networkidle' });
                    await page.waitForTimeout(1000);
                    
                } else {
                    // Internal link - check if it exists
                    console.log(`   🏠 Internal link - checking...`);
                    
                    try {
                        const response = await page.goto(link.href, {
                            waitUntil: 'domcontentloaded',
                            timeout: 10000
                        });
                        
                        if (response && response.status() < 400) {
                            console.log(`   ✅ Working (Status: ${response.status()})`);
                            workingLinks.push(link);
                        } else {
                            console.log(`   ❌ Broken (Status: ${response ? response.status() : 'No response'})`);
                            brokenLinks.push(link);
                        }
                        
                        // Go back to main page
                        await page.goto('https://therapair.com.au', { waitUntil: 'networkidle' });
                        await page.waitForTimeout(1000);
                        
                    } catch (error) {
                        console.log(`   ❌ Error: ${error.message}`);
                        brokenLinks.push(link);
                    }
                }
                
            } catch (error) {
                console.log(`   ❌ Error checking link: ${error.message}`);
                brokenLinks.push(link);
            }
        }
        
        // Generate report
        console.log('\n📋 LINK CHECK REPORT');
        console.log('═══════════════════════════════════════════════════════════');
        console.log(`✅ Working links: ${workingLinks.length}`);
        console.log(`❌ Broken links: ${brokenLinks.length}`);
        console.log(`📊 Total links: ${links.length}`);
        
        if (brokenLinks.length > 0) {
            console.log('\n❌ BROKEN LINKS:');
            brokenLinks.forEach((link, index) => {
                console.log(`${index + 1}. "${link.text}" → ${link.href}`);
            });
        }
        
        if (workingLinks.length > 0) {
            console.log('\n✅ WORKING LINKS:');
            workingLinks.forEach((link, index) => {
                console.log(`${index + 1}. "${link.text}" → ${link.href}`);
            });
        }
        
        // Save report to file
        const report = {
            timestamp: new Date().toISOString(),
            totalLinks: links.length,
            workingLinks: workingLinks.length,
            brokenLinks: brokenLinks.length,
            brokenLinksList: brokenLinks,
            workingLinksList: workingLinks
        };
        
        const fs = require('fs');
        fs.writeFileSync('link-check-report.json', JSON.stringify(report, null, 2));
        console.log('\n💾 Report saved to: link-check-report.json');
        
    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
        console.log('\n✅ Link check complete!');
    }
}

console.log('═══════════════════════════════════════════════════════════');
console.log('  Therapair Link Checker                                   ');
console.log('═══════════════════════════════════════════════════════════\n');

checkAllLinks().catch(console.error);
