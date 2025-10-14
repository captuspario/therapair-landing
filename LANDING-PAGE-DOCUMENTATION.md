# Therapair Landing Page - Complete Documentation

**Version**: 1.0  
**Last Updated**: 2025-10-10  
**Status**: Production  
**URL**: https://therapair.com.au

---

## 📑 Table of Contents

1. [Executive Summary](#executive-summary)
2. [Business Strategy & Goals](#business-strategy--goals)
3. [User Personas & Journeys](#user-personas--journeys)
4. [Information Architecture](#information-architecture)
5. [Content Strategy](#content-strategy)
6. [Technical Stack](#technical-stack)
7. [Form System](#form-system)
8. [Integration Systems](#integration-systems)
9. [Deployment & Hosting](#deployment--hosting)
10. [Analytics & Tracking](#analytics--tracking)
11. [Maintenance & Updates](#maintenance--updates)
12. [Future Roadmap](#future-roadmap)

---

## Executive Summary

### What is Therapair?

Therapair is a **therapist-matching concierge service** designed specifically for diverse communities and marginalised groups. Built with real humans and real care, we're revolutionising how people find mental health support that truly understands their identity and needs.

### Landing Page Purpose

The landing page serves as the **primary entry point** for all Therapair stakeholders:
- Individuals seeking therapy
- Mental health professionals
- Organisations and clinics
- Supporters and investors

**Primary Goal**: Capture early interest during development phase and build a qualified database of future users.

### Key Metrics

- **Form Completion Rate**: Track % of visitors who submit interest
- **Audience Distribution**: Monitor which personas show most interest
- **Email Engagement**: Measure open/click rates on confirmation emails
- **Notion Database Growth**: Track submissions by audience type

---

## Business Strategy & Goals

### Mission Statement

*"To create truly inclusive mental health care by connecting people with therapists who understand their unique identities, experiences, and needs."*

### Current Phase: Early Development

We are in **beta/research mode**:
- ✅ Gathering user interest and feedback
- ✅ Building qualified database of potential users
- ✅ Learning what matters most to each audience
- ✅ Validating product-market fit
- ❌ NOT actively matching yet
- ❌ NOT taking payments yet

### Business Goals

1. **Build Community**: 500+ interested individuals in database
2. **Recruit Therapists**: 50+ inclusive practitioners expressing interest
3. **Establish Partnerships**: 10+ organisations/clinics interested
4. **Validate Demand**: Prove market need for inclusive therapy matching
5. **Gather Insights**: Understand what features matter most to users

### Value Proposition

**For Individuals**:
- Personalised matching with therapists who truly understand them
- Focus on LGBTQ+, neurodivergent, culturally diverse communities
- Safe, affirming, culturally competent care

**For Therapists**:
- Connect with clients who match their expertise and values
- Be part of an inclusive practitioner network
- Serve diverse populations effectively

**For Organisations**:
- Better referral processes
- Improved client outcomes
- Partnership opportunities

**For Supporters**:
- Support inclusive mental health innovation
- Be part of the solution
- Investment/advocacy opportunities

---

## User Personas & Journeys

### Persona 1: Individual Seeking Therapy

**Profile**:
- Age: 18-45
- Identity: LGBTQ+, neurodivergent, culturally diverse
- Pain Point: Difficulty finding therapists who understand their identity
- Motivation: Want affirming, competent care from someone who "gets it"

**User Journey**:
```
Landing → Read about Therapair → Select "Individual" → 
Choose therapy interests → Share thoughts → Submit email → 
Receive AI confirmation → Wait for launch notification
```

**Form Fields**:
- Therapy Interests (multi-select): LGBTQ+ affirming care, Neurodiversity support, Cultural competency, Trauma-informed care, Anxiety & depression, Relationship issues
- Additional Thoughts (free text)
- Email

**Outcome**: Entry in Notion with high interest level, default email preferences

---

### Persona 2: Mental Health Professional

**Profile**:
- Role: Psychologist, counsellor, therapist
- Specialty: Inclusive practice, diverse populations
- Pain Point: Want to connect with clients who value their specific expertise
- Motivation: Be part of inclusive network, serve target communities

**User Journey**:
```
Landing → Read about network → Select "Therapist" → 
Enter credentials → Share specialisations → Submit → 
Receive confirmation → Await onboarding info
```

**Form Fields**:
- Full Name
- Professional Title
- Organisation/Practice
- Areas of Specialisation (free text)
- Email

**Outcome**: Entry in Notion with "Pending" verification status, "Interest" onboarding stage

---

### Persona 3: Organisation/Clinic

**Profile**:
- Type: Mental health clinic, community health centre, EAP provider
- Size: Small to enterprise
- Pain Point: Need better referral processes for diverse clients
- Motivation: Improve client outcomes, partnership opportunities

**User Journey**:
```
Landing → Read about partnerships → Select "Organisation" → 
Enter contact details → Share partnership interest → Submit → 
Receive confirmation → Await partnership discussion
```

**Form Fields**:
- Contact Name
- Position/Title
- Organisation Name
- Partnership Interest (free text)
- Email

**Outcome**: Entry in Notion with "Collaboration" partnership type

---

### Persona 4: Supporter/Investor

**Profile**:
- Type: Angel investor, advisor, advocate, volunteer
- Interest: Social impact, mental health innovation, inclusive care
- Pain Point: Want to support meaningful change in mental health
- Motivation: Be part of the solution, investment opportunity

**User Journey**:
```
Landing → Read about mission → Select "Supporter" → 
Share how they'd like to help → Submit → 
Receive confirmation → Stay updated on progress
```

**Form Fields**:
- Name
- Support Interest (free text)
- Email

**Outcome**: Entry in Notion with "Advocate" support type, "High" engagement level

---

## Information Architecture

### Page Structure

```
┌─────────────────────────────────────────┐
│  HERO SECTION                           │
│  - Value proposition                     │
│  - CTA button                            │
│  - Trust indicators                      │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  HOW IT WORKS                           │
│  - 3-step process                        │
│  - Visual explanation                    │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  WHO IT'S FOR                           │
│  - 5 audience cards                      │
│  - Individuals, Therapists, Orgs,       │
│    Supporters, Investors                 │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  MAIN FORM (Dynamic)                    │
│  - Audience selection                    │
│  - Dynamic fields per audience           │
│  - Submit CTA                            │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  TRUST & BENEFITS                       │
│  - Early access benefits                 │
│  - Privacy protection                    │
│  - Inclusive by design                   │
└─────────────────────────────────────────┘
┌─────────────────────────────────────────┐
│  FOOTER                                 │
│  - Copyright                             │
│  - Brand message                         │
└─────────────────────────────────────────┘
```

### Navigation Flow

```
index.html → submit-form.php → thank-you.html
              ↓
              email-preferences.html (via email link)
              ↓
              update-preferences.php
```

---

## Content Strategy

### Messaging Framework

**Core Message**: "You're one of the first to explore this with us"

**Tone & Voice**:
- ✅ **Warm but professional** - Not overly enthusiastic
- ✅ **Conversational** - "Thanks so much for taking the time..."
- ✅ **Human-centred** - "Built with real humans and real care"
- ✅ **Inclusive** - Acknowledges diverse identities
- ✅ **Honest** - Transparent about being in early development
- ✅ **Australian English** - Personalised, organisation, specialisation

**What We Avoid**:
- ❌ Over-promising ("We'll match you immediately!")
- ❌ Corporate jargon ("Dear User", "Request has been received")
- ❌ Excessive exclamation points!!
- ❌ Clinical/medical language
- ❌ Pressure or urgency

### Key Headlines

1. **Hero**: "Match with a therapist who truly understands you"
2. **Value Prop**: "Finding the right therapist shouldn't be overwhelming"
3. **CTA Section**: "Join our early development phase"
4. **Form Title**: "You're one of the first to explore this with us"

### Content Principles

1. **Acknowledge Identity**: Explicitly mention LGBTQ+, neurodivergent, culturally diverse
2. **Set Expectations**: Clear that we're in early development, not active matching
3. **Show Value**: Explain what makes us different (concierge, human-centred)
4. **Build Trust**: Privacy protection, inclusive by design messaging
5. **Create FOMO**: Early access benefits, be first to experience

---

## Technical Stack

### Frontend

**Core Technologies**:
- HTML5
- CSS3 (Custom properties for theming)
- Vanilla JavaScript (No frameworks)
- Tailwind CSS (CDN - to be replaced in production)
- Lucide Icons (CDN)

**Why No Framework?**:
- Simplicity and speed for MVP
- Easy for anyone to edit
- Minimal dependencies
- Fast page load
- Future migration path available

**Browser Support**:
- Modern browsers (Chrome, Firefox, Safari, Edge)
- Mobile-first responsive design
- Progressive enhancement approach

### Backend

**Technologies**:
- PHP 7.4+ (Hostinger default)
- No database (Notion serves as database)
- File-based configuration

**Key Files**:
```
index.html              - Landing page
submit-form.php         - Form handler & email sender
thank-you.html          - Success page
email-preferences.html  - Preference management page
update-preferences.php  - Preference handler
config.php             - Sensitive configuration (not in git)
notion-sync.php        - Notion API integration
```

### Styling System

**CSS Variables** (Therapair Brand Colors):
```css
--therapair-primary: #2563eb    (Blue)
--therapair-secondary: #8b5cf6  (Purple)
--therapair-accent: #10b981     (Green)
--therapair-success: #059669    (Dark Green)
--therapair-text: #1e293b       (Dark Gray)
--therapair-gray: #64748b       (Medium Gray)
--therapair-background: #f8fafc (Light Gray)
```

**Design System**:
- Border radius: 12px (cards), 8px (buttons/inputs)
- Spacing: 4px base unit (Tailwind scale)
- Typography: System fonts for performance
- Shadows: Subtle, layered elevation

---

## Form System

### Form Architecture

**Dynamic Form Behavior**:
1. User selects audience type
2. JavaScript shows relevant fields for that audience
3. Other fields are hidden AND disabled (prevents validation issues)
4. On submit: JavaScript collects multi-checkbox values
5. PHP processes, sends emails, syncs to Notion
6. User redirected to thank-you page

### Field Validation

**Client-Side**:
- HTML5 required attributes (dynamic based on audience)
- Email format validation
- Fields disabled when hidden

**Server-Side**:
- Audience type required
- Email required and validated
- Data sanitisation (htmlspecialchars, strip_tags, trim)
- Honeypot spam protection

### Form Field Naming

**Convention**: `Field_Name` (PascalCase with underscores)

**All Fields**:
- `Audience_Type` - hidden, set by JavaScript
- `Email` - required for all
- `Therapy_Interests` - hidden, populated from checkboxes
- `Additional_Thoughts` - Individual only
- `Full_Name` - Therapist only
- `Professional_Title` - Therapist only
- `Organization` - Therapist only (note: US spelling in code)
- `Specializations` - Therapist only
- `Contact_Name` - Organisation only
- `Position` - Organisation only
- `Organization_Name` - Organisation only
- `Partnership_Interest` - Organisation only
- `Name` - Supporter only
- `Support_Interest` - Supporter only

### JavaScript Functions

```javascript
handleAudienceSelection(audience)
  - Shows/hides field groups
  - Enables/disables inputs
  - Sets required attributes
  
collectTherapyInterests()
  - Gathers checkbox values
  - Concatenates to comma-separated string
  - Populates hidden field
  
Form submit event
  - Calls collectTherapyInterests()
  - Lets form submit normally
```

---

## Integration Systems

### 1. Email System

**Provider**: Hostinger PHP `mail()` function

**Email Flow**:
```
Form Submit → Admin Email → User Email → Redirect
```

**Admin Email**:
- **To**: contact@therapair.com.au
- **Subject**: "New [Audience] Interest Form Submission"
- **Content**: All form data in readable format
- **Headers**: From noreply@therapair.com.au, X-Mailer, X-Priority

**User Confirmation Email**:
- **To**: User's email
- **Subject**: "Thank you for your interest in Therapair"
- **Content**: AI-generated personalised message OR static template fallback
- **Headers**: From noreply@therapair.com.au, HTML content-type
- **Includes**: Email preference management link

**Email Deliverability**:
- SPF/DKIM records configured (see `email-deliverability-guide.md`)
- Professional from address (noreply@therapair.com.au)
- Proper headers (X-Mailer, X-Priority)
- HTML email format

---

### 2. AI Personalisation System

**Provider**: OpenAI API  
**Model**: gpt-4o-mini (cost-effective, fast)

**Configuration**:
```php
USE_AI_PERSONALIZATION = true
AI_MODEL = 'gpt-4o-mini'
OPENAI_API_KEY = [stored in config.php]
```

**AI Email Generation Flow**:
```
1. Build context from form data (audience-specific)
2. Load system prompt from email-ai-prompt.md strategy
3. Call OpenAI API with context
4. Wrap AI content in HTML email template
5. Send to user
6. If AI fails: fallback to static template
```

**System Prompt Strategy** (see `email-ai-prompt.md`):
- Warm, conversational, human tone
- 2-3 paragraphs maximum
- Thank them for sharing
- Explain Therapair (concierge experience)
- Set expectations (early development, updates)
- Avoid over-promising
- Sign-off: "Warm regards, Therapair Team"

**Context Building**:
- Individual: therapy interests, additional thoughts
- Therapist: title, specialisations
- Organisation: org name, partnership interest
- Supporter: support interest

**Fallback System**:
- Static templates for each audience type
- Activated if AI fails or API key missing
- Still personalised based on form data

---

### 3. Notion Database Integration

**Provider**: Notion API  
**Database ID**: `2875c25944da80c0b14afbbdf2510bb0`

**Sync Flow**:
```
Form Submit → Email Sent → Notion Sync → Redirect
(Notion sync won't block user experience if it fails)
```

**Database Structure**:

**Core Properties** (all entries):
- Name (Title) - Auto-generated
- Email (Email type)
- Audience Type (Select)
- Submission Date (Date)
- Status (Status: New, Contacted, etc.)
- Email Preferences (Multi-select)
- Unsubscribed (Checkbox)
- Last Contacted (Date)
- Notes (Long text)

**Individual Properties**:
- Therapy Interests (Multi-select)
- Additional Thoughts (Long text)
- Interest Level (Select: High)

**Therapist Properties**:
- Full Name (Text)
- Professional Title (Text)
- Organisation (Text)
- Specialisations (Long text)
- Verification Status (Select: Pending)
- Onboarding Stage (Select: Interest)

**Organisation Properties**:
- Contact Name (Text)
- Position (Text)
- Organisation Name (Text)
- Partnership Interest (Long text)
- Partnership Type (Select: Collaboration)

**Supporter Properties**:
- Name (from title)
- Support Interest (Long text)
- Support Type (Select: Advocate)
- Engagement Level (Select: High)

**Default Values**:
- Status: "New"
- Email Preferences: Product Updates, Launch News, Research & Feedback (+audience-specific)
- Interest Level / Engagement Level: "High"

**Error Handling**:
- Logs errors to PHP error_log
- Doesn't block form submission
- User experience not affected by Notion downtime

---

### 4. Email Preference Management

**User Flow**:
```
Confirmation Email → Click "Manage Preferences" →
email-preferences.html → Select preferences → Submit →
update-preferences.php → Confirmation email
```

**Preference Categories**:
- Product Updates
- Launch News  
- Therapist Opportunities (therapists only)
- Partnership News (organisations only)
- Research & Feedback
- Event Invitations
- Investment Updates (supporters only)

**Unsubscribe Options**:
- Full unsubscribe (all communications)
- Selective unsubscribe (choose categories)

**Implementation**:
- HTML form with checkboxes
- PHP handler logs to file (future: update Notion)
- Confirmation email sent

**Future Enhancement**:
- Direct Notion integration
- Update user preferences in database
- Honour preferences in email campaigns

---

## Deployment & Hosting

### Hosting Provider

**Provider**: Hostinger  
**Server**: Shared hosting  
**SSH Access**: Port 65002  
**PHP Version**: 7.4+  

**File Structure**:
```
domains/therapair.com.au/public_html/
├── index.html
├── thank-you.html
├── email-preferences.html
├── submit-form.php
├── update-preferences.php
├── notion-sync.php
├── config.php (not in git)
├── .gitignore
└── [documentation files]
```

### Deployment Process

**Method**: Git-based deployment

**Setup**:
1. GitHub repository: `captuspario/therapair-landing`
2. Hostinger has Git remote configured
3. Local deployment script automates push + pull

**Deploy Command**:
```bash
./deploy-to-hostinger.sh
```

**Script Actions**:
1. Commits and pushes to GitHub
2. SSHs into Hostinger
3. Pulls latest from GitHub
4. Confirms deployment

**Manual Deployment**:
```bash
cd /Users/tino/Projects/therapair-landing-page
git add .
git commit -m "Your message"
git push github main
ssh u549396201@45.87.81.159 -p 65002 \
  'cd domains/therapair.com.au/public_html && git pull origin main'
```

### Configuration Management

**Sensitive Data**: Stored in `config.php` (not committed to Git)

**config.php Contents**:
```php
// Email
ADMIN_EMAIL
FROM_EMAIL
FROM_NAME

// OpenAI
OPENAI_API_KEY
USE_AI_PERSONALIZATION
AI_MODEL

// Notion
NOTION_TOKEN
NOTION_DATABASE_ID
USE_NOTION_SYNC

// Website
WEBSITE_URL
THANK_YOU_URL
```

**config.example.php**: Template in Git for reference

### Environment Variables

**Production** (Hostinger):
- Uses `config.php` with real credentials
- AI personalization: ENABLED
- Notion sync: ENABLED

**Development** (Local):
- Copy `config.example.php` to `config.php`
- Add your own API keys
- Test before deploying

---

## Analytics & Tracking

### Current Tracking

**Form Submissions**:
- Tracked in Notion database
- Admin email notifications
- Submission timestamp recorded

**Metrics Available in Notion**:
- Total submissions
- Submissions by audience type
- Therapy interests distribution (individuals)
- Submission date/time
- Email preferences selected

### Recommended Analytics

**Google Analytics 4** (to implement):
```javascript
// Add to index.html
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-XXXXXXXXXX');
</script>
```

**Events to Track**:
- Page views (landing, thank-you)
- CTA button clicks ("Request Early Access")
- Audience type selection
- Form submissions (by audience)
- Form abandonment
- Email preference clicks

**Conversion Goals**:
- Form completion rate
- Email confirmation open rate
- Preference management engagement

---

## Maintenance & Updates

### Regular Maintenance

**Weekly**:
- ✅ Check Notion database for new entries
- ✅ Review admin emails for submissions
- ✅ Monitor form submission patterns

**Monthly**:
- ✅ Review spam submissions (honeypot effectiveness)
- ✅ Check email deliverability (spam rates)
- ✅ Update content based on user feedback
- ✅ Backup Notion database

**Quarterly**:
- ✅ Review AI email quality
- ✅ Update messaging/copy based on insights
- ✅ Assess audience distribution
- ✅ Iterate on form fields if needed

### Updating Content

**Copy Changes**:
1. Edit `index.html` directly
2. Test locally
3. Commit and deploy

**Email Template Changes**:
1. Edit `email-ai-prompt.md` for AI emails
2. Edit `submit-form.php` for fallback templates
3. Test with real submissions
4. Deploy

**Form Field Changes**:
1. Update HTML in `index.html`
2. Update JavaScript field handling
3. Update `submit-form.php` data collection
4. Update `notion-sync.php` property mapping
5. Update Notion database schema
6. Test all 4 journeys
7. Deploy

### Troubleshooting

**Form Not Submitting**:
- Check browser console for JavaScript errors
- Verify all required fields visible and enabled
- Check server error logs

**Emails Not Sending**:
- Check Hostinger email quota
- Verify email addresses in config.php
- Check spam folders
- Review SPF/DKIM records

**Notion Sync Failing**:
- Verify integration token is valid
- Confirm database is shared with integration
- Check property names match exactly
- Review PHP error logs

**AI Emails Not Working**:
- Verify OpenAI API key is valid
- Check API quota/billing
- Review error logs for API errors
- Fallback templates should still work

### Common Issues

**Issue**: "An invalid form control with name='X' is not focusable"  
**Solution**: Hidden fields can't be required. JavaScript should remove `required` from hidden fields.

**Issue**: Emails going to spam  
**Solution**: Check `email-deliverability-guide.md`, configure SPF/DKIM/DMARC

**Issue**: Multi-checkbox values not captured  
**Solution**: Verify JavaScript `collectTherapyInterests()` runs before submit

**Issue**: Notion "object_not_found" error  
**Solution**: Share database with integration in Notion settings

---

## Future Roadmap

### Phase 1: Current (Early Development) ✅

- ✅ Landing page live
- ✅ Multi-journey form system
- ✅ Email confirmations (AI-powered)
- ✅ Notion database integration
- ✅ Email preference management

### Phase 2: Launch Preparation (Q1 2026)

**Features**:
- [ ] Google Analytics integration
- [ ] A/B testing on CTAs
- [ ] Video explainer on landing page
- [ ] Testimonials section
- [ ] FAQ section
- [ ] Blog/news section for updates
- [ ] Social proof (number of sign-ups)

**Technical**:
- [ ] Move from Tailwind CDN to build process
- [ ] Optimise images (WebP format)
- [ ] Add service worker for offline
- [ ] Implement proper SEO (meta tags, schema.org)
- [ ] Set up staging environment

### Phase 3: Beta Launch (Q2 2026)

**Features**:
- [ ] User dashboard (check application status)
- [ ] Waitlist position indicator
- [ ] Referral program
- [ ] Early access tiers
- [ ] Email nurture sequence
- [ ] Community building (newsletter, events)

**Technical**:
- [ ] User authentication system
- [ ] Database migration (Notion → proper DB)
- [ ] API development
- [ ] Admin panel for team

### Phase 4: Active Matching (Q3 2026)

**Features**:
- [ ] Full matching algorithm
- [ ] Therapist profiles
- [ ] Booking system
- [ ] Payment integration
- [ ] Session management
- [ ] Messaging/communication platform

**Technical**:
- [ ] Complete platform rebuild (React/Next.js)
- [ ] Scalable backend (Node.js/PostgreSQL)
- [ ] Security audit
- [ ] HIPAA compliance
- [ ] Data encryption

---

## Appendices

### Related Documentation Files

- `README.md` - Quick start and overview
- `email-ai-prompt.md` - AI email generation strategy
- `notion-database-setup.md` - Notion database structure guide
- `email-deliverability-guide.md` - Email best practices
- `NOTION-DATABASE-AUDIT.md` - Field mapping verification
- `config.example.php` - Configuration template

### Key Decisions & Rationale

**Why PHP instead of Node.js?**  
Hostinger shared hosting limitation + simplicity for MVP

**Why Notion instead of database?**  
Speed of setup, built-in UI, no database management overhead for early stage

**Why AI emails?**  
Personalised user experience at scale without manual work

**Why multi-journey form instead of separate pages?**  
Single entry point easier to maintain, better UX, clearer value proposition

**Why Australian English?**  
Target market is Australia, builds trust with local audience

**Why no framework?**  
Simplicity, speed, accessibility to non-developers for content updates

---

## Version History

### v1.0 (2025-10-10)
- Initial production release
- 4 user journeys implemented
- AI email integration
- Notion database sync
- Email preference management
- Australian English conversion
- Complete documentation

---

## Contact & Support

**Technical Issues**: See troubleshooting section above  
**Content Updates**: Edit relevant files and deploy  
**API Issues**: Check respective service dashboards (OpenAI, Notion)  
**Hosting Issues**: Contact Hostinger support

**Documentation Maintained By**: Therapair Development Team  
**Last Review**: 2025-10-10

---

**End of Documentation**



