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

async function generateAllScreenshots() {
    console.log('🎯 Generating all screenshots: Quiz, Results, and Booking Form...\n');
    
    const browser = await chromium.launch({ 
        headless: false,
        slowMo: 500
    });
    
    const context = await browser.newContext({
        viewport: { width: 1600, height: 2400 }
    });
    
    const page = await context.newPage();
    
    try {
        // 1. Generate Quiz Question Screenshot
        console.log('📝 Generating Quiz Question Screenshot...');
        const quizHTML = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Therapair Quiz Question</title>
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
                    max-width: 800px;
                    margin: 0 auto;
                }
                
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 3rem;
                }
                
                .logo {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                    font-weight: 700;
                    color: #4F064F;
                    font-size: 1.25rem;
                }
                
                .logo-icon {
                    width: 32px;
                    height: 32px;
                    background: #9B74B7;
                    border-radius: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-weight: bold;
                }
                
                .nav {
                    display: flex;
                    gap: 2rem;
                    align-items: center;
                }
                
                .nav a {
                    color: #6B7280;
                    text-decoration: none;
                    font-weight: 500;
                }
                
                .nav .enquire-btn {
                    background: #9B74B7;
                    color: white;
                    padding: 0.5rem 1rem;
                    border-radius: 8px;
                    text-decoration: none;
                }
                
                .main-content {
                    text-align: center;
                    margin-bottom: 3rem;
                }
                
                .main-content h1 {
                    color: #111827;
                    font-size: 2.5rem;
                    font-weight: 700;
                    margin-bottom: 1rem;
                }
                
                .main-content p {
                    color: #6B7280;
                    font-size: 1.125rem;
                    max-width: 600px;
                    margin: 0 auto;
                }
                
                .quiz-card {
                    background: white;
                    border-radius: 24px;
                    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                    padding: 2rem;
                    max-width: 600px;
                    margin: 0 auto;
                    border: 2px solid rgba(155, 116, 183, 0.1);
                }
                
                .progress-container {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 2rem;
                }
                
                .progress-dots {
                    display: flex;
                    gap: 0.5rem;
                }
                
                .progress-dot {
                    width: 12px;
                    height: 12px;
                    border-radius: 50%;
                    background: #e5e7eb;
                }
                
                .progress-dot.active {
                    background: #9B74B7;
                    transform: scale(1.2);
                }
                
                .progress-text {
                    color: #6B7280;
                    font-size: 0.875rem;
                    font-weight: 500;
                }
                
                .question {
                    color: #111827;
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin-bottom: 2rem;
                    text-align: left;
                }
                
                .options {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                }
                
                .option-button {
                    background: #9B74B7;
                    color: white;
                    border: none;
                    border-radius: 25px;
                    padding: 1rem 1.5rem;
                    font-size: 1rem;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    text-align: left;
                }
                
                .option-button:hover {
                    background: #4F064F;
                    transform: translateY(-2px);
                }
                
                .option-button.selected {
                    background: #4F064F;
                    transform: translateY(-2px);
                }
                
                .footer {
                    text-align: center;
                    margin-top: 3rem;
                    color: #6B7280;
                    font-size: 0.875rem;
                }
                
                .footer .powered-by {
                    margin-bottom: 0.5rem;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="logo">
                        <div class="logo-icon">U</div>
                        Unison MENTAL HEALTH
                    </div>
                    <nav class="nav">
                        <a href="#">Our Services</a>
                        <a href="#">Our Team</a>
                        <a href="#">Fees</a>
                        <a href="#">Resources</a>
                        <a href="#" class="enquire-btn">Enquire</a>
                    </nav>
                </div>
                
                <div class="main-content">
                    <h1>Let's find a therapist who's right for you</h1>
                    <p>Answer a few quick questions - we'll guide you to someone who really gets it.</p>
                </div>
                
                <div class="quiz-card">
                    <div class="progress-container">
                        <div class="progress-dots">
                            <div class="progress-dot active"></div>
                            <div class="progress-dot"></div>
                            <div class="progress-dot"></div>
                            <div class="progress-dot"></div>
                            <div class="progress-dot"></div>
                            <div class="progress-dot"></div>
                            <div class="progress-dot"></div>
                            <div class="progress-dot"></div>
                            <div class="progress-dot"></div>
                        </div>
                        <div class="progress-text">1 of 9</div>
                    </div>
                    
                    <h2 class="question">Who is seeking therapy?</h2>
                    
                    <div class="options">
                        <button class="option-button selected">For myself</button>
                        <button class="option-button">For myself and my partner(s)</button>
                        <button class="option-button">For someone else</button>
                    </div>
                </div>
                
                <div class="footer">
                    <div class="powered-by">Powered by Therapair for Unison Mental Health</div>
                    <div>Find mental health support that understands you.</div>
                </div>
            </div>
        </body>
        </html>
        `;
        
        await page.setContent(quizHTML);
        await page.waitForTimeout(1000);
        await page.screenshot({
            path: 'images/therapair-quiz-question.png',
            fullPage: true,
            type: 'png'
        });
        console.log('✅ Quiz question screenshot saved');
        
        // 2. Generate Results Cards Screenshot
        console.log('📊 Generating Results Cards Screenshot...');
        const resultsHTML = `
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
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 1.5rem;
                    margin-bottom: 2rem;
                }
                
                @media (min-width: 1200px) {
                    .therapist-cards-grid {
                        grid-template-columns: repeat(3, minmax(300px, 380px));
                        justify-content: center;
                        gap: 2rem;
                    }
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
                    margin-bottom: 0.25rem;
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
                    margin-bottom: 0.25rem;
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
        
        await page.setContent(resultsHTML);
        await page.waitForTimeout(1000);
        await page.screenshot({
            path: 'images/therapair-results-3-cards.png',
            fullPage: true,
            type: 'png'
        });
        console.log('✅ Results cards screenshot saved');
        
        // 3. Generate Booking Form Screenshot
        console.log('📅 Generating Booking Form Screenshot...');
        const bookingHTML = `
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Therapair Booking Form</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                    background-attachment: fixed;
                    padding: 2rem;
                    min-height: 100vh;
                    overflow-y: auto;
                }
                
                .container {
                    max-width: 800px;
                    margin: 0 auto;
                }
                
                .booking-modal {
                    background: white;
                    border-radius: 24px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                    padding: 2rem;
                    border: 2px solid rgba(155, 116, 183, 0.1);
                }
                
                .modal-header {
                    display: flex;
                    align-items: flex-start;
                    gap: 1rem;
                    margin-bottom: 2rem;
                    padding-bottom: 1.5rem;
                    border-bottom: 1px solid #e5e7eb;
                }
                
                .therapist-image {
                    width: 120px;
                    height: 120px;
                    border-radius: 12px;
                    object-fit: cover;
                    flex-shrink: 0;
                }
                
                .therapist-info h3 {
                    color: #111827;
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin-bottom: 0.5rem;
                }
                
                .therapist-info p {
                    color: #6B7280;
                    font-size: 0.875rem;
                    margin-bottom: 0.5rem;
                }
                
                .specialties {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                    margin-top: 0.5rem;
                }
                
                .specialty-tag {
                    background-color: #9B74B7;
                    color: white;
                    padding: 0.25rem 0.5rem;
                    border-radius: 9999px;
                    font-size: 0.7rem;
                    font-weight: 500;
                }
                
                .form-section {
                    margin-bottom: 2rem;
                }
                
                .form-section h3 {
                    color: #111827;
                    font-size: 1.25rem;
                    font-weight: 600;
                    margin-bottom: 1rem;
                }
                
                .form-group {
                    margin-bottom: 1rem;
                }
                
                .form-group label {
                    display: block;
                    margin-bottom: 0.5rem;
                    font-weight: 500;
                    color: #111827;
                    font-size: 0.875rem;
                }
                
                .form-group input,
                .form-group select,
                .form-group textarea {
                    width: 100%;
                    padding: 0.75rem;
                    border: 1px solid #d1d5db;
                    border-radius: 8px;
                    font-size: 0.875rem;
                    background: #f9fafb;
                    transition: border-color 0.2s ease;
                }
                
                .form-group input:focus,
                .form-group select:focus,
                .form-group textarea:focus {
                    outline: none;
                    border-color: #9B74B7;
                    background: white;
                }
                
                .form-group textarea {
                    min-height: 100px;
                    resize: vertical;
                }
                
                .preferences-summary {
                    background: #f9fafb;
                    border-radius: 12px;
                    padding: 1.5rem;
                    margin-bottom: 2rem;
                }
                
                .preferences-summary h4 {
                    color: #4F064F;
                    margin-bottom: 1rem;
                    font-size: 1.1rem;
                }
                
                .preferences-summary p {
                    color: #374151;
                    margin-bottom: 0.5rem;
                    font-size: 0.875rem;
                }
                
                .form-actions {
                    display: flex;
                    gap: 1rem;
                    justify-content: flex-end;
                    margin-top: 2rem;
                }
                
                .btn {
                    padding: 0.75rem 1.5rem;
                    border-radius: 8px;
                    font-size: 0.875rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    border: none;
                }
                
                .btn-secondary {
                    background: #f3f4f6;
                    color: #374151;
                }
                
                .btn-secondary:hover {
                    background: #e5e7eb;
                }
                
                .btn-primary {
                    background: #9B74B7;
                    color: white;
                }
                
                .btn-primary:hover {
                    background: #4F064F;
                }
                
                .required {
                    color: #ef4444;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="booking-modal">
                    <div class="modal-header">
                        <img src="images/adam.jpeg" alt="Adam Forman" class="therapist-image">
                        <div class="therapist-info">
                            <h3>Book with Adam Forman</h3>
                            <p>Experienced therapist specializing in relationship dynamics, ENM relationships, and attachment healing.</p>
                            <div class="specialties">
                                <span class="specialty-tag">ENM relationships</span>
                                <span class="specialty-tag">Attachment healing</span>
                                <span class="specialty-tag">Mediation</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="preferences-summary">
                        <h4>Your Preferences Summary</h4>
                        <p><strong>Seeking therapy for:</strong> Myself</p>
                        <p><strong>Preferred approach:</strong> Person-centered, CBT</p>
                        <p><strong>Specialties needed:</strong> Relationship dynamics, ENM relationships</p>
                        <p><strong>Selected Therapist:</strong> Adam Forman</p>
                        <p><strong>Specializes in:</strong> ENM relationships, Attachment healing, Mediation</p>
                    </div>
                    
                    <div class="form-section">
                        <h3>Your Contact Information</h3>
                        <div class="form-group">
                            <label for="fullName">Full Name <span class="required">*</span></label>
                            <input type="text" id="fullName" placeholder="Enter your full name" value="Sarah Johnson">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address <span class="required">*</span></label>
                            <input type="email" id="email" placeholder="Enter your email address" value="sarah.johnson@email.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" placeholder="Enter your phone number" value="+61 400 123 456">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Appointment Preferences</h3>
                        <div class="form-group">
                            <label for="preferredTime">Preferred Time</label>
                            <select id="preferredTime">
                                <option value="">Select preferred time</option>
                                <option value="morning" selected>Morning (9am - 12pm)</option>
                                <option value="afternoon">Afternoon (12pm - 5pm)</option>
                                <option value="evening">Evening (5pm - 8pm)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="preferredDay">Preferred Day</label>
                            <select id="preferredDay">
                                <option value="">Select preferred day</option>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday" selected>Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="sessionType">Session Type</label>
                            <select id="sessionType">
                                <option value="individual" selected>Individual Session</option>
                                <option value="couples">Couples Session</option>
                                <option value="group">Group Session</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Additional Information</h3>
                        <div class="form-group">
                            <label for="message">Message (Optional)</label>
                            <textarea id="message" placeholder="Tell us about your goals or any specific needs...">I'm looking forward to working with Adam on relationship dynamics and attachment healing. I have experience with ENM relationships and would like to explore this further in therapy.</textarea>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button class="btn btn-secondary">Cancel</button>
                        <button class="btn btn-primary">Submit Booking Request</button>
                    </div>
                </div>
            </div>
        </body>
        </html>
        `;
        
        await page.setContent(bookingHTML);
        await page.waitForTimeout(1000);
        await page.screenshot({
            path: 'images/therapair-booking-form.png',
            fullPage: true,
            type: 'png'
        });
        console.log('✅ Booking form screenshot saved');
        
        console.log('\n🎉 All screenshots generated successfully!');
        console.log('📸 Generated files:');
        console.log('   - images/therapair-quiz-question.png');
        console.log('   - images/therapair-results-3-cards.png');
        console.log('   - images/therapair-booking-form.png');
        
    } catch (error) {
        console.error('❌ Error:', error.message);
    } finally {
        await browser.close();
        console.log('\n✅ Browser closed. All screenshots ready!');
    }
}

console.log('═══════════════════════════════════════════════════════════');
console.log('  Therapair Complete Screenshot Generator                    ');
console.log('  (Quiz Question + Results Cards + Booking Form)            ');
console.log('═══════════════════════════════════════════════════════════\n');

generateAllScreenshots().catch(console.error);
