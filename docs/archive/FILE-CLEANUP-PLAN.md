# File Structure Cleanup Plan

## 📊 **Current State: 75+ files in root directory**

### **Issues:**
- Too many files in root
- Multiple similar/duplicate docs
- Screenshots scattered
- Old/unused files
- No clear organization

---

## 🎯 **Proposed New Structure**

```
therapair-landing-page/
├── README.md (main entry point)
├── index.html (landing page)
├── documentation.html (docs hub)
├── privacy-request.html
├── thank-you.html
├── email-preferences.html
│
├── /docs
│   ├── /onboarding
│   │   ├── ONBOARDING-JOURNEY-PLAN.md
│   │   ├── THERAPIST-JOURNEY-FLOW.md
│   │   └── NOTION-PROFILE-PAGE-TEMPLATE.md
│   │
│   ├── /database
│   │   ├── DATABASE-GUIDE.md (merged from 5 docs)
│   │   ├── NOTION-SETUP.md (merged from 4 docs)
│   │   └── CLEANUP-LOGS.md (reference only)
│   │
│   ├── /deployment
│   │   ├── DEPLOYMENT-GUIDE.md
│   │   └── EMAIL-SETUP.md
│   │
│   └── /archive
│       ├── screenshot-summaries/
│       ├── old-versions/
│       └── completed-work/
│
├── /legal
│   ├── index.html
│   ├── privacy-policy.html
│   ├── terms-and-conditions.html
│   ├── therapist-terms.html
│   └── consent-removal.html
│
├── /images
│   ├── /therapists (profile photos)
│   ├── /marketing (landing page images)
│   └── /journey (quiz screenshots)
│
├── /scripts
│   ├── /notion (database cleanup scripts)
│   ├── /screenshots (Playwright scripts)
│   └── /deployment (deploy scripts)
│
├── /tests
│   └── /playwright (existing test files)
│
└── /node_modules
```

---

## 📋 **Files to MERGE**

### **Database Documentation (Merge into 2 files):**

**1. Create: `docs/database/DATABASE-GUIDE.md`**
Merge these 5 files:
- DATABASE-STRUCTURE.md
- DATABASE-SETUP-GUIDE.md
- DATABASE-OPTIMIZATION-RECOMMENDATIONS.md
- DATABASE-CLEANUP-COMPLETE.md
- FINAL-DATABASE-STATUS.md

**2. Create: `docs/database/NOTION-SETUP.md`**
Merge these 4 files:
- NOTION-QUICK-SETUP.md
- NOTION-DATABASE-SETUP.md
- NOTION-DATABASE-STRATEGY.md
- READ-NOTION-DATABASE-GUIDE.md

### **Screenshot Documentation (Merge into 1 file):**

**Create: `docs/archive/SCREENSHOT-WORK.md`**
Merge:
- 3-CARD-LAYOUT-SUMMARY.md
- COMPLETE-SCREENSHOT-SUMMARY.md
- SCREENSHOT-CAPTURE-GUIDE.md
- SCREENSHOT-UPDATE-SUMMARY.md
- USER-SCREENSHOTS-SUMMARY.md
- MANUAL-CAPTURE-INSTRUCTIONS.md

### **Deployment Documentation (Merge into 1 file):**

**Create: `docs/deployment/DEPLOYMENT-GUIDE.md`**
Merge:
- DEPLOYMENT-SUMMARY.md
- FORMSUBMIT-SUMMARY.md
- LANDING-PAGE-DOCUMENTATION.md

---

## 🗑️ **Files to DELETE**

### **Screenshots (19 files):**
```
✗ Screenshot 2025-10-14 at 1.12.16 pm.png
✗ screencapture-unisonmentalhealth-find-a-therapist...png
✗ f5124ce3-fc77-4d4e-8cc6-4cb81829001d.png
✗ therapair-quiz-question.jpeg (duplicate - have .png)
✗ therapair-results-full.jpeg (duplicate)
✗ journey-1-questions.png (old version)
✗ journey-2-results.png (old version)
✗ journey-3-booking.png (old version)
```

Keep only final versions in /images/journey/

### **Old/Unused HTML:**
```
✗ index-old.html
✗ therapair-optimized-landing.html
✗ noun-project-preview.html
✗ organic-icons-preview.html
✗ pastel-style-icons.html
✗ warm-icons-preview.html
```

### **Old Scripts:**
```
✗ analyze-ui.js
✗ competitive-analysis.js
✗ auto-capture.js
✗ auto-navigate-capture.js
✗ capture-3-results.js
✗ capture-improved-results.js
✗ capture-results-manual.js
✗ direct-capture.js
✗ simple-capture.js
```

Keep only: generate-*.js (current versions)

### **Log Files:**
```
✗ advanced-cleanup-log.txt
✗ cleanup-log.txt
✗ contact-extraction-log.txt
✗ new-cleanup-log.txt
✗ optimization-log.txt
```

Archive or delete (information captured in docs)

### **Duplicate/Old Docs:**
```
✗ WARM-TRANSFORMATION-SUMMARY.md (old work)
✗ WHAT-CHANGED.md (old)
✗ email-deliverability-guide.md (merge into deployment)
✗ DOCUMENTATION-INDEX.md (superseded by main README)
```

---

## 📁 **Files to MOVE**

### **To /docs/onboarding/:**
```
→ ONBOARDING-JOURNEY-PLAN.md
→ THERAPIST-JOURNEY-FLOW.md
→ VICTORIAN-THERAPISTS-NOTION-SETUP.md
→ NOTION-PROFILE-PAGE-TEMPLATE.md
→ ONE-CLICK-PROFILE-GUIDE.md
```

### **To /docs/database/:**
```
→ All NOTION-*.md files (after merging)
→ All DATABASE-*.md files (after merging)
→ COLUMN-REORDERING-GUIDE.md
→ FIX-COLUMN-ORDER-AND-SORT.md
```

### **To /docs/archive/:**
```
→ All screenshot-related docs (after merging)
→ All log files
→ WARM-TRANSFORMATION-SUMMARY.md
→ WHAT-CHANGED.md
```

### **To /scripts/notion/:**
```
→ cleanup-notion-database.js
→ read-notion-therapists.js
→ check-all-links.js
→ clear-other-contact.sh (if keeping)
```

### **To /scripts/screenshots/:**
```
→ generate-3-cards.js
→ generate-all-screenshots.js
→ generate-journey-layout.js
```

### **To /images/journey/:**
```
→ All journey/quiz screenshot .png files
```

---

## 🔄 **Execution Plan**

### **Phase 1: Create New Structure**
1. Create folders
2. Merge documents
3. Move files

### **Phase 2: Clean Up**
1. Delete old screenshots
2. Delete unused scripts
3. Delete log files
4. Delete old HTML previews

### **Phase 3: Archive**
1. Move completed work to archive
2. Keep version history in git
3. Update .gitignore

### **Phase 4: Update Main Files**
1. Update README.md
2. Update documentation.html
3. Create CHANGELOG.md

---

## ✅ **Result: Clean, Professional Structure**

From: 75+ files in root
To: ~10 essential files + organized folders

Want me to execute this plan?

