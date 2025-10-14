import { chromium } from 'playwright';

// Therapist data for Adam, Natasha, and Emma
const therapistData = [
    {
        name: "Adam Forman",
        image: "images/adam.jpeg",
        tagline: "Experienced therapist specializing in relationship dynamics, ENM relationships, and attachment healing. I help couples and individuals navigate complex relationship structures with compassion and expertise.",
        specialties: ["ENM relationships", "Opening relationships", "Relationship dynamics", "Attachment healing", "Mediation"],
        accepting: true
    },
    {
        name: "Natasha Lama", 
        image: "images/natasha.jpeg",
        tagline: "Culturally sensitive therapist with expertise in sex therapy and sexual health. I provide affirming care for diverse communities, helping clients explore identity and relationships.",
        specialties: ["Sex therapy", "Cultural sensitivity", "Sexual health", "Cultural identity", "CBT", "Mindfulness"],
        accepting: true
    },
    {
        name: "Emma",
        image: "images/emma.jpeg", 
        tagline: "Trauma-informed therapist specializing in psychedelic integration and art therapy. I help clients process difficult experiences through evidence-based and creative approaches.",
        specialties: ["Psychedelic integration", "Trauma therapy", "DBT", "Art therapy", "Harm reduction", "Couples therapy"],
        accepting: true
    }
];

async function generate3CardLayout() {
    console.log('🎯 Generating 3-card layout with Adam, Natasha, and Emma...\n');
    
    const browser = await chromium.launch({ 
        headless: false,
        slowMo: 500
    });
    
    const context = await browser.newContext({
        viewport: { width: 1600, height: 2400 }
    });
    
    const page = await context.newPage();
    
    try {
        // Create HTML content with the 3 cards
        const htmlContent = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Therapair Results - 3 Cards</title>
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
                    max-width: 1200px;
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
                
                .results-section {
                    margin-bottom: 2rem;
                }
                
                .results-tier h3 {
                    color: #4F064F;
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin-bottom: 2rem;
                    text-align: center;
                }
                
                .therapist-cards-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 2rem;
                    margin-bottom: 2rem;
                }
                
                .therapist-card {
                    background: white;
                    border-radius: 24px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    overflow: hidden;
                    height: 720px;
                    display: flex;
                    flex-direction: column;
                    transition: all 0.3s ease;
                    position: relative;
                }
                
                .therapist-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
                }
                
                .card-image {
                    width: 100%;
                    height: 200px;
                    object-fit: cover;
                    border-radius: 0;
                }
                
                .card-content {
                    padding: 1.5rem;
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                }
                
                .therapist-name {
                    color: #111827;
                    font-size: 1.25rem;
                    font-weight: 600;
                    margin-bottom: 0.75rem;
                    line-height: 1.3;
                }
                
                .therapist-tagline {
                    color: #6B7280;
                    font-size: 0.875rem;
                    line-height: 1.4;
                    margin-bottom: 1rem;
                    flex: 1;
                }
                
                .specialties-section {
                    margin-bottom: 0.5rem;
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                }
                
                .specialties-label {
                    font-size: 0.875rem;
                    color: #6B7280;
                    margin-bottom: 0.5rem;
                    font-weight: 500;
                }
                
                .specialties-container {
                    display: flex;
                    flex-wrap: wrap;
                    align-items: flex-start;
                    align-content: flex-start;
                    margin-bottom: 0.5rem;
                }
                
                .specialty-tag {
                    background-color: #9B74B7;
                    color: white;
                    padding: 0.25rem 0.625rem;
                    border-radius: 9999px;
                    font-size: 0.7rem;
                    font-weight: 500;
                    margin: 0.15rem 0.15rem 0.15rem 0;
                    display: inline-block;
                }
                
                .card-buttons {
                    padding: 1rem 1.5rem 1.5rem;
                    margin-top: auto;
                }
                
                .btn-book {
                    width: 100%;
                    background-color: #9B74B7;
                    color: white;
                    border: none;
                    border-radius: 12px;
                    padding: 0.875rem 1.5rem;
                    font-size: 1rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .btn-book:hover {
                    background-color: #4F064F;
                    box-shadow: 0 4px 12px rgba(155, 116, 183, 0.25);
                }
                
                @media (max-width: 768px) {
                    .therapist-cards-grid {
                        grid-template-columns: 1fr;
                        gap: 1.5rem;
                    }
                    
                    .header h1 {
                        font-size: 2rem;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Best matches</h1>
                    <p>These therapists align closely with your preferences and therapeutic needs</p>
                </div>
                
                <div class="results-section">
                    <div class="results-tier">
                        <div class="therapist-cards-grid">
                            ${therapistData.map(therapist => `
                                <div class="therapist-card">
                                    <img src="${therapist.image}" alt="${therapist.name}" class="card-image">
                                    <div class="card-content">
                                        <h3 class="therapist-name">${therapist.name}</h3>
                                        <p class="therapist-tagline">${therapist.tagline}</p>
                                        
                                        <div class="specialties-section">
                                            <p class="specialties-label">Specializes in:</p>
                                            <div class="specialties-container">
                                                ${therapist.specialties.map(specialty => `
                                                    <span class="specialty-tag">${specialty}</span>
                                                `).join('')}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-buttons">
                                        <button class="btn-book">Book Now</button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        `;
        
        // Set the HTML content
        await page.setContent(htmlContent);
        
        console.log('✅ HTML content generated with 3 therapist cards');
        console.log('📸 Taking screenshot...');
        
        // Wait for images to load
        await page.waitForTimeout(2000);
        
        // Take screenshot of the entire page
        await page.screenshot({
            path: 'images/therapair-results-3-cards.png',
            fullPage: true,
            type: 'png'
        });
        
        console.log('✅ Screenshot saved: images/therapair-results-3-cards.png');
        
        // Also take a screenshot of just the cards section
        const cardsSection = page.locator('.therapist-cards-grid');
        await cardsSection.screenshot({
            path: 'images/therapair-results-cards-only.png',
            type: 'png'
        });
        
        console.log('✅ Cards-only screenshot saved: images/therapair-results-cards-only.png');
        
        console.log('\n🎉 3-card layout generated successfully!');
        console.log('💡 Browser will stay open for 5 seconds for inspection...');
        await page.waitForTimeout(5000);
        
    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
        console.log('\n✅ Browser closed. Check the generated images!');
    }
}

console.log('═══════════════════════════════════════════════════════════');
console.log('  Therapair 3-Card Layout Generator (Adam, Natasha, Emma)  ');
console.log('═══════════════════════════════════════════════════════════\n');

generate3CardLayout().catch(console.error);
