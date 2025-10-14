import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

async function generateJourneyLayout() {
    console.log('🎯 Generating journey layout with user-provided screenshots...\n');
    
    const browser = await chromium.launch({ 
        headless: false,
        slowMo: 500
    });
    
    const context = await browser.newContext({
        viewport: { width: 1600, height: 2400 }
    });
    
    const page = await context.newPage();
    
    try {
        // First, let's copy the user-provided images to our images directory
        console.log('📁 Copying user-provided images...');
        
        const sourceDir = '/Users/tino/Projects/therapair-landing-page';
        const imagesDir = path.join(sourceDir, 'images');
        
        // Copy the images with standardized names
        const imageMappings = [
            { source: 'journey-1-questions.png', target: 'therapair-quiz-question.png' },
            { source: 'journey-2-results.png', target: 'therapair-results-3-cards.png' },
            { source: 'journey-3-booking.png', target: 'therapair-booking-form.png' }
        ];
        
        for (const mapping of imageMappings) {
            const sourcePath = path.join(sourceDir, mapping.source);
            const targetPath = path.join(imagesDir, mapping.target);
            
            if (fs.existsSync(sourcePath)) {
                fs.copyFileSync(sourcePath, targetPath);
                console.log(`✅ Copied ${mapping.source} → ${mapping.target}`);
            } else {
                console.log(`⚠️ Source file not found: ${mapping.source}`);
            }
        }
        
        // Generate HTML layout with consistent sizing
        console.log('🎨 Generating layout with global UI best practices...');
        
        const layoutHTML = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Therapair Journey Layout</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                    padding: 2rem;
                    min-height: 100vh;
                }
                
                .container {
                    max-width: 1400px;
                    margin: 0 auto;
                }
                
                .header {
                    text-align: center;
                    margin-bottom: 3rem;
                }
                
                .header h1 {
                    color: #4F064F;
                    font-size: 2.5rem;
                    font-weight: 700;
                    margin-bottom: 1rem;
                }
                
                .header p {
                    color: #6B7280;
                    font-size: 1.125rem;
                    max-width: 600px;
                    margin: 0 auto;
                }
                
                /* Global UI Best Practices - 3 Column Grid */
                .journey-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
                    gap: 2rem;
                    margin-bottom: 3rem;
                }
                
                /* 3-column layout for desktop above 1200px */
                @media (min-width: 1200px) {
                    .journey-grid {
                        grid-template-columns: repeat(3, minmax(400px, 450px));
                        justify-content: center;
                        gap: 2.5rem;
                    }
                }
                
                /* Mobile responsive */
                @media (max-width: 768px) {
                    .journey-grid {
                        grid-template-columns: 1fr;
                        gap: 1.5rem;
                    }
                    
                    .header h1 {
                        font-size: 2rem;
                    }
                }
                
                .journey-card {
                    background: white;
                    border-radius: 24px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    overflow: hidden;
                    transition: all 0.3s ease;
                    border: 2px solid rgba(155, 116, 183, 0.1);
                }
                
                .journey-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
                    border-color: rgba(155, 116, 183, 0.2);
                }
                
                .card-header {
                    padding: 1.5rem 1.5rem 1rem;
                }
                
                .card-title {
                    font-size: 1.25rem;
                    font-weight: 600;
                    color: #111827;
                    margin-bottom: 0.5rem;
                }
                
                .card-description {
                    font-size: 0.875rem;
                    color: #6B7280;
                    line-height: 1.4;
                }
                
                .card-image {
                    width: 100%;
                    height: 500px;
                    object-fit: cover;
                    object-position: center;
                    border-radius: 0;
                }
                
                /* Consistent image sizing using aspect ratio */
                .image-container {
                    position: relative;
                    width: 100%;
                    height: 500px;
                    overflow: hidden;
                }
                
                .image-container img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    object-position: center;
                }
                
                /* Step indicators */
                .step-indicator {
                    position: absolute;
                    top: 1rem;
                    left: 1rem;
                    background: rgba(155, 116, 183, 0.9);
                    color: white;
                    padding: 0.5rem 1rem;
                    border-radius: 20px;
                    font-size: 0.875rem;
                    font-weight: 600;
                    backdrop-filter: blur(10px);
                }
                
                /* Global spacing using 8px grid system */
                .spacing-8 { margin: 0.5rem; }
                .spacing-16 { margin: 1rem; }
                .spacing-24 { margin: 1.5rem; }
                .spacing-32 { margin: 2rem; }
                
                /* Accessibility improvements */
                .journey-card:focus-within {
                    outline: 2px solid #9B74B7;
                    outline-offset: 2px;
                }
                
                /* Loading states */
                .card-image {
                    background: #f3f4f6;
                    transition: opacity 0.3s ease;
                }
                
                .card-image.loaded {
                    opacity: 1;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>See it in action</h1>
                    <p>Experience what thoughtful, intelligent therapy matching feels like. Our live demo walks you through a conversational matching experience that considers your unique identity, values, and needs — helping you understand how Therapair works before you commit.</p>
                </div>
                
                <div class="journey-grid">
                    <!-- Step 1: Quiz Question -->
                    <div class="journey-card">
                        <div class="card-header">
                            <h3 class="card-title">Step 1: Answer guided questions</h3>
                            <p class="card-description">Simple, thoughtful questions that get to know you and your needs</p>
                        </div>
                        <div class="image-container">
                            <div class="step-indicator">Step 1</div>
                            <img 
                                src="images/therapair-quiz-question.png" 
                                alt="Screenshot of Therapair matching quiz showing question interface"
                                class="card-image"
                                loading="lazy"
                            />
                        </div>
                    </div>
                    
                    <!-- Step 2: Results Cards -->
                    <div class="journey-card">
                        <div class="card-header">
                            <h3 class="card-title">Step 2: Get personalised matches</h3>
                            <p class="card-description">See 3 therapist matches who align with your preferences, identity, and therapeutic needs</p>
                        </div>
                        <div class="image-container">
                            <div class="step-indicator">Step 2</div>
                            <img 
                                src="images/therapair-results-3-cards.png" 
                                alt="Screenshot of Therapair results showing 3 matched therapist profiles with specialties and Book Now buttons"
                                class="card-image"
                                loading="lazy"
                            />
                        </div>
                    </div>
                    
                    <!-- Step 3: Booking Form -->
                    <div class="journey-card">
                        <div class="card-header">
                            <h3 class="card-title">Step 3: Book your session</h3>
                            <p class="card-description">Complete booking form with your preferences and contact information</p>
                        </div>
                        <div class="image-container">
                            <div class="step-indicator">Step 3</div>
                            <img 
                                src="images/therapair-booking-form.png" 
                                alt="Screenshot of Therapair booking form showing therapist details and appointment preferences"
                                class="card-image"
                                loading="lazy"
                            />
                        </div>
                    </div>
                </div>
                
                <!-- CTA Section -->
                <div class="text-center spacing-32">
                    <a 
                        href="https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/" 
                        target="_blank"
                        rel="noopener noreferrer"
                        style="
                            display: inline-block;
                            background: #9B74B7;
                            color: white;
                            padding: 1rem 2rem;
                            border-radius: 12px;
                            text-decoration: none;
                            font-weight: 600;
                            font-size: 1.125rem;
                            transition: all 0.2s ease;
                        "
                        onmouseover="this.style.background='#4F064F'; this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.background='#9B74B7'; this.style.transform='translateY(0)'"
                    >
                        Try the Demo
                    </a>
                    <p style="margin-top: 1rem; color: #6B7280; font-size: 0.875rem;">
                        This is a demo version of the experience currently being tested with Unison Mental Health
                    </p>
                </div>
            </div>
        </body>
        </html>
        `;
        
        await page.setContent(layoutHTML);
        await page.waitForTimeout(2000);
        
        // Take screenshot of the complete layout
        await page.screenshot({
            path: 'images/therapair-journey-layout.png',
            fullPage: true,
            type: 'png'
        });
        console.log('✅ Journey layout screenshot saved');
        
        // Also take individual card screenshots for reference
        const cards = await page.locator('.journey-card').all();
        for (let i = 0; i < cards.length; i++) {
            await cards[i].screenshot({
                path: `images/therapair-step-${i + 1}.png`,
                type: 'png'
            });
        }
        console.log('✅ Individual step screenshots saved');
        
        console.log('\n🎉 Journey layout generated successfully!');
        console.log('📸 Generated files:');
        console.log('   - images/therapair-journey-layout.png (complete layout)');
        console.log('   - images/therapair-step-1.png (quiz question)');
        console.log('   - images/therapair-step-2.png (results cards)');
        console.log('   - images/therapair-step-3.png (booking form)');
        
    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
        console.log('\n✅ Browser closed. All images ready!');
    }
}

console.log('═══════════════════════════════════════════════════════════');
console.log('  Therapair Journey Layout Generator                        ');
console.log('  (User-provided screenshots with consistent sizing)       ');
console.log('═══════════════════════════════════════════════════════════\n');

generateJourneyLayout().catch(console.error);
