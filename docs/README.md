# Therapair Documentation Hub

**Welcome to the Therapair documentation center.** This directory contains all strategic, technical, and operational documentation for the Therapair platform.

---

## 📚 Documentation Structure

```
docs/
├── README.md (you are here)
├── EXECUTIVE-SUMMARY.md          # Vision, business model, roadmap overview
│
├── technical/
│   ├── ARCHITECTURE.md            # System architecture & tech stack
│   ├── email-ai-prompt.md         # AI email generation prompt
│   └── API-DOCUMENTATION.md       # (Future) API endpoints
│
├── planning/
│   ├── PRODUCT-ROADMAP.md         # Product evolution plan (5-year)
│   └── FEATURE-SPECS.md           # (Future) Detailed feature specs
│
├── guides/
│   ├── notion-database-setup.md   # Notion CRM setup instructions
│   ├── DEPLOYMENT-GUIDE.md        # (Future) Deployment procedures
│   └── ONBOARDING-GUIDE.md        # (Future) Clinic onboarding
│
└── archive/
    └── fix-widget-card-spacing.plan.md  # Historical planning docs
```

---

## 🚀 Quick Start

### For New Team Members
1. Read [EXECUTIVE-SUMMARY.md](./EXECUTIVE-SUMMARY.md) - Understand vision & strategy
2. Review [ARCHITECTURE.md](./technical/ARCHITECTURE.md) - Understand technical foundation
3. Check [PRODUCT-ROADMAP.md](./planning/PRODUCT-ROADMAP.md) - See where we're headed

### For Developers
1. [ARCHITECTURE.md](./technical/ARCHITECTURE.md) - Current tech stack & setup
2. [Deployment Guide](./guides/DEPLOYMENT-GUIDE.md) *(coming soon)* - How to deploy changes
3. Main repos:
   - **Landing Page:** `/therapair-landing-page`
   - **Widget:** `/therapair-widget-unison`

### For Business/Marketing
1. [EXECUTIVE-SUMMARY.md](./EXECUTIVE-SUMMARY.md) - Pitch deck foundation
2. [PRODUCT-ROADMAP.md](./planning/PRODUCT-ROADMAP.md) - Feature timeline
3. [Notion Setup Guide](./guides/notion-database-setup.md) - CRM management

---

## 📖 Key Documents

### Strategic Documents

#### [EXECUTIVE-SUMMARY.md](./EXECUTIVE-SUMMARY.md)
**Comprehensive business overview including:**
- Vision & mission
- Current state (MVP 1.0)
- Market analysis & competitive advantages
- Business model evolution
- Scalability architecture
- Investment strategy
- 5-year roadmap
- Success metrics
- Exit strategy

**Use for:**
- Investor pitches
- Partnership discussions
- Strategic planning sessions
- Team onboarding

---

#### [PRODUCT-ROADMAP.md](./planning/PRODUCT-ROADMAP.md)
**Detailed product evolution plan:**
- Phase 0: MVP ✅ (Q4 2025)
- Phase 1: Validation (Q1-Q2 2026)
- Phase 2: Pilot Program (Q2-Q3 2026)
- Phase 3: Scale (Q4 2026-2027)
- Phase 4: AI-Native Platform (2028-2029)
- Phase 5: Global Expansion (2030+)

**Includes:**
- Feature prioritization
- Success metrics per phase
- Risk mitigation strategies
- Technology evolution
- Open research questions

**Use for:**
- Sprint planning
- Feature prioritization
- Stakeholder updates
- Fundraising conversations

---

### Technical Documents

#### [ARCHITECTURE.md](./technical/ARCHITECTURE.md)
**Complete technical overview:**
- System architecture (MVP → Future)
- Component details (Landing Page, Widget, PHP backend)
- Data flow diagrams
- Deployment architecture
- Security & privacy measures
- Performance optimization
- Future scaling plans

**Use for:**
- Developer onboarding
- Architecture reviews
- Technical due diligence
- Migration planning

---

#### [email-ai-prompt.md](./technical/email-ai-prompt.md)
**AI-powered email generation:**
- System prompt for GPT-4
- Tone guidelines
- Structure templates
- Audience-specific customization
- Australian English standards

**Use for:**
- Maintaining email consistency
- Training new AI models
- Updating confirmation templates

---

### Guides

#### [notion-database-setup.md](./guides/notion-database-setup.md)
**Notion CRM configuration:**
- Database schema
- Property definitions
- Audience segmentation
- Email preference management
- Best practices

**Use for:**
- Setting up new Notion workspace
- CRM customization
- Lead management workflows

---

## 🎯 Project Status (October 2025)

### ✅ Live & Operational
- **Landing Page:** https://therapair.com.au
- **Demo Widget:** https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/
- **Notion CRM:** Active lead capture
- **AI Emails:** Personalized confirmations working

### 📊 Current Metrics
- **Partnerships:** 1 (Unison Mental Health)
- **Therapists:** 8 active profiles
- **Email Delivery:** 100% success rate
- **Users Tested:** In progress

### 🔜 Next Milestones (Q1 2026)
- [ ] 100+ users complete matching quiz
- [ ] 3-5 clinic pilot agreements signed
- [ ] Match quality validated (80%+ satisfaction)
- [ ] $150K-300K pre-seed funding secured

---

## 🛠️ Tech Stack

### Current (MVP)
```
Frontend:  Vanilla JavaScript + HTML5 + CSS3
Backend:   PHP (Hostinger)
AI:        OpenAI GPT-4 API
Database:  Notion API (CRM)
Email:     PHP mail() + HTML templates
Hosting:   Hostinger (shared hosting)
Deploy:    Git + SSH
```

### Phase 2 Target (Q2 2026)
```
Frontend:  React (Next.js)
Backend:   Node.js (Express) or Python (FastAPI)
Database:  PostgreSQL (Supabase/Railway)
Auth:      Clerk or Auth0
Email:     SendGrid or Postmark
Hosting:   Vercel + Railway
Deploy:    GitHub Actions (CI/CD)
```

---

## 📝 Documentation Standards

### When to Create New Docs

**Always document:**
- New features (spec before building)
- Architecture changes (before implementing)
- API changes (update immediately)
- Deployment procedures (after first successful deploy)
- Security incidents (post-mortem)

**Optional but encouraged:**
- Bug fixes (if complex or recurring)
- Performance improvements (with benchmarks)
- User research findings (synthesize insights)

### Document Format

Use Markdown (`.md`) files with:
- Clear headings (##, ###)
- Code blocks with language specification
- Diagrams (ASCII art or Mermaid)
- Tables for comparisons
- Links to related docs

**Template:**
```markdown
# [Document Title]

**Purpose:** One-sentence description

## Overview
Brief introduction

## Details
Main content

## Related Documents
- [Link 1]
- [Link 2]

---
*Last Updated: YYYY-MM-DD*
```

---

## 🔄 Documentation Maintenance

### Regular Updates

**Weekly:**
- Update roadmap progress (mark completed items)
- Log key decisions in planning docs

**Monthly:**
- Review executive summary (update metrics)
- Archive outdated documents
- Update architecture if tech changes

**Quarterly:**
- Full documentation audit
- Update all "Last Updated" timestamps
- Reorganize if structure is confusing

### Version Control

All documentation is version-controlled in Git alongside code.

**Commit messages for docs:**
```
docs: Add Phase 2 feature specs
docs: Update architecture for API migration
docs: Fix typos in deployment guide
```

---

## 🤝 Contributing

### For Team Members

1. **Propose changes via PR:**
   - Create branch: `docs/your-change-name`
   - Update relevant documents
   - Submit PR for review

2. **Keep docs close to code:**
   - When adding a feature, update docs in same PR
   - When changing architecture, update `ARCHITECTURE.md`

3. **Ask before reorganizing:**
   - Discuss major structural changes first
   - Keep URLs stable (important for external links)

### For External Contributors

We're not currently accepting external contributions to documentation, but we appreciate:
- Typo reports (create an issue)
- Clarity suggestions (email tino@unisoncounselling.com)
- Use case examples (we might feature them!)

---

## 📧 Contact

**Questions about documentation?**
- **Email:** tino@unisoncounselling.com
- **GitHub:** @captuspario (for PRs/issues)

**Need a specific document?**
If you can't find what you're looking for, let us know. We'll either point you to it or create it!

---

## 🗂️ Document Index

### Strategic
- [Executive Summary](./EXECUTIVE-SUMMARY.md) - Business overview & vision
- [Product Roadmap](./planning/PRODUCT-ROADMAP.md) - 5-year feature plan

### Technical
- [Architecture](./technical/ARCHITECTURE.md) - System design & tech stack
- [Email AI Prompt](./technical/email-ai-prompt.md) - AI configuration

### Guides
- [Notion Setup](./guides/notion-database-setup.md) - CRM configuration

### Archive
- [Widget Spacing Plan](./archive/fix-widget-card-spacing.plan.md) - Historical planning

---

## 📅 Recent Updates

**October 10, 2025:**
- ✅ Created executive summary
- ✅ Documented complete architecture
- ✅ Created 5-year product roadmap
- ✅ Organized documentation structure
- ✅ Added this README

**Next Planned Updates:**
- [ ] API documentation (when Phase 2 starts)
- [ ] Deployment automation guide
- [ ] User research synthesis document
- [ ] Clinic onboarding playbook

---

*This documentation hub is maintained by the Therapair team and updated regularly.*  
*Last Major Update: October 10, 2025*



