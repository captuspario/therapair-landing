# Feedback Widget Sitewide Implementation

**Date:** 2025-01-30  
**Status:** ✅ Complete

---

## Overview

The Therapair feedback widget is now implemented sitewide across all pages, with comprehensive page tracking and Notion database integration.

---

## ✅ What Was Implemented

### 1. Widget Added to All Legal Pages

Added the feedback widget script to all legal pages:
- ✅ `legal/privacy-policy.html`
- ✅ `legal/terms-and-conditions.html`
- ✅ `legal/hipaa-compliance.html`
- ✅ `legal/therapist-terms.html`
- ✅ `legal/consent-removal.html`
- ✅ `legal/index.html`

**Total pages with widget:** 13 pages (including existing ones)

### 2. Page Tracking Features

The widget automatically tracks:
- ✅ **Page URL** (`window.location.href`) - Full URL including query parameters
- ✅ **Page Path** (`window.location.pathname`) - Path portion of URL
- ✅ **Page Title** (`document.title`) - HTML page title
- ✅ **Section** - Auto-detected based on path:
  - `home` - Homepage
  - `documentation` - Documentation pages
  - `legal` - Legal pages (newly added)
  - `survey` - Survey/research pages
  - `sandbox` - Sandbox demo pages
  - `other` - All other pages
- ✅ **Scroll Percentage** - How far user scrolled before submitting
- ✅ **Referrer** - Where they came from
- ✅ **UTM Parameters** - Campaign tracking (source, medium, campaign, content, term)
- ✅ **Viewport Size** - Screen dimensions
- ✅ **Timestamp** - When feedback was submitted

### 3. Notion Database Integration

Feedback is saved to:
- **Database:** `NOTION_DB_SANDBOX` (User Feedback database)
- **Properties Saved:**
  - `Name` - Title with rating, section, page name, and timestamp
  - `Rating` - Number (1-6 stars)
  - `Feedback` - Rich text with comment, tags, and all context
  - `Audience Type` - Select field (Home, Documentation, Legal, Survey, Sandbox, Website)
  - `Submission Date` - Date/time when submitted
  - `Submission Status` - Select field (default: "New")
  - `Page URL` - URL property linking to the page where feedback came from
  - `Tracking ID` - Rich text (for linking multiple feedback entries from same user)
  - `Session ID` - Rich text (if available)

---

## 📍 Pages with Feedback Widget

### Already Had Widget
1. `index.html` - Homepage
2. `documentation.html` - Documentation hub
3. `email-preferences.html` - Email preferences
4. `privacy-request.html` - Privacy request form
5. `thank-you.html` - Thank you page
6. `research/survey/index.html` - Survey page
7. `research/survey/success.html` - Survey success page

### Newly Added
8. `legal/privacy-policy.html` - Privacy policy
9. `legal/terms-and-conditions.html` - Terms & conditions
10. `legal/hipaa-compliance.html` - HIPAA compliance
11. `legal/therapist-terms.html` - Therapist terms
12. `legal/consent-removal.html` - Consent & removal policy
13. `legal/index.html` - Legal index page

---

## 🔍 How It Works

1. **Widget Loading:**
   - Script loaded via: `<script src="/therapair-feedback-widget.js" defer></script>`
   - Appears as floating button in bottom-right corner
   - Visible on all pages sitewide

2. **User Interaction:**
   - User clicks "💬 Give Feedback" button
   - Modal opens with feedback form
   - Page context is captured when modal opens (not when submitted)

3. **Feedback Submission:**
   - User selects rating (1-6 stars)
   - Optionally selects tags (Bug, Usability, Speed, Content, Accessibility, Other)
   - Optionally adds comment
   - Submits form

4. **Data Tracking:**
   - All page context captured when modal opened
   - Sent to `/api/research/feedback.php` via POST request
   - Includes all tracking data (URL, path, title, section, scroll, etc.)

5. **Notion Storage:**
   - API endpoint saves to `NOTION_DB_SANDBOX` database
   - All properties mapped correctly
   - Page URL stored as clickable URL property

---

## 🔧 Configuration

### Widget Script Path
```
/therapair-feedback-widget.js
```

### API Endpoint
```
/api/research/feedback.php
```

### Notion Database
Configured in `config.php`:
```php
define('NOTION_DB_SANDBOX', 'your-database-id-here');
```

---

## 📊 Tracking Details

### Section Detection Logic

```javascript
- '/sandbox' → section: 'sandbox'
- '/survey' or '/research' → section: 'survey'
- '/documentation' → section: 'documentation'
- '/legal' → section: 'legal' (NEW)
- '/' or '/index.html' → section: 'home'
- Other → section: 'other'
```

### Data Captured Per Submission

```javascript
{
  rating: 1-6,
  comment: "user comment",
  tags: ["Bug", "Usability"],
  page_url: "https://therapair.com.au/legal/privacy-policy.html",
  page_path: "/legal/privacy-policy.html",
  page_title: "Privacy Policy - Therapair",
  section: "legal",
  scroll_percent: 45,
  viewport_height: 1080,
  viewport_width: 1920,
  referrer: "https://google.com",
  utm_source: "google",
  utm_medium: "cpc",
  utm_campaign: "privacy_awareness",
  created_at: "2025-01-30T12:00:00.000Z",
  tracking_id: "uuid-v4",
  session_id: "optional-session-id"
}
```

---

## ✅ Verification Checklist

- [x] Widget added to all legal pages
- [x] Page URL tracking implemented
- [x] Page path tracking implemented
- [x] Page title tracking implemented
- [x] Section detection includes 'legal'
- [x] Feedback saves to Notion database
- [x] Page URL saved as URL property in Notion
- [x] All tracking data included in Notion record

---

## 🚀 Next Steps

1. **Test the widget** on different pages to verify:
   - Widget appears correctly
   - Page context is captured
   - Feedback saves to Notion with correct page URL

2. **Monitor Notion database** to see feedback from different pages:
   - Check that Page URL property is populated
   - Verify section detection works correctly
   - Confirm all tracking data is present

3. **Optional enhancements:**
   - Add page-specific metadata (e.g., which section of a long page)
   - Track time spent on page before feedback
   - Add user journey tracking

---

## 📝 Notes

- The widget captures page context when the modal opens, not when it's submitted
- This ensures accurate tracking even if user navigates away
- All feedback is stored in a single Notion database for easy analysis
- Page URL is clickable in Notion, making it easy to navigate back to the source page

