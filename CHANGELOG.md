# Changelog

All notable changes to the Therapair Landing Page project.

---

## [2.0.0] - 2025-10-14

### 🎉 Major Release: Database & File Structure Optimization

#### Added
- **Victorian Therapists Database** (202 therapists in Notion)
  - Complete data cleanup and optimization
  - 193 valid therapists with email addresses
  - 74 social media accounts extracted
  - 29 phone numbers formatted
  - National scalability fields added
- **Notion Profile Page Template System**
  - One-click beautiful profile views
  - Organized sections for all data
  - Verification workflow support
- **Comprehensive File Organization**
  - `/docs` folder with subcategories
  - `/scripts` folder for automation
  - `/images` organized by type
  - Merged duplicate documentation
- **Database Scalability**
  - State field (ready for NSW, QLD, etc.)
  - Service Type (In-Person, Online, Phone)
  - Primary Profession (structured)
  - Modalities, Client Age Groups
  - Registration Number field
  - International-ready structure

#### Changed
- **File Structure** - Reorganized from 75+ root files to clean folder structure
- **Documentation** - Merged 15+ similar docs into 5 comprehensive guides
- **Database Fields** - Added 27 new properties for scalability
- **Social Media** - Extracted to dedicated columns (Instagram, Facebook, Twitter/X, LinkedIn)
- **Phone Formatting** - Standardized Australian phone number formats
- **Column Names** - Cleaner, shorter names following best practices

#### Removed
- Old screenshot files (19 files deleted)
- Duplicate documentation (12 merged files)
- Unused HTML preview files (6 files)
- Old script versions (9 files archived)
- Log files (moved to archive)

#### Fixed
- Footer navigation links (all working)
- GitHub repository links (correct URLs)
- Search functionality in documentation
- Request Early Access button (proper anchor link)
- Language field cleanup (removed English, N/A, None)
- Email validation (9 entries archived without emails)

---

## [1.5.0] - 2025-10-13

### Journey Screenshots & UI Improvements

#### Added
- Three journey screenshots (quiz, results, booking)
- Automated screenshot generation with Playwright
- User-provided screenshot integration
- Consistent image sizing and layout

#### Changed
- "See it in action" section to 3-column grid
- Image display with consistent 500px height
- Updated Unison widget spacing
- Result cards layout for 3-card display

---

## [1.4.0] - 2025-10-12

### Legal Documentation & Footer Enhancement

#### Added
- Privacy Policy (8,000 words, Australian law compliant)
- Terms & Conditions (7,000 words)
- Therapist Terms of Participation (6,000 words)
- Consent & Removal Policy (4,500 words)
- Privacy Request Form (form-based, not email)
- Crisis Support Links (Lifeline, Beyond Blue, Kids Helpline, 1800RESPECT)
- Comprehensive 5-column footer with 40+ links

#### Changed
- All AI mentions replaced with "smart" service
- City mentions removed (Melbourne, Sydney) - kept national
- Email links replaced with form submissions
- Footer layout to accommodate crisis support

---

## [1.3.0] - 2025-10-11

### Documentation Hub Launch

#### Added
- Complete online documentation hub (documentation.html)
- 10 major sections with searchable interface
- Sticky sidebar navigation
- Mobile-responsive design
- Quick links and emergency resources

---

## [1.2.0] - 2025-10-10

### Form Submissions & Email System

#### Added
- Multi-audience form (Individual, Therapist, Organization, Supporter)
- PHP form handler with validation
- Email automation (admin + user confirmations)
- Notion database sync capability
- AI-powered email personalization (optional)

#### Security
- Input sanitization
- Honeypot spam protection
- Rate limiting
- HTTPS enforcement

---

## [1.1.0] - 2025-10-09

### Initial Landing Page

#### Added
- Hero section with gradient background
- "How it works" 3-step process
- "Who it's for" audience sections
- Interactive demo integration (Unison widget)
- FormSubmit.co integration
- Responsive mobile design
- Lucide icons
- Google Analytics

---

## [1.0.0] - 2025-10-08

### Project Launch

#### Added
- Initial HTML landing page
- Basic styling with Tailwind CSS
- Contact form
- Deployment setup

---

## 🔮 **Upcoming (Roadmap)**

### [2.1.0] - Planned
- [ ] Therapist onboarding page (/onboarding/{token})
- [ ] Email invitation system (193 therapists)
- [ ] Admin verification dashboard
- [ ] First 20 therapist profiles published

### [2.2.0] - Planned
- [ ] Public therapist directory
- [ ] Search and filter functionality
- [ ] Therapist profile pages
- [ ] Booking integration

### [3.0.0] - Planned
- [ ] NSW & QLD expansion
- [ ] Advanced matching algorithm
- [ ] Client dashboard
- [ ] Therapist dashboard

---

## 📊 **Metrics**

### **Current Status:**
- **Lines of Code:** ~15,000 (HTML, CSS, JS, PHP)
- **Documentation:** ~25,000 words
- **Therapists:** 202 in database, 193 ready
- **Pages:** 12 HTML pages
- **Scripts:** 15+ automation scripts
- **Tests:** 15 Playwright tests

### **Performance:**
- Page load: <2 seconds
- Mobile responsive: 100%
- Accessibility: WCAG 2.1 AA compliant
- SEO optimized

---

## 🤝 **Team**

- **Project Lead:** Tino
- **Development:** Cursor AI + Tino
- **Design:** Therapair Brand System
- **Legal:** Australian law compliant

---

## 📝 **Notes**

### **Version History:**
All versions are preserved in git history. Use `git log` to see detailed changes.

### **Archived Files:**
Old versions and completed work are in `/docs/archive/` for reference.

### **Scripts:**
Database cleanup scripts are kept local only (.gitignore) for security.

---

**For detailed changelog, see git commits:**
```bash
git log --oneline --decorate --graph
```
