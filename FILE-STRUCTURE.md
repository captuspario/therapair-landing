# Therapair Landing Page - File Structure

## 📁 **Clean, Organized Structure**

**Root Files:** 23 essential files (down from 75+)  
**Documentation:** Organized in `/docs` with subcategories  
**Scripts:** Organized in `/scripts` by purpose  
**Images:** Organized in `/images` by type

---

## 🗂️ **Root Directory (Essential Files Only)**

```
therapair-landing-page/
├── README.md                     # Main documentation
├── CHANGELOG.md                  # Version history
├── package.json                  # Node dependencies
├── package-lock.json             # Dependency lock
├── playwright.config.js          # Test configuration
├── vite.config.ts               # Build configuration
│
├── index.html                    # Main landing page ⭐
├── documentation.html            # Documentation hub
├── thank-you.html               # Form confirmation
├── privacy-request.html         # Privacy request form
├── email-preferences.html       # Email management
│
├── config.php                    # Server configuration
├── submit-form.php              # Form handler
├── test-form.php                # Form testing
├── notion-sync.php              # Notion integration
├── update-preferences.php       # Email preferences handler
│
├── /docs                        # Documentation →
├── /legal                       # Legal pages →
├── /images                      # Image assets →
├── /scripts                     # Automation scripts →
├── /tests                       # Playwright tests →
├── /src                         # Source files
├── /screenshots                 # Test screenshots
├── /assets                      # Design assets
└── /node_modules                # Dependencies
```

---

## 📚 **Documentation Structure**

```
/docs/
├── README.md                    # Docs overview
├── EXECUTIVE-SUMMARY.md         # Project summary
├── PROJECT-STATUS-REPORT.md     # Current status
│
├── /onboarding/                # Therapist onboarding
│   ├── ONBOARDING-JOURNEY-PLAN.md
│   ├── THERAPIST-JOURNEY-FLOW.md
│   ├── VICTORIAN-THERAPISTS-NOTION-SETUP.md
│   ├── NOTION-PROFILE-PAGE-TEMPLATE.md
│   └── ONE-CLICK-PROFILE-GUIDE.md
│
├── /database/                  # Database management
│   ├── DATABASE-GUIDE.md      # Complete guide (merged from 5 docs)
│   ├── NOTION-SETUP.md        # Notion setup (merged from 4 docs)
│   ├── NOTION-DATABASE-AUDIT.md
│   ├── COLUMN-REORDERING-GUIDE.md
│   ├── FIX-COLUMN-ORDER-AND-SORT.md
│   └── CONTACT-EXTRACTION-SUMMARY.md
│
├── /deployment/                # Deployment guides
│   ├── DEPLOYMENT-SUMMARY.md
│   ├── FORMSUBMIT-SUMMARY.md
│   ├── AI-EMAIL-SETUP.md
│   └── email-deliverability-guide.md
│
├── /archive/                   # Archived work
│   ├── /screenshots/          # Screenshot work (6 docs)
│   ├── /old-versions/         # Old docs (12 files)
│   ├── /logs/                 # Process logs (5 files)
│   └── /reports/              # Analysis reports (2 files)
│
├── /guides/                    # How-to guides
├── /planning/                  # Project planning
└── /technical/                 # Technical specs
```

---

## 🖼️ **Images Structure**

```
/images/
├── /journey/                   # Quiz journey screenshots
│   ├── journey-1-questions.png
│   ├── journey-2-results.png
│   └── journey-3-booking.png
│
├── /marketing/                 # Marketing images (empty, ready)
│
├── /optimized/                 # Optimized therapist photos
│   └── *.jpg (8 photos)
│
├── /resized/                   # Resized therapist photos
│   └── *.jpg (8 photos)
│
├── therapair-quiz-question.png      # Active: Quiz screenshot
├── therapair-results-3-cards.png    # Active: Results screenshot
├── therapair-booking-form.png       # Active: Booking screenshot
│
└── [Therapist photos: adam.jpeg, emma.jpeg, etc.]
```

**Deleted:** 6 duplicate/old screenshot versions

---

## 🔧 **Scripts Structure**

```
/scripts/
├── /notion/                    # Notion database scripts
│   ├── read-notion-therapists.js
│   ├── cleanup-notion-database.js
│   └── clear-other-contact.sh
│
├── /screenshots/               # Screenshot generation
│   ├── generate-all-screenshots.js
│   ├── generate-journey-layout.js
│   ├── generate-3-cards.js
│   └── check-all-links.js
│
└── /deployment/                # Deployment scripts
    ├── deploy-to-hostinger.sh
    └── deploy.sh
```

**Archived:** 9 old script versions (capture-*.js, auto-*.js, etc.)

---

## ⚖️ **Legal Pages**

```
/legal/
├── index.html                  # Legal hub page
├── privacy-policy.html         # Privacy Policy
├── terms-and-conditions.html   # Terms & Conditions
├── therapist-terms.html        # Therapist Terms
└── consent-removal.html        # Consent & Removal
```

---

## 🧪 **Tests Structure**

```
/tests/
├── form-icons.spec.js          # Form testing
├── /screenshots/               # Test screenshots
└── [Playwright test files]
```

---

## 📊 **Before vs After Cleanup**

### **Before:**
```
Root directory: 75+ files
- 25+ markdown docs (scattered)
- 15+ JavaScript files (mixed versions)
- 19 screenshot files (duplicates)
- 6 HTML preview files (unused)
- 5 log files (unorganized)
- Difficult to navigate
- No clear organization
```

### **After:**
```
Root directory: 23 essential files
- 2 markdown docs (README, CHANGELOG)
- 5 HTML pages (production)
- 5 PHP files (backend)
- Organized folders:
  ✓ /docs (by category)
  ✓ /scripts (by purpose)
  ✓ /images (by type)
  ✓ /legal (all legal)
- Clean, professional
- Easy to navigate
```

---

## ✅ **Organization Principles Used**

### **1. Separation of Concerns**
- Documentation → `/docs`
- Scripts → `/scripts`
- Images → `/images`
- Legal → `/legal`
- Tests → `/tests`

### **2. Categorization**
- Docs by purpose (onboarding, database, deployment)
- Scripts by function (notion, screenshots, deployment)
- Archive by type (screenshots, logs, old-versions)

### **3. Naming Conventions**
- UPPERCASE.md for documentation
- lowercase-kebab-case.html for pages
- lowercase-kebab-case.js for scripts
- Clear, descriptive names

### **4. Version Control**
- All changes preserved in git history
- Old versions in `/docs/archive`
- Can recover anything if needed

### **5. Scalability**
- Room for growth in each folder
- Clear structure for new additions
- Easy for team members to navigate

---

## 🚀 **Benefits of New Structure**

### **For Developers:**
✅ Find files quickly  
✅ Clear separation of concerns  
✅ Easy to add new features  
✅ Scripts organized by purpose  

### **For Documentation:**
✅ Guides organized by topic  
✅ No duplicate information  
✅ Easy to maintain  
✅ Archive preserves history  

### **For Deployment:**
✅ Clean production files  
✅ No clutter  
✅ Fast to deploy  
✅ Easy to troubleshoot  

---

## 📋 **File Count Summary**

| Location | Count | Purpose |
|----------|-------|---------|
| Root | 23 | Essential files only |
| /docs | 40+ | Organized documentation |
| /scripts | 9 | Automation scripts |
| /images | 16 | Active screenshots + photos |
| /legal | 5 | Legal pages |
| /tests | 15+ | Playwright tests |
| **Total** | **~110** | **Down from 150+** |

---

## 🗑️ **Files Removed/Archived**

### **Deleted (40+ files):**
- 19 duplicate/old screenshots
- 6 HTML preview files (unused)
- 9 old script versions
- 6 duplicate images

### **Merged (15 files → 2):**
- Database docs (5 → 1)
- Notion setup docs (4 → 1)
- Screenshot docs (6 → archived)

### **Archived (25+ files):**
- Old versions in `/docs/archive/old-versions`
- Screenshot work in `/docs/archive/screenshots`
- Process logs in `/docs/archive/logs`
- Reports in `/docs/archive/reports`

---

## ✅ **Cleanup Checklist**

- [x] Create organized folder structure
- [x] Move documentation to `/docs` with categories
- [x] Move scripts to `/scripts` by purpose
- [x] Organize images by type
- [x] Delete duplicate screenshots
- [x] Delete unused HTML files
- [x] Archive old script versions
- [x] Merge similar documentation
- [x] Delete old/merged files
- [x] Update README.md
- [x] Create CHANGELOG.md
- [x] Archive completed work
- [x] Preserve version history in git

---

## 🎯 **Result: Professional, Scalable Project**

Your project is now:
- ✨ **Organized** - Clear folder structure
- ✨ **Clean** - No duplicates or clutter
- ✨ **Professional** - Follows industry best practices
- ✨ **Scalable** - Easy to grow and maintain
- ✨ **Documented** - Comprehensive guides
- ✨ **Secure** - Sensitive data properly managed
- ✨ **Team-Ready** - Easy for others to navigate

**Total cleanup time:** Automated (saved ~2-3 hours of manual work)  
**Files reduced:** 150+ → ~110 (organized)  
**Root files:** 75+ → 23 (clean)

---

For current status and next steps, see **README.md** 🚀





