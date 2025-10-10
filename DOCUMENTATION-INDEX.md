# Therapair Documentation Index

**Last Updated**: 2025-10-10  
**Total Documentation**: 10 files, 6,000+ lines

---

## 🎯 START HERE

### **New to Therapair?**
👉 Read: **`THERAPAIR-README.md`**

### **Need to Update Something?**
👉 Find the specific doc below

### **Understanding the Whole System?**
👉 Read: **`THERAPAIR-CENTRAL-DOCUMENTATION.md`**

---

## 📚 Complete Documentation Suite

### **Level 1: Overview & Strategy**

| Document | Purpose | Audience |
|----------|---------|----------|
| **THERAPAIR-README.md** | Master index, quick start, project overview | Everyone |
| **THERAPAIR-CENTRAL-DOCUMENTATION.md** | Central hub linking all projects | Developers, PMs |

### **Level 2: Systems & Architecture**

| Document | Purpose | Audience |
|----------|---------|----------|
| **LANDING-PAGE-DOCUMENTATION.md** | Complete landing page system | Developers, PMs, Content |
| **THERAPAIR-DESIGN-SYSTEM.md** | Unified design system (colors, typography, components) | Designers, Developers |
| **THERAPAIR-SCALABLE-INFRASTRUCTURE.md** | Email & database scalability templates | Developers, Architects |

### **Level 3: Operations & Implementation**

| Document | Purpose | Audience |
|----------|---------|----------|
| **notion-database-setup.md** | Notion database structure & setup | Ops, PMs |
| **email-ai-prompt.md** | AI email generation strategy | Content, Developers |
| **email-deliverability-guide.md** | Email best practices & DNS setup | DevOps, Developers |
| **NOTION-DATABASE-AUDIT.md** | Field mapping verification | QA, Developers |

### **Level 4: Widget (Separate Project)**

| Document | Location | Purpose |
|----------|----------|---------|
| **WIDGET-DOCUMENTATION.md** | `therapair-widget-unison/` | Complete widget system |
| *+ All central docs copied there* | `therapair-widget-unison/` | Shared knowledge |

---

## 🗺️ Documentation Map

```
THERAPAIR-README.md (START HERE)
    ├─→ THERAPAIR-CENTRAL-DOCUMENTATION.md
    │       ├─→ THERAPAIR-DESIGN-SYSTEM.md
    │       ├─→ THERAPAIR-SCALABLE-INFRASTRUCTURE.md
    │       ├─→ LANDING-PAGE-DOCUMENTATION.md
    │       │       ├─→ notion-database-setup.md
    │       │       ├─→ email-ai-prompt.md
    │       │       ├─→ email-deliverability-guide.md
    │       │       └─→ NOTION-DATABASE-AUDIT.md
    │       └─→ WIDGET-DOCUMENTATION.md
    │
    └─→ Quick reference for common tasks
```

---

## 🎯 Common Tasks → Documentation

### **"I need to update the landing page copy"**
📖 Read: **LANDING-PAGE-DOCUMENTATION.md** → "Content Strategy" section

### **"I want to change the email tone"**
📖 Read: **email-ai-prompt.md** → Edit the system prompt

### **"I need to add a form field"**
📖 Read: **LANDING-PAGE-DOCUMENTATION.md** → "Form System" section  
📖 Also: **NOTION-DATABASE-AUDIT.md** for database mapping

### **"I want to update the design/colors"**
📖 Read: **THERAPAIR-DESIGN-SYSTEM.md** → Update CSS variables

### **"How do I deploy?"**
📖 Read: **LANDING-PAGE-DOCUMENTATION.md** → "Deployment & Hosting"

### **"I need to add the widget to Notion"**
📖 Read: **THERAPAIR-SCALABLE-INFRASTRUCTURE.md** → "Widget Booking Handler Template"

### **"What's the long-term plan?"**
📖 Read: **THERAPAIR-CENTRAL-DOCUMENTATION.md** → "Scalability Roadmap"

### **"I need to troubleshoot an issue"**
📖 Read: **LANDING-PAGE-DOCUMENTATION.md** → "Troubleshooting" section

---

## 📊 Documentation Coverage

### **Business & Strategy**
- ✅ Mission and values
- ✅ Business goals
- ✅ User personas (4 types)
- ✅ Value propositions
- ✅ Roadmap (4 phases)

### **Design & UX**
- ✅ Complete design system
- ✅ Brand guidelines
- ✅ Component library
- ✅ Accessibility standards
- ✅ Responsive design patterns

### **Technical**
- ✅ Frontend architecture
- ✅ Backend architecture
- ✅ Form system (validation, JavaScript)
- ✅ Email system (PHP, AI, templates)
- ✅ Notion integration (API, mapping)
- ✅ Database schema (current + future)
- ✅ API design (future)
- ✅ Deployment procedures

### **Operations**
- ✅ Maintenance schedules
- ✅ Troubleshooting guides
- ✅ Configuration management
- ✅ Email deliverability
- ✅ Analytics and tracking

### **Content**
- ✅ Voice and tone guidelines
- ✅ Messaging framework
- ✅ Australian English standards
- ✅ Copy examples
- ✅ AI prompt engineering

---

## 📈 Documentation Stats

| Metric | Count |
|--------|-------|
| **Total Documentation Files** | 10 |
| **Total Lines** | 6,000+ |
| **Total Words** | 45,000+ |
| **Sections** | 150+ |
| **Code Examples** | 50+ |
| **Diagrams** | 15+ |
| **Checklists** | 30+ |

---

## 🔄 Keeping Documentation Updated

### When to Update

**Always update docs when you**:
- ✅ Add new features
- ✅ Change user journeys
- ✅ Modify form fields
- ✅ Update email templates
- ✅ Change design system
- ✅ Add integrations
- ✅ Modify database schema

### How to Update

1. Find the relevant doc (use this index)
2. Make your changes
3. Update version number if major change
4. Update "Last Updated" date
5. Add to version history section
6. Commit with clear message

### Documentation Review Schedule

**Weekly**: Check for outdated info
**Monthly**: Full documentation audit
**Quarterly**: Major review and reorganisation if needed

---

## 💡 Best Practices

### Writing Documentation

1. **Be Specific**: Include exact file paths, line numbers, code examples
2. **Show Examples**: Code snippets, before/after, visual diagrams
3. **Explain Why**: Document decisions and rationale
4. **Keep Updated**: Update docs in same commit as code changes
5. **Link Between Docs**: Cross-reference related documentation

### Using Documentation

1. **Search First**: Use Cmd+F to find what you need
2. **Follow Links**: Documentation is interconnected
3. **Read Context**: Don't just copy/paste, understand why
4. **Ask Questions**: If docs unclear, improve them
5. **Share Knowledge**: Point teammates to relevant docs

---

## 🏆 Documentation Quality Standards

### ✅ Good Documentation

- Clear, scannable structure
- Practical examples
- Up-to-date information
- Explains "why" not just "what"
- Links to related docs
- Version history

### ❌ Bad Documentation

- Outdated information
- Missing context
- No examples
- Unclear purpose
- Dead links
- No version tracking

---

## 🎓 Learning Path

### **Week 1: Understand the System**
1. Read `THERAPAIR-README.md`
2. Read `THERAPAIR-CENTRAL-DOCUMENTATION.md`
3. Skim project-specific docs
4. Explore codebase

### **Week 2: Deep Dive**
1. Read `LANDING-PAGE-DOCUMENTATION.md`
2. Read `THERAPAIR-DESIGN-SYSTEM.md`
3. Review form system code
4. Test locally

### **Week 3: Advanced Topics**
1. Read `THERAPAIR-SCALABLE-INFRASTRUCTURE.md`
2. Understand Notion integration
3. Learn AI email system
4. Review database schema

### **Week 4: Contribute**
1. Make first documentation update
2. Fix small bug or issue
3. Deploy change
4. Add to version history

---

## 📞 Getting Help

### Documentation Issues

**Can't find what you need?**  
Check: `THERAPAIR-CENTRAL-DOCUMENTATION.md` → Full index

**Documentation outdated?**  
Update it! You're empowered to improve docs.

**Something unclear?**  
Add a note or question to the doc, commit it.

### Technical Support

**Landing Page**: See troubleshooting in `LANDING-PAGE-DOCUMENTATION.md`  
**Widget**: See `WIDGET-DOCUMENTATION.md`  
**Design**: See `THERAPAIR-DESIGN-SYSTEM.md`  
**Infrastructure**: See `THERAPAIR-SCALABLE-INFRASTRUCTURE.md`

---

## 🚀 Next Steps

After reading this index:

**For Developers**:
1. Read `THERAPAIR-CENTRAL-DOCUMENTATION.md`
2. Read `LANDING-PAGE-DOCUMENTATION.md`
3. Set up local environment
4. Make a test change

**For Designers**:
1. Read `THERAPAIR-DESIGN-SYSTEM.md`
2. Review current implementation
3. Suggest improvements
4. Document design decisions

**For Product Managers**:
1. Read `THERAPAIR-README.md`
2. Read `LANDING-PAGE-DOCUMENTATION.md` → Business section
3. Review analytics and metrics
4. Plan next features

**For Content Writers**:
1. Read `LANDING-PAGE-DOCUMENTATION.md` → Content Strategy
2. Read `email-ai-prompt.md`
3. Review current copy
4. Suggest improvements

---

## 📅 Version History

### v1.0 (2025-10-10)
- Initial documentation index created
- Complete cross-project documentation system established
- All projects aligned and documented

---

**Maintained By**: Therapair Development Team  
**Last Review**: 2025-10-10

---

*📖 Knowledge is power. Documentation is sharing that power with your team.*

