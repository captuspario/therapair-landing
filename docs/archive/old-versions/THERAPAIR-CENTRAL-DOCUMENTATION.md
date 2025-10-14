# Therapair - Central Documentation Hub

**Version**: 1.0  
**Last Updated**: 2025-10-10  
**Scope**: All Therapair Projects

---

## 📚 Documentation Index

This is the central hub for all Therapair documentation across all projects and deployments.

### **Project Structure**

```
therapair/
├── therapair-landing-page/     # Main marketing site (therapair.com.au)
│   ├── LANDING-PAGE-DOCUMENTATION.md
│   ├── notion-database-setup.md
│   ├── email-ai-prompt.md
│   └── [operational files]
│
├── therapair-widget-unison/    # Embedded widget (unisoncounselling.com)
│   ├── WIDGET-DOCUMENTATION.md (to create)
│   ├── docs/unison-design-system.md
│   └── [widget files]
│
└── THERAPAIR-CENTRAL-DOCUMENTATION.md (this file)
```

---

## 🎨 Shared Design System

### **Brand Identity**

**Brand Name**: Therapair  
**Tagline**: "Mental health matching, built with care"  
**Mission**: Create truly inclusive mental health care by connecting people with therapists who understand their unique identities

### **Brand Values**
1. **Inclusive** - Built specifically for diverse and marginalised communities
2. **Human-Centred** - Real humans, real care (not just algorithms)
3. **Professional** - Warm but competent and trustworthy
4. **Transparent** - Honest about what we are and aren't doing
5. **Evidence-Based** - Grounded in best practices for therapy matching

---

## 🎨 **UNIFIED DESIGN SYSTEM**

### **Color Palette**

#### **Primary Colors**
```css
/* Therapair Purple - Main brand color */
--therapair-primary: #9B74B7      /* Medium purple */
--therapair-primary-dark: #4F064F  /* Dark purple (hover states) */
--therapair-primary-light: #D4B5D8 /* Light purple (accents) */

/* Therapair Blue - Secondary */
--therapair-secondary: #2563eb     /* Blue (professional) */
--therapair-secondary-light: #8b5cf6 /* Purple-blue */

/* Therapair Green - Success/Affirming */
--therapair-accent: #10b981        /* Green */
--therapair-success: #059669       /* Dark green */
```

#### **Neutral Colors**
```css
--therapair-text: #1e293b          /* Almost black */
--therapair-gray: #64748b          /* Medium gray */
--therapair-gray-light: #d1d5db    /* Light gray */
--therapair-background: #f8fafc    /* Off-white */
--therapair-background-purple: #F8F4FF /* Light purple tint */
```

#### **Semantic Colors**
```css
--color-error: #ef4444            /* Red */
--color-warning: #f59e0b          /* Orange */
--color-info: #3b82f6             /* Blue */
```

### **Typography**

#### **Font Families**
```css
/* Primary: Open Sans (loaded via Google Fonts) */
font-family: 'Open Sans', ui-sans-serif, system-ui, sans-serif;

/* Fallback: System fonts for performance */
font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
```

#### **Font Scales**
```css
/* Headings */
--font-size-h1: 2.5rem;    /* 40px */
--font-size-h2: 2rem;      /* 32px */
--font-size-h3: 1.5rem;    /* 24px */
--font-size-h4: 1.25rem;   /* 20px */

/* Body */
--font-size-base: 1rem;    /* 16px */
--font-size-sm: 0.875rem;  /* 14px */
--font-size-xs: 0.75rem;   /* 12px */

/* Weights */
--font-weight-normal: 400;
--font-weight-medium: 500;
--font-weight-semibold: 600;
--font-weight-bold: 700;
```

### **Spacing System**

```css
/* Base unit: 4px */
--space-1: 0.25rem;   /* 4px */
--space-2: 0.5rem;    /* 8px */
--space-3: 0.75rem;   /* 12px */
--space-4: 1rem;      /* 16px */
--space-6: 1.5rem;    /* 24px */
--space-8: 2rem;      /* 32px */
--space-12: 3rem;     /* 48px */
--space-16: 4rem;     /* 64px */
```

### **Border Radius**

```css
--radius-sm: 8px;      /* Small elements */
--radius-md: 12px;     /* Cards, inputs */
--radius-lg: 16px;     /* Large cards */
--radius-pill: 25px;   /* Pills, buttons */
--radius-full: 9999px; /* Circular */
```

### **Shadows**

```css
/* Elevation system */
--shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
--shadow-md: 0 4px 12px rgba(155, 116, 183, 0.15);
--shadow-lg: 0 8px 24px rgba(155, 116, 183, 0.2);
--shadow-xl: 0 20px 40px rgba(155, 116, 183, 0.25);
```

---

## ✍️ Content & Voice Guidelines

### **Tone of Voice**

**Overall**: Warm, professional, conversational

**Characteristics**:
- ✅ **Conversational**: "Thanks so much for..." not "Thank you for your inquiry"
- ✅ **Inclusive**: Explicitly mention diverse identities
- ✅ **Honest**: Transparent about current stage and limitations
- ✅ **Encouraging**: "You're one of the first to explore this with us"
- ✅ **Human**: Avoid jargon, speak plainly
- ✅ **Australian**: Use Australian English spelling

**Avoid**:
- ❌ Corporate/formal: "Dear Sir/Madam", "Your request has been received"
- ❌ Over-promising: "We'll match you immediately!"
- ❌ Overly enthusiastic: Too many exclamation points!!!
- ❌ Clinical language: Avoid medical jargon where possible
- ❌ Pressure tactics: No urgent CTAs or scarcity

### **Writing Style**

**Sentence Structure**:
- Short, clear sentences
- Active voice preferred
- Conversational but professional
- 2-3 paragraphs maximum for key messages

**Word Choice**:
- "Therapist" not "provider" or "practitioner" (except where formal)
- "Matching" not "pairing" or "connecting"
- "Inclusive" and "affirming" are key brand words
- "Built with real humans and real care" (tagline)

### **Australian English**

**Consistent Spelling**:
- Personalised (not personalized)
- Organisation (not organization)
- Specialisation (not specialization)
- Recognise (not recognize)
- Prioritise (not prioritize)
- Programme (not program - for initiatives)
- Centre (not center)

---

## 📧 **UNIFIED EMAIL & COMMUNICATION SYSTEM**

### **Email Strategy**

#### **Principles**
1. **Personalised**: Use recipient name, reference their specific inputs
2. **Contextual**: Different messages for different audiences
3. **Actionable**: Clear next steps
4. **Branded**: Consistent look and feel
5. **Trackable**: Include preference management

#### **Email Types Across Projects**

| Email Type | Trigger | Recipients | Content |
|------------|---------|------------|---------|
| **Admin Notification** | Form submission | Team | All form data, formatted |
| **User Confirmation** | Form submission | User | AI-personalised thank you |
| **Booking Request** | Widget booking | Team | Booking details, user info |
| **Booking Confirmation** | Widget booking | User | Booking confirmed, next steps |
| **Preference Update** | Preference change | User | Confirmation of changes |
| **Newsletter** | Manual/automated | Subscribers | Updates, news |

### **Email Template Structure**

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Shared email styles */
        body { font-family: 'Open Sans', Arial, sans-serif; }
        .header { background: linear-gradient(135deg, #9B74B7, #4F064F); color: white; }
        .content { padding: 2rem; background: white; }
        .footer { color: #64748b; font-size: 0.875rem; }
        .button { background: #9B74B7; color: white; padding: 12px 24px; }
    </style>
</head>
<body>
    <div class="header">[BRAND/LOGO]</div>
    <div class="content">[PERSONALISED CONTENT]</div>
    <div class="footer">
        <p>Therapair Team</p>
        <p>📧 contact@therapair.com.au</p>
        <p>⚙️ <a href="[PREFERENCES_URL]">Manage Email Preferences</a></p>
    </div>
</body>
</html>
```

### **AI Email Generation (OpenAI)**

**Shared System Prompt Framework**:
```
You are Therapair's email assistant. Generate clear, warm, human responses.

CONTEXT: [Project-specific context]
AUDIENCE: [User type]
TONE: Conversational, professional, warm
LENGTH: 2-3 paragraphs max
BRAND: Therapist-matching concierge with real humans and real care

KEY REQUIREMENTS:
- Thank them for their submission
- Acknowledge what they shared specifically
- Explain Therapair briefly
- Set expectations (early development, updates to come)
- No over-promising
- Sign-off: "Warm regards, Therapair Team"
```

---

## 🗄️ **UNIFIED DATABASE SYSTEM**

### **Data Collection Strategy**

#### **Core Fields (All Projects)**
```json
{
  "email": "string (required)",
  "submission_date": "datetime",
  "source": "landing_page | widget | other",
  "audience_type": "individual | therapist | organisation | supporter",
  "status": "new | contacted | engaged | converted",
  "email_preferences": ["product_updates", "launch_news", ...],
  "unsubscribed": false
}
```

#### **Project-Specific Extensions**

**Landing Page**:
```json
{
  "therapy_interests": ["lgbtq", "neurodiversity", ...],
  "additional_thoughts": "string",
  "professional_title": "string",
  "organisation": "string",
  ...
}
```

**Widget (Unison)**:
```json
{
  "full_name": "string",
  "phone": "string",
  "selected_therapist": "string",
  "user_responses": {...},
  "preferences_summary": "string",
  "booking_status": "pending | confirmed | completed"
}
```

### **Notion Database Structure**

#### **Option 1: Unified Database (Recommended for MVP)**
- Single database with all entries
- "Source" field distinguishes projects
- "Type" field distinguishes entry type
- Conditional views filter by source/type

#### **Option 2: Separate Databases (Scalable)**
- `therapair_interest` - Landing page submissions
- `therapair_bookings` - Widget bookings
- `therapair_users` - Deduplicated user database
- Relations link entries to users

#### **Recommended: Start Unified, Plan for Separation**

**Current**: Single Notion database with source field
**Future**: Migrate to proper database with relations when scaling

---

## 🔗 **CROSS-PROJECT INTEGRATION POINTS**

### **1. Shared Email Infrastructure**

**Current State**:
- Landing Page: PHP `mail()` + OpenAI API
- Widget: FormSubmit.co

**Goal State**:
- Unified PHP email handler
- Shared AI prompt system
- Consistent email templates
- Single email preference database

**Migration Path**:
1. Create shared `email-handler.php` module
2. Widget updates to use shared handler
3. Both projects use same Notion database
4. Unified preference management

### **2. Shared Notion Database**

**Strategy**:
```
Entry Fields:
- source: "landing_page" | "widget_unison"  
- entry_type: "interest" | "booking"
- audience_type: "individual" | "therapist" | "organisation" | "supporter"
- project: "therapair" (allows for future projects)
```

**Views in Notion**:
- All Entries
- Landing Page Submissions
- Widget Bookings
- By Audience Type
- By Status
- Recent (last 7 days)

### **3. Design System Sync**

**Ensure Consistency**:
- Same color variables
- Same typography
- Same button styles
- Same form input styles
- Same spacing system

**Implementation**:
- Create `shared-styles.css` 
- Both projects import or copy
- Document in design system

---

## 📊 **ANALYTICS & TRACKING STRATEGY**

### **Unified Tracking Goals**

**Primary Metrics**:
1. **Lead Generation**: Total form submissions across all sources
2. **Conversion Rate**: % of visitors who submit
3. **Audience Distribution**: Which personas show most interest
4. **Email Engagement**: Open rates, click rates
5. **Booking Completion**: Widget booking flow completion rate

**Google Analytics 4 Setup** (Recommended):
```javascript
// Shared GA4 property for all Therapair projects
gtag('config', 'G-THERAPAIR-ID', {
  'custom_map': {
    'dimension1': 'project_source',
    'dimension2': 'audience_type',
    'dimension3': 'entry_type'
  }
});

// Event tracking
gtag('event', 'form_submission', {
  'project_source': 'landing_page',
  'audience_type': 'individual',
  'entry_type': 'interest'
});
```

### **Notion Analytics**

Create formulas and rollups:
- Total submissions by source
- Conversion funnel (interest → booking → completed)
- Response time tracking
- Email preference trends

---

## 🚀 **SCALABILITY ROADMAP**

### **Phase 1: Current MVP (Q4 2025)**

**Landing Page**:
- ✅ Multi-audience interest forms
- ✅ AI email confirmations
- ✅ Notion database sync
- ✅ Email preferences

**Widget**:
- ✅ Therapist matching quiz
- ✅ Booking form (FormSubmit.co)
- ⚠️ Basic email (needs upgrade)
- ❌ No Notion sync (to add)

### **Phase 2: Unified Infrastructure (Q1 2026)**

**Goals**:
- [ ] Shared email handler module
- [ ] Widget migrated to shared Notion database
- [ ] Unified email preference system
- [ ] Cross-project user deduplication
- [ ] Shared analytics dashboard

**Technical**:
- [ ] Create `shared/` directory with common modules
- [ ] Refactor both projects to use shared code
- [ ] Set up staging environments
- [ ] Implement proper CI/CD

### **Phase 3: Database Migration (Q2 2026)**

**From**: Notion (current)  
**To**: PostgreSQL + Notion sync

**Benefits**:
- Faster queries
- Better data integrity
- API development ready
- Scalable to thousands of users
- Notion becomes read-only view

**Keep Notion For**:
- Team collaboration
- Quick views and filters
- Templates and workflows
- Manual note-taking

### **Phase 4: Full Platform (Q3 2026)**

**Architecture**:
```
Frontend: React/Next.js (unified platform)
Backend: Node.js/Express API
Database: PostgreSQL
Cache: Redis
Search: Elasticsearch (therapist search)
Email: SendGrid/Postmark (transactional)
Analytics: Mixpanel + GA4
Hosting: Vercel (frontend) + AWS (backend)
```

---

## 📁 **PROJECT DOCUMENTATION INDEX**

### **Landing Page** (therapair.com.au)
- **Main Doc**: `therapair-landing-page/LANDING-PAGE-DOCUMENTATION.md`
- **Notion Setup**: `therapair-landing-page/notion-database-setup.md`
- **Email AI**: `therapair-landing-page/email-ai-prompt.md`
- **Deliverability**: `therapair-landing-page/email-deliverability-guide.md`
- **Database Audit**: `therapair-landing-page/NOTION-DATABASE-AUDIT.md`

### **Widget** (Unison Embedding)
- **Main Doc**: `therapair-widget-unison/WIDGET-DOCUMENTATION.md` (to create)
- **Design System**: `therapair-widget-unison/docs/unison-design-system.md`
- **Copy Elements**: `therapair-widget-unison/docs/widget-copy-elements.md`
- **Deployment**: `therapair-widget-unison/DEPLOYMENT-INSTRUCTIONS.md`
- **Email Setup**: `therapair-widget-unison/EMAIL-SETUP-GUIDE.md`

### **Shared Resources**
- **Central Hub**: `THERAPAIR-CENTRAL-DOCUMENTATION.md` (this file)
- **Design System**: `THERAPAIR-DESIGN-SYSTEM.md` (to create)
- **Email Templates**: `THERAPAIR-EMAIL-TEMPLATES.md` (to create)
- **Database Schema**: `THERAPAIR-DATABASE-SCHEMA.md` (to create)

---

## 🔧 **DEVELOPMENT SETUP**

### **Prerequisites**
```bash
# Node.js & npm (for widget development)
node >= 18.x
npm >= 9.x

# PHP (for landing page)
php >= 7.4

# Git
git >= 2.x
```

### **Environment Variables**

Create `.env` files in each project:

**Landing Page**:
```bash
ADMIN_EMAIL=contact@therapair.com.au
OPENAI_API_KEY=sk-proj-xxx
NOTION_TOKEN=ntn_xxx
NOTION_DATABASE_ID=xxx
```

**Widget**:
```bash
FORMSUBMIT_EMAIL=tino@unisoncounselling.com
NOTION_TOKEN=ntn_xxx
NOTION_DATABASE_ID=xxx
```

### **Local Development**

**Landing Page**:
```bash
cd therapair-landing-page
# Use local PHP server or MAMP/XAMPP
php -S localhost:8000
```

**Widget**:
```bash
cd therapair-widget-unison
npm install
# Open src/therapair-standalone.html in browser
```

---

## 🔄 **DEPLOYMENT WORKFLOWS**

### **Landing Page** → Hostinger
```bash
cd therapair-landing-page
./deploy-to-hostinger.sh
```

### **Widget** → Unison Website
```bash
cd therapair-widget-unison
# Follow DEPLOYMENT-INSTRUCTIONS.md
# Copy therapair-widget/ to Unison server
```

---

## 📝 **CONTRIBUTING GUIDELINES**

### **Documentation Standards**

1. **Update this file** when adding new projects or major changes
2. **Link to this file** from project-specific docs
3. **Keep design system synced** across all projects
4. **Document all decisions** with rationale
5. **Version all changes** with dates

### **Code Standards**

1. **Consistent naming**: PascalCase for form fields
2. **Commented code**: Explain why, not what
3. **Responsive design**: Mobile-first
4. **Accessible**: ARIA labels, keyboard navigation
5. **Australian English**: All user-facing content

### **Before Committing**

- [ ] Test locally
- [ ] Check responsive design
- [ ] Update relevant documentation
- [ ] Lint/format code
- [ ] Test email flows if changed
- [ ] Verify Notion sync if changed

---

## 🆘 **SUPPORT & CONTACTS**

**Technical Issues**: Check project-specific documentation first

**Email System**: See `email-deliverability-guide.md`

**Notion Database**: See `notion-database-setup.md`

**Widget Issues**: See `WIDGET-DOCUMENTATION.md`

**Design Questions**: See `THERAPAIR-DESIGN-SYSTEM.md`

---

## 📅 **Version History**

### v1.0 (2025-10-10)
- Initial central documentation hub created
- Unified design system established
- Cross-project integration strategy defined
- Scalability roadmap outlined

---

**Maintained By**: Therapair Development Team  
**Last Review**: 2025-10-10  
**Next Review**: 2026-01-10

---

*This is a living document. Update as the Therapair ecosystem evolves.*

