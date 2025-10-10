# Therapair Landing Page

**Intelligent therapy matching starts here.**

> This is the main landing page and interest capture system for Therapair, an AI-powered platform that matches individuals with therapists who truly understand their identity, values, and needs.

---

## 🌐 Live Site

**URL:** https://therapair.com.au

---

## 📋 What This Is

The Therapair landing page serves as:

1. **Brand Presence** - Professional introduction to Therapair
2. **Lead Generation** - Capture interest from 4 key audiences:
   - Individuals seeking therapy
   - Mental health practitioners
   - Clinics & organisations
   - Supporters & investors
3. **Demo Showcase** - Link to live widget on Unison Mental Health
4. **Email Automation** - AI-powered personalized confirmation emails
5. **CRM Integration** - Notion database sync for lead management

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+ (for form handling)
- Hostinger account (or similar PHP hosting)
- OpenAI API key
- Notion API integration

### Local Development

```bash
# Clone the repository
git clone <repo-url>
cd therapair-landing-page

# Open in browser (requires local PHP server)
php -S localhost:8000

# Visit http://localhost:8000
```

### Configuration

Create `config.php` (ignored by Git):

```php
<?php
// OpenAI Configuration
define('OPENAI_API_KEY', 'sk-...');

// Email Configuration  
define('ADMIN_EMAIL', 'your-email@domain.com');
define('FROM_EMAIL', 'hello@therapair.com.au');
define('FROM_NAME', 'Therapair Team');

// Notion Configuration
define('NOTION_API_KEY', 'secret_...');
define('NOTION_DATABASE_ID', 'your-database-id');
?>
```

---

## 📁 Project Structure

```
therapair-landing-page/
├── index.html                    # Main landing page
├── thank-you.html               # Post-submission confirmation
├── submit-form.php              # Form handler + AI email generator
├── notion-sync.php              # Notion API integration (MOVED to docs/)
├── config.php                   # Environment variables (gitignored)
│
├── images/                      # Assets & screenshots
│   ├── therapair-quiz-question.png
│   └── therapair-results-full.png
│
└── docs/                        # Documentation hub
    ├── README.md                # Documentation index
    ├── EXECUTIVE-SUMMARY.md     # Business strategy & vision
    │
    ├── technical/
    │   ├── ARCHITECTURE.md      # System architecture
    │   └── email-ai-prompt.md   # AI email prompt template
    │
    ├── planning/
    │   └── PRODUCT-ROADMAP.md   # 5-year product plan
    │
    ├── guides/
    │   └── notion-database-setup.md  # CRM setup instructions
    │
    └── archive/                 # Historical docs
```

---

## 🎯 Key Features

### 1. Dynamic Form System
- **4 Audience Types** with unique form fields
- Client-side validation
- Honeypot spam protection
- Mobile-responsive design

### 2. AI-Powered Emails
- GPT-4 generates personalized confirmations
- Tone: Professional, warm, conversational
- Australian English localization
- Fallback templates if API fails

### 3. Notion CRM Integration
- Automatic lead creation
- Audience segmentation
- Email preference management
- Status tracking

### 4. Visual Demo Section
- Screenshot previews of quiz + results
- High-conversion CTA design
- Links to live demo on partner site

---

## 🛠️ Tech Stack

**Frontend:**
- HTML5, CSS3 (utility-first approach)
- Vanilla JavaScript (no frameworks)
- Lucide icons
- Google Fonts (Open Sans)

**Backend:**
- PHP 7.4+ (form handling)
- OpenAI API (email generation)
- Notion API (CRM sync)
- PHP mail() (email delivery)

**Hosting:**
- Hostinger (shared hosting)
- Git deployment
- SSL/HTTPS enabled

---

## 📊 Form Flow

```
User visits therapair.com.au
    │
    ▼
Selects audience type (Individual/Practitioner/Clinic/Supporter)
    │
    ▼
Form fields dynamically update
    │
    ▼
User fills form + submits
    │
    ▼
POST to submit-form.php
    │
    ├──▶ Send admin notification (immediate)
    │
    ├──▶ Call OpenAI API (2-5 seconds)
    │    └──▶ Generate personalized email
    │
    ├──▶ Send user confirmation
    │
    ├──▶ Sync to Notion (parallel)
    │    └──▶ Create database entry
    │
    └──▶ Redirect to thank-you.html
```

---

## 🔐 Security & Privacy

### What We Do
- ✅ Input sanitization (`htmlspecialchars`, `trim`)
- ✅ Email validation (`FILTER_VALIDATE_EMAIL`)
- ✅ Honeypot spam protection
- ✅ HTTPS enforcement
- ✅ API keys in gitignored `config.php`
- ✅ No PHI (Protected Health Information) collected

### What We Don't Do
- ❌ Store payment information
- ❌ Track users with analytics pixels (yet)
- ❌ Sell or share user data
- ❌ Store session recordings

---

## 📧 Email System

### Admin Notifications
**To:** `tino@unisoncounselling.com`  
**Format:** Professional HTML with:
- User details (name, email, phone, location)
- Audience type
- Form responses
- Submission timestamp

### User Confirmations
**From:** `hello@therapair.com.au` (Therapair Team)  
**Format:** AI-generated HTML with:
- Personalized greeting
- Acknowledgment of their specific interests/concerns
- Next steps (what to expect)
- Contact information
- Unsubscribe link

**AI Prompt:** See [`docs/technical/email-ai-prompt.md`](./docs/technical/email-ai-prompt.md)

---

## 🎨 Design System

### Colors
```css
--therapair-primary: #4F064F;    /* Deep purple */
--therapair-secondary: #9B74B7;  /* Medium purple */
--therapair-accent: #06B6D4;     /* Cyan */
--therapair-success: #10B981;    /* Green */
--therapair-gray: #64748b;       /* Slate gray */
--therapair-background: #FEFEFF; /* Off-white */
```

### Typography
- **Font:** Open Sans (Google Fonts)
- **Headings:** 600-700 weight
- **Body:** 400-500 weight
- **Line Height:** 1.6-1.8

### Spacing
- **Sections:** `py-20` to `py-24` (80-96px)
- **Cards:** `p-8` to `p-12` (32-48px)
- **Elements:** `mb-4` to `mb-6` (16-24px)

---

## 🚢 Deployment

### Automatic (Git Push)

```bash
git add .
git commit -m "feat: description"
git push origin main

# Hostinger auto-deploys from Git
```

### Manual (FTP/SSH)

If Git deployment fails:
1. Connect to Hostinger via FTP
2. Upload changed files to `public_html/`
3. Verify `config.php` is present (not in Git)
4. Test form submission

### Deployment Checklist
- [ ] Test form locally
- [ ] Commit changes to Git
- [ ] Push to remote
- [ ] Verify live site loads
- [ ] Test form submission on production
- [ ] Check admin email received
- [ ] Check user confirmation email received
- [ ] Verify Notion sync (check database)

---

## 🧪 Testing

### Manual Testing Checklist

**Form Validation:**
- [ ] Required fields show error if empty
- [ ] Email validation works
- [ ] Phone validation (optional field)
- [ ] Dynamic fields update when audience changes
- [ ] Honeypot prevents spam bots

**Email Delivery:**
- [ ] Admin receives notification
- [ ] User receives confirmation
- [ ] Emails not in spam (check headers)
- [ ] AI personalization works (mentions user's concerns)
- [ ] Fallback template works if OpenAI fails

**Notion Sync:**
- [ ] New entry created in database
- [ ] Correct audience type set
- [ ] All form fields mapped properly
- [ ] Email preferences recorded

**Mobile Responsiveness:**
- [ ] Form fields stack on mobile
- [ ] Buttons are tappable
- [ ] Text is readable
- [ ] Images scale properly

---

## 📈 Analytics (Future)

### Planned Metrics
- Form views
- Form submissions
- Completion rate (%)
- Audience type breakdown
- Email open rates
- Email click-through rates

### Implementation (Phase 2)
- Google Analytics 4
- Mixpanel for conversion funnels
- SendGrid for email tracking

---

## 🐛 Known Issues & TODOs

### High Priority
- [ ] Migrate from PHP mail() to SendGrid (better deliverability)
- [ ] Add rate limiting (prevent spam)
- [ ] Implement CSRF tokens
- [ ] Add Google Analytics

### Medium Priority
- [ ] A/B test form fields
- [ ] Add reCAPTCHA v3
- [ ] Optimize images (WebP format)
- [ ] Add loading state to submit button

### Low Priority
- [ ] Add dark mode toggle
- [ ] Animate on scroll (AOS library)
- [ ] Add FAQ section
- [ ] Multi-language support

---

## 📚 Documentation

**Full documentation is available in [`docs/`](./docs/README.md)**

### Key Documents:
- [Executive Summary](./docs/EXECUTIVE-SUMMARY.md) - Vision, strategy, roadmap
- [Technical Architecture](./docs/technical/ARCHITECTURE.md) - System design
- [Product Roadmap](./docs/planning/PRODUCT-ROADMAP.md) - Feature timeline
- [Notion Setup Guide](./docs/guides/notion-database-setup.md) - CRM configuration

---

## 🤝 Contributing

This is currently a solo project (Tino), but if you're collaborating:

1. Create a feature branch: `git checkout -b feature/your-feature`
2. Make your changes
3. Test thoroughly (form submission, emails, Notion sync)
4. Commit with clear messages: `git commit -m "feat: description"`
5. Push and create PR: `git push origin feature/your-feature`

### Commit Message Format
```
feat: Add new audience type form
fix: Correct email validation regex
docs: Update deployment guide
style: Improve mobile responsiveness
refactor: Simplify form validation logic
```

---

## 📞 Support & Contact

**Technical Issues:**
- Check [`docs/technical/ARCHITECTURE.md`](./docs/technical/ARCHITECTURE.md)
- Review server logs (Hostinger → File Manager → `error_log`)
- Email: tino@unisoncounselling.com

**Business Inquiries:**
- Email: tino@unisoncounselling.com
- Website: https://therapair.com.au

---

## 📜 License

Proprietary - All Rights Reserved  
© 2025 Therapair

---

## 🙏 Acknowledgments

- **Unison Mental Health** - First partnership, demo hosting
- **OpenAI** - GPT-4 API for email generation
- **Notion** - CRM infrastructure
- **Hostinger** - Reliable hosting

---

## 🗺️ Roadmap

**Current Phase: MVP (Q4 2025)** ✅
- [x] Landing page live
- [x] AI email automation working
- [x] Notion CRM integrated
- [x] Demo widget on Unison site

**Next Phase: Validation (Q1-Q2 2026)**
- [ ] 100+ users tested matching quiz
- [ ] 3-5 clinic pilot agreements
- [ ] User research interviews
- [ ] Match quality tracking

**See full roadmap:** [`docs/planning/PRODUCT-ROADMAP.md`](./docs/planning/PRODUCT-ROADMAP.md)

---

*Built with ❤️ and AI by Tino*  
*Last Updated: October 10, 2025*
