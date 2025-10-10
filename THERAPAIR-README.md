# Therapair - Project Documentation

**Version**: 1.0  
**Last Updated**: 2025-10-10  
**Organisation**: Therapair  
**Mission**: Create truly inclusive mental health care through intelligent therapist matching

---

## 🎯 Quick Start

### What is Therapair?

Therapair is a **therapist-matching concierge service** built with real humans and real care, designed specifically for diverse and marginalised communities.

We're revolutionising how people find mental health support by:
- ✅ Matching based on identity, values, and specific needs
- ✅ Highlighting inclusive, culturally competent practitioners
- ✅ Using AI to personalise the experience
- ✅ Building with feedback from our community

---

## 📁 Project Structure

```
/Users/tino/Projects/
│
├── THERAPAIR-README.md (this file)
├── THERAPAIR-CENTRAL-DOCUMENTATION.md
├── THERAPAIR-DESIGN-SYSTEM.md
├── THERAPAIR-SCALABLE-INFRASTRUCTURE.md
│
├── therapair-landing-page/
│   ├── index.html
│   ├── submit-form.php
│   ├── notion-sync.php
│   ├── LANDING-PAGE-DOCUMENTATION.md
│   ├── notion-database-setup.md
│   ├── email-ai-prompt.md
│   └── [documentation & code files]
│
└── therapair-widget-unison/
    ├── src/therapair-standalone.html
    ├── therapair-widget/
    ├── WIDGET-DOCUMENTATION.md
    ├── docs/unison-design-system.md
    └── [widget files]
```

---

## 📚 Documentation Index

### **Start Here**
1. **THERAPAIR-README.md** (this file) - Overview and quick links
2. **THERAPAIR-CENTRAL-DOCUMENTATION.md** - Central hub for all projects

### **Design & Brand**
3. **THERAPAIR-DESIGN-SYSTEM.md** - Colors, typography, components, brand guidelines

### **Technical Architecture**
4. **THERAPAIR-SCALABLE-INFRASTRUCTURE.md** - Email system, database schema, API design

### **Project-Specific**
5. **therapair-landing-page/LANDING-PAGE-DOCUMENTATION.md** - Complete landing page docs
6. **therapair-widget-unison/WIDGET-DOCUMENTATION.md** - Widget system docs

### **Operations**
7. **therapair-landing-page/notion-database-setup.md** - Notion database structure
8. **therapair-landing-page/email-ai-prompt.md** - AI email generation strategy
9. **therapair-landing-page/email-deliverability-guide.md** - Email best practices
10. **therapair-landing-page/NOTION-DATABASE-AUDIT.md** - Field mapping verification

---

## 🚀 Current Projects

### 1. Landing Page (therapair.com.au)

**Status**: ✅ **Production**  
**Purpose**: Interest gathering for Therapair platform  
**Users**: Individuals, Therapists, Organisations, Supporters

**Features**:
- Multi-audience interest forms
- AI-powered email confirmations (OpenAI)
- Notion database integration
- Email preference management
- Australian English throughout

**Tech Stack**:
- Frontend: HTML, CSS, Vanilla JavaScript
- Backend: PHP 7.4+
- Email: PHP mail() + OpenAI API
- Database: Notion API
- Hosting: Hostinger

**Documentation**: `therapair-landing-page/LANDING-PAGE-DOCUMENTATION.md`

---

### 2. Therapist Matching Widget (Unison)

**Status**: ✅ **Production**  
**Purpose**: Match Unison Counselling clients with right therapist  
**Users**: Individuals seeking therapy

**Features**:
- Interactive 6-question quiz
- Smart matching algorithm (8 therapists)
- Booking form integration
- Email notifications (FormSubmit.co)
- Embedded iframe widget

**Tech Stack**:
- Frontend: HTML, CSS, Vanilla JavaScript
- Backend: FormSubmit.co (to migrate to PHP)
- Database: None (to add Notion)
- Hosting: Unison Counselling server

**Documentation**: `therapair-widget-unison/WIDGET-DOCUMENTATION.md`

---

## 🎨 Design System

### Brand Colors

```css
Purple (Primary): #9B74B7, #4F064F, #D4B5D8
Blue (Secondary): #2563eb, #3b82f6
Green (Success): #10b981, #059669
```

### Typography

```css
Font: 'Open Sans', sans-serif
Sizes: 12px - 48px scale
Weights: 400, 500, 600, 700
```

### Spacing

```css
Base unit: 4px
Scale: 4px, 8px, 12px, 16px, 24px, 32px, 48px, 64px
```

**Full Details**: `THERAPAIR-DESIGN-SYSTEM.md`

---

## 📧 Email System

### Current Implementation

**Landing Page**:
- PHP mail() function
- OpenAI API for AI personalization
- HTML email templates
- Preference management

**Widget**:
- FormSubmit.co (basic)
- Static autoresponse
- No preference management

### Unified System (Roadmap)

- Shared email handler module
- Consistent templates across projects
- AI personalization for all
- Unified preference database

**Full Details**: `THERAPAIR-SCALABLE-INFRASTRUCTURE.md`

---

## 🗄️ Database System

### Current Implementation

**Notion Database** (Single unified database):
- Database ID: `2875c25944da80c0b14afbbdf2510bb0`
- Integration Token: Configured in `config.php`

**Entry Types**:
- Landing Page: Interest forms (4 audience types)
- Widget: Bookings (to be added)

**Properties**:
- Core: Name, Email, Source, Type, Date, Status
- Landing Page: Therapy interests, thoughts, professional details
- Widget: Booking details, preferences, therapist selection

### Future Database

**PostgreSQL** (when scaling):
- Proper relational structure
- User deduplication
- Booking management
- Email logs
- Analytics events

**Full Schema**: `THERAPAIR-SCALABLE-INFRASTRUCTURE.md`

---

## 🔧 Development Setup

### Prerequisites

```bash
# System requirements
Node.js >= 18.x
PHP >= 7.4
Git >= 2.x
```

### Local Setup

**Landing Page**:
```bash
cd therapair-landing-page
cp config.example.php config.php
# Add your API keys to config.php
php -S localhost:8000
```

**Widget**:
```bash
cd therapair-widget-unison
npm install
# Open src/therapair-standalone.html in browser
```

---

## 🚀 Deployment

### Landing Page → Hostinger

```bash
cd therapair-landing-page
./deploy-to-hostinger.sh
```

### Widget → Unison Server

```bash
cd therapair-widget-unison
# Follow DEPLOYMENT-INSTRUCTIONS.md
# Manual upload to Unison server
```

---

## 📊 Current Status

### Landing Page
- ✅ Production ready
- ✅ All 4 journeys working
- ✅ Email system working
- ✅ Notion integration working
- ✅ Fully documented

### Widget
- ✅ Production ready (basic)
- ⚠️ Needs Notion integration
- ⚠️ Needs AI email upgrade
- ⚠️ Needs preference management
- ✅ Documented

### Shared Infrastructure
- ✅ Design system documented
- ✅ Scalability roadmap defined
- ⏳ Shared modules to build
- ⏳ Database migration planned

---

## 🗺️ Roadmap

### Q4 2025 (Current)
- ✅ Landing page live
- ✅ Widget live
- ✅ Notion database operational
- ✅ AI emails working

### Q1 2026
- [ ] Widget Notion integration
- [ ] Widget AI emails
- [ ] Shared email module
- [ ] Unified analytics

### Q2 2026
- [ ] PostgreSQL database
- [ ] API development
- [ ] User deduplication
- [ ] Advanced analytics

### Q3 2026
- [ ] Full platform launch
- [ ] Active matching begins
- [ ] Payment integration
- [ ] Booking management system

---

## 🆘 Support

### Technical Issues

**Landing Page**: See `therapair-landing-page/LANDING-PAGE-DOCUMENTATION.md`  
**Widget**: See `therapair-widget-unison/WIDGET-DOCUMENTATION.md`  
**Design**: See `THERAPAIR-DESIGN-SYSTEM.md`  
**Infrastructure**: See `THERAPAIR-SCALABLE-INFRASTRUCTURE.md`

### Common Tasks

**Update Landing Page Content**:
```bash
cd therapair-landing-page
# Edit index.html
./deploy-to-hostinger.sh
```

**Update Widget**:
```bash
cd therapair-widget-unison
# Edit src/therapair-standalone.html
# Follow DEPLOYMENT-INSTRUCTIONS.md
```

**Update Email Templates**:
```bash
# For AI emails: Edit email-ai-prompt.md
# For fallback: Edit submit-form.php
./deploy-to-hostinger.sh
```

**Add Therapist to Widget**:
```javascript
// Edit therapair-standalone.html
// Add to therapists array
{
  name: "New Name",
  image: "images/newname.jpeg",
  gender: "...",
  specializations: [...],
  ...
}
```

---

## 👥 Team & Contacts

**Email**: contact@therapair.com.au  
**Website**: https://therapair.com.au  
**Widget**: https://unisoncounselling.com (embedded)

**Development**: Tino @ Unison Counselling  
**Documentation Maintained By**: Therapair Development Team

---

## 📝 Contributing

### Before Making Changes

1. Read relevant documentation
2. Test locally
3. Update documentation if needed
4. Commit with clear message
5. Deploy and verify

### Documentation Standards

- Keep all docs up to date
- Link between related docs
- Include version history
- Explain rationale for decisions
- Provide examples

### Code Standards

- Mobile-first responsive design
- Australian English for user-facing content
- Comments explain "why" not "what"
- Consistent naming conventions
- Accessibility best practices

---

## 🔗 Quick Links

| Resource | Location |
|----------|----------|
| **Live Landing Page** | https://therapair.com.au |
| **Live Widget** | https://unisoncounselling.com |
| **Notion Database** | [Notion workspace] |
| **GitHub (Landing)** | captuspario/therapair-landing |
| **Central Docs** | THERAPAIR-CENTRAL-DOCUMENTATION.md |
| **Design System** | THERAPAIR-DESIGN-SYSTEM.md |
| **Infrastructure** | THERAPAIR-SCALABLE-INFRASTRUCTURE.md |

---

## 📅 Version History

### v1.0 (2025-10-10)
- Initial unified documentation system
- All projects documented
- Design system established
- Scalability roadmap defined
- Infrastructure templates created

---

**Welcome to Therapair! 💜**

*Building inclusive mental health care, one match at a time.*

---

**Last Updated**: 2025-10-10  
**Next Review**: 2026-01-10

