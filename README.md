# Therapair Landing Page

> Smart therapy matching for inclusive, culturally competent mental health care

**Live Site:** https://therapair.com.au  
**Status:** Production (MVP Phase)  
**Last Updated:** October 2025

---

## 🎯 **Quick Overview**

Therapair is an intelligent therapist matching platform focused on inclusive mental health care for LGBTQ+, neurodivergent, and culturally diverse communities across Australia.

### **Current Status:**
- ✅ Landing page live with interactive demo
- ✅ Legal documentation complete (Privacy, Terms, Therapist Terms)
- ✅ Victorian Therapists database ready (193 verified therapists)
- ✅ Onboarding system designed and documented
- 🔄 MVP phase: Building therapist directory

---

## 📁 **Project Structure**

```
therapair-landing-page/
├── index.html                 # Main landing page
├── documentation.html         # Documentation hub
├── privacy-request.html       # Privacy request form
├── thank-you.html            # Form confirmation page
├── email-preferences.html    # Email preferences
│
├── /legal                    # Legal documents
│   ├── privacy-policy.html
│   ├── terms-and-conditions.html
│   ├── therapist-terms.html
│   └── consent-removal.html
│
├── /images                   # All image assets
│   ├── /journey             # Quiz/results screenshots
│   ├── /optimized           # Optimized therapist photos
│   └── /resized             # Resized images
│
├── /docs                     # Documentation
│   ├── /onboarding          # Therapist onboarding guides
│   ├── /database            # Database setup & management
│   ├── /deployment          # Deployment guides
│   └── /archive             # Old versions & completed work
│
├── /scripts                  # Automation scripts
│   ├── /notion              # Database management
│   ├── /screenshots         # Screenshot generation
│   └── /deployment          # Deployment scripts
│
├── /tests                    # Playwright tests
│   └── /*.spec.js
│
├── package.json              # Dependencies
├── playwright.config.js      # Test configuration
└── README.md                 # This file
```

---

## 🚀 **Quick Start**

### **For Development:**

```bash
# Clone repository
git clone https://github.com/captuspario/therapair-landing.git
cd therapair-landing-page

# Install dependencies
npm install

# Run locally
open index.html
# or use a local server
npx serve .
```

### **For Deployment:**

```bash
# Deploy to Hostinger
./scripts/deployment/deploy-to-hostinger.sh
```

---

## 📚 **Documentation**

### **Core Guides:**

| Guide | Location | Purpose |
|-------|----------|---------|
| **Database Setup** | `/docs/database/NOTION-SETUP.md` | Set up Notion databases |
| **Database Management** | `/docs/database/DATABASE-GUIDE.md` | Manage 202 therapists |
| **Onboarding Journey** | `/docs/onboarding/ONBOARDING-JOURNEY-PLAN.md` | Therapist onboarding flow |
| **Profile Template** | `/docs/onboarding/NOTION-PROFILE-PAGE-TEMPLATE.md` | One-click profile views |
| **Deployment** | `/docs/deployment/` | Deploy to production |

### **Quick Links:**

- 📖 [Full Documentation Hub](https://therapair.com.au/documentation.html)
- 🔒 [Privacy Policy](https://therapair.com.au/legal/privacy-policy.html)
- 📋 [Therapist Terms](https://therapair.com.au/legal/therapist-terms.html)
- 🌐 [Live Demo](https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/)

---

## 🗄️ **Database Status**

### **Victorian Therapists Database**

**Platform:** Notion  
**Database ID:** `28c5c25944da80a48d85fd43119f4ec1`  
**Total:** 202 therapists  
**Valid:** 193 (with email addresses)  
**Status:** Cleaned, optimized, ready for onboarding

**Features:**
- ✅ All names parsed (First + Last)
- ✅ 202 unique onboarding tokens generated
- ✅ 202 profile URLs created
- ✅ 29 phone numbers formatted
- ✅ 74 social media accounts extracted
- ✅ Regions standardized
- ✅ Ready for national expansion

**Next Steps:**
- Send onboarding invitations
- Verify therapist profiles
- Publish to website

---

## 🛠️ **Tech Stack**

### **Frontend:**
- HTML5, CSS3 (Tailwind CSS)
- Vanilla JavaScript
- Lucide Icons
- Google Analytics

### **Backend:**
- PHP (form handling)
- NodeMailer (email sending)
- Notion API (database)

### **Tools:**
- Playwright (testing & screenshots)
- Git (version control)
- Hostinger (hosting)

### **APIs:**
- Notion API (database management)
- FormSubmit.co (form fallback)
- Google Analytics (tracking)

---

## 📧 **Email System**

### **Form Submissions:**
- Admin notification (contact@therapair.com.au)
- User confirmation (personalized)
- Optional: AI-powered personalization via OpenAI
- Notion sync for tracking

### **Onboarding Invitations:**
- Personalized with therapist name
- Secure magic link with token
- 30-day expiry
- Tracked in Notion

---

## 🔐 **Security & Privacy**

- ✅ Australian Privacy Act 1988 compliant
- ✅ HTTPS only
- ✅ Token-based authentication for therapist onboarding
- ✅ No passwords stored
- ✅ IP logging for security
- ✅ Form validation and sanitization
- ✅ Notion tokens in .env (not in git)

**Security Note:** All sensitive credentials are in `.env` and `config.php`, which are gitignored.

---

## 🧪 **Testing**

### **Run Playwright Tests:**

```bash
# All tests
npx playwright test

# Specific test
npx playwright test tests/form-icons.spec.js

# With UI
npx playwright test --ui
```

### **Screenshot Generation:**

```bash
# Generate all journey screenshots
node scripts/screenshots/generate-all-screenshots.js

# Generate specific layouts
node scripts/screenshots/generate-journey-layout.js
```

---

## 📦 **Deployment**

### **Deploy to Hostinger:**

```bash
# Full deployment (pulls from GitHub on server)
./scripts/deployment/deploy-to-hostinger.sh
```

### **What Happens:**
1. Commits pushed to GitHub
2. SSH to Hostinger server
3. Pull latest from GitHub
4. Updates live at therapair.com.au

**Deployment URL:** https://therapair.com.au

---

## 📊 **Current Metrics**

### **Website:**
- Landing page with interactive demo
- Comprehensive documentation hub
- Legal pages (privacy, terms, therapist terms)
- Crisis support resources (Lifeline, Beyond Blue, etc.)
- Request early access form

### **Database:**
- 202 Victorian therapists imported
- 193 valid (with email for onboarding)
- 74 social media accounts linked
- 29 phone numbers available
- All regions standardized

### **Documentation:**
- 40+ markdown files
- Organized in /docs/ folders
- Complete guides for all processes
- Archive of completed work

---

## 🗂️ **File Organization**

### **Root Level (Essential Files Only):**
- `index.html` - Landing page
- `documentation.html` - Docs hub
- `privacy-request.html` - Privacy form
- `thank-you.html` - Confirmation page
- `email-preferences.html` - Email preferences
- `README.md` - This file
- `package.json` - Dependencies
- `playwright.config.js` - Test config

### **Organized Folders:**
- `/docs` - All documentation (onboarding, database, deployment, archive)
- `/legal` - Legal pages
- `/images` - All images organized by type
- `/scripts` - All automation scripts
- `/tests` - Playwright tests
- `/node_modules` - Dependencies

---

## 🎯 **Next Steps**

### **Immediate (This Week):**
1. [ ] Create Notion profile page template
2. [ ] Reorder database columns manually
3. [ ] Select pilot group (10-20 therapists)
4. [ ] Send first onboarding invitations

### **Short Term (Next 2-4 Weeks):**
5. [ ] Build therapist onboarding page (/onboarding/{token})
6. [ ] Create therapist profile pages
7. [ ] Verify and publish first batch
8. [ ] Launch therapist directory

### **Medium Term (2-3 Months):**
9. [ ] Expand to NSW, QLD
10. [ ] Build admin dashboard
11. [ ] Implement real-time matching
12. [ ] Scale to 500+ therapists

---

## 🤝 **Contributing**

This is a private repository for Therapair development.

### **Development Workflow:**
1. Create feature branch
2. Make changes
3. Test locally
4. Commit with clear messages
5. Push to GitHub
6. Deploy via script

---

## 📞 **Contact**

- **Email:** contact@therapair.com.au
- **Website:** https://therapair.com.au
- **GitHub:** https://github.com/captuspario/therapair-landing

---

## 📄 **License**

© 2025 Therapair. All rights reserved.

---

## 🔄 **Changelog**

See git commit history for detailed changes.

### **Recent Major Updates:**

**October 2025:**
- ✅ Complete file structure reorganization
- ✅ Victorian Therapists database (202 entries)
- ✅ Social media extraction (74 accounts)
- ✅ Phone number formatting (29 numbers)
- ✅ National scalability optimization
- ✅ Profile page template system
- ✅ Comprehensive documentation

**September 2025:**
- ✅ Legal documentation complete
- ✅ Documentation hub launched
- ✅ Crisis support resources added
- ✅ Privacy request form system

---

## ✅ **Project Status: Production Ready**

- 🟢 Landing page
- 🟢 Legal documentation
- 🟢 Form submissions
- 🟢 Database management
- 🟡 Therapist onboarding (designed, building)
- 🟡 Public directory (planned)
- ⚪ Matching algorithm (future)

**MVP Target:** Launch with 20-50 verified therapists by end of 2025

---

For detailed guides, see `/docs/` folder or visit the [documentation hub](https://therapair.com.au/documentation.html).