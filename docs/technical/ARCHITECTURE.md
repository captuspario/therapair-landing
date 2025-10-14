# Therapair: Technical Architecture

**Version:** 1.0 (MVP)  
**Last Updated:** October 10, 2025  
**Status:** Production (Unison Partnership + Landing Page)

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Current Architecture (MVP)](#current-architecture-mvp)
3. [Component Details](#component-details)
4. [Data Flow](#data-flow)
5. [Deployment Architecture](#deployment-architecture)
6. [Security & Privacy](#security--privacy)
7. [Performance Optimisation](#performance-optimisation)
8. [Future Architecture Plans](#future-architecture-plans)

---

## System Overview

Therapair consists of two main components:

1. **Landing Page** - Interest capture and lead generation
2. **Matching Widget** - Therapy matching quiz and booking system

Both are designed to be lightweight, performant, and easily embeddable while maintaining a foundation for future scalability.

---

## Current Architecture (MVP)

### High-Level Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     USERS                                    │
│  (Individuals, Therapists, Clinics, Investors)              │
└───────────────────┬─────────────────────────────────────────┘
                    │
        ┌───────────┴──────────────┐
        │                          │
┌───────▼────────┐        ┌───────▼─────────┐
│  Landing Page  │        │  Matching Widget│
│  (therapair.   │        │  (Embedded on   │
│   com.au)      │        │   partner sites)│
└───────┬────────┘        └───────┬─────────┘
        │                         │
        │                         │
   ┌────▼─────┐            ┌─────▼──────┐
   │ Submit   │            │ Submit     │
   │ Form     │            │ Booking    │
   └────┬─────┘            └─────┬──────┘
        │                        │
   ┌────▼─────────────────────────▼─────┐
   │     PHP Handlers (Hostinger)       │
   │  - submit-form.php (landing page)  │
   │  - submit-booking.php (widget)     │
   └─────┬──────────────────┬───────────┘
         │                  │
    ┌────▼──────┐      ┌───▼────────┐
    │ OpenAI    │      │ Notion     │
    │ API       │      │ API        │
    │ (Email    │      │ (CRM)      │
    │ AI)       │      │            │
    └───────────┘      └────────────┘
         │
    ┌────▼──────┐
    │ Email     │
    │ Delivery  │
    │ (PHP mail)│
    └───────────┘
```

---

## Component Details

### 1. Landing Page (`therapair-landing-page`)

**Purpose:** Lead generation, brand awareness, interest capture

**Tech Stack:**
- HTML5, CSS3 (Tailwind-inspired utility classes)
- Vanilla JavaScript (no frameworks)
- PHP backend (form handling)
- Lucide icons
- Google Fonts (Open Sans)

**Key Files:**
```
therapair-landing-page/
├── index.html                 # Main landing page
├── thank-you.html            # Post-submission confirmation
├── submit-form.php           # Form handler + AI email
├── notion-sync.php           # Notion API integration
├── email-ai-prompt.md        # AI email prompt template
├── config.php                # Environment variables (gitignored)
├── images/                   # Screenshots, assets
└── docs/                     # Documentation
    ├── EXECUTIVE-SUMMARY.md
    ├── technical/
    ├── planning/
    └── guides/
```

**Features:**
- Dynamic form fields based on audience selection
- Client-side validation
- AI-powered personalised confirmation emails
- Notion CRM sync for lead management
- Australian English localisation
- Mobile-responsive design
- SEO optimised

**Form Audiences:**
1. **Individuals** - People seeking therapy
2. **Practitioners** - Therapists joining platform
3. **Clinics** - Organisations interested in widget
4. **Supporters** - Investors, advisors, partners

---

### 2. Matching Widget (`therapair-widget-unison`)

**Purpose:** Intelligent therapy matching, booking request system

**Tech Stack:**
- Vanilla JavaScript (ES6+)
- HTML5, CSS3
- PHP backend (booking handler)
- Embedded as iframe or standalone

**Key Files:**
```
therapair-widget-unison/
├── therapair-widget/
│   ├── index.html              # Main widget (standalone)
│   ├── submit-booking.php      # Booking form handler
│   ├── booking-thank-you.html  # Confirmation page
│   └── .htaccess               # Cache control headers
│   └── images/                 # Therapist photos
├── src/
│   └── therapair-standalone.html  # Local dev version
├── deploy-widget-only.sh       # SSH deployment script
└── tests/                      # Screenshots, test files
```

**Features:**
- **9-Question Matching Quiz:**
  1. Who is seeking therapy?
  2. Age verification (18+)
  3. Therapy format (online/in-person)
  4. Primary concerns (anxiety, trauma, etc.)
  5. Gender preference
  6. Cultural background
  7. LGBTQIA+ affirmation
  8. Special requirements (neurodivergence, language)
  9. Previous therapy experience

- **Matching Algorithm:**
  - Multi-factor weighted scoring
  - Mandatory filters (e.g., LGBTQIA+ affirming)
  - Preferred filters (gender, cultural background)
  - Specialty matching (concerns → therapist expertise)
  - Sorted by match score (highest first)

- **Results Display:**
  - Therapist cards with photos, bios, specialities
  - All skills displayed (no "+X more")
  - Optimised spacing (100px card padding)
  - "Book Now" CTA (no "View Profile")

- **Booking System:**
  - Modal form with user preferences summary
  - Contact information capture
  - PHP email delivery (admin + user)
  - Professional confirmation emails

---

### 3. PHP Backend (`submit-form.php`, `submit-booking.php`)

**Purpose:** Server-side form processing, email generation, API integrations

**Common Flow:**
```
1. Receive POST data
2. Sanitize + validate inputs
3. Send admin notification email
4. Generate AI-powered user confirmation (landing page only)
5. Sync to Notion CRM (landing page only)
6. Redirect to thank-you page
```

**Key Functions:**

**`submit-form.php` (Landing Page):**
```php
// Form handling
sanitizeInput($data)
validateEmail($email)

// AI email generation
OpenAI API call with:
  - System prompt (email-ai-prompt.md)
  - User context (audience + responses)
  - Fallback templates if API fails

// Notion sync
notionSync($formData, $audience)
  - Create new database entry
  - Map form fields to Notion properties
  - Handle email preference checkboxes

// Email delivery
mail($to, $subject, $htmlBody, $headers)
```

**`submit-booking.php` (Widget):**
```php
// Booking handling
formatUserConfirmationEmail($firstName, $therapist, $preferences)
  - Simple, non-excited confirmation
  - Show therapist selection
  - Display user preferences
  - Clear "What happens next" section

// No AI, no Notion (MVP simplicity)
// Just reliable email delivery
```

---

### 4. Notion Integration (`notion-sync.php`)

**Purpose:** CRM for lead management, audience segmentation

**Database Structure:**
```
Therapair Leads (Database)
├── Title (Title)           - Auto-generated name
├── Email (Email)           - Primary contact
├── Audience Type (Select)  - Individual/Practitioner/Clinic/Supporter
├── Status (Status)         - New, Contacted, Qualified, etc.
├── Created (Created time)  - Auto timestamp
├── Phone (Phone)           - Optional
├── Location (Text)         - State/city
├── Message (Long text)     - Full form submission
├── Email Preferences (Multi-select) - Newsletter, Product Updates, etc.
└── [Audience-specific properties based on type]
```

**Audience-Specific Properties:**

**Individuals:**
- Therapy Concerns (Multi-select)
- Preferred Therapy Type (Select)
- Cultural Background (Text)
- LGBTQIA+ Affirmation (Checkbox)

**Practitioners:**
- Practice Type (Select)
- Years Experience (Number)
- Specialisations (Multi-select)
- Current Capacity (Select)

**Clinics:**
- Clinic Size (Select)
- Services Offered (Multi-select)
- Integration Interest (Select)

**Supporters:**
- Support Type (Select) - Investor, Advisor, Partner
- Interest Area (Multi-select)

---

### 5. AI Integration (OpenAI API)

**Purpose:** Personalised email generation for landing page leads

**Configuration:**
```
Model: gpt-4
Temperature: 0.7
Max Tokens: 800
```

**Prompt Structure:**
```
System Prompt (email-ai-prompt.md):
  - Role: Therapair team member
  - Tone: Professional, warm, conversational
  - Language: Australian English
  - Structure: Greeting, acknowledgment, next steps, sign-off
  - Constraints: No promises, no guarantees

User Prompt:
  - Audience type
  - User responses (concerns, preferences, etc.)
  - Specific context (e.g., individual vs practitioner)
```

**Fallback Strategy:**
- If OpenAI API fails → Use template email
- Template includes same structure, less personalisation
- Still mentions user's stated interests/concerns

**Cost Optimisation:**
- Only used for landing page (high-value leads)
- Widget uses static templates (volume play)
- Estimated cost: $0.01-0.03 per email

---

## Data Flow

### Landing Page Submission Flow

```
User fills form
    │
    ▼
JavaScript validation
    │
    ▼
POST to submit-form.php
    │
    ├──▶ Send admin notification (immediate)
    │
    ├──▶ Call OpenAI API (2-5 seconds)
    │    └──▶ Generate personalised email
    │
    ├──▶ Send user confirmation (immediate after AI)
    │
    ├──▶ Sync to Notion (parallel, non-blocking)
    │    └──▶ Create database entry with properties
    │
    └──▶ Redirect to thank-you.html
```

### Widget Matching Flow

```
User starts quiz
    │
    ▼
Answer 9 questions (client-side)
    │
    ▼
JavaScript matching algorithm
    │ (Scores each therapist against preferences)
    ▼
Display results (sorted by score)
    │
    ▼
User clicks "Book Now"
    │
    ▼
Booking modal opens (pre-filled preferences)
    │
    ▼
POST to submit-booking.php
    │
    ├──▶ Send admin notification
    │
    ├──▶ Send user confirmation
    │
    └──▶ Redirect to booking-thank-you.html
```

---

## Deployment Architecture

### Hosting Setup

**Landing Page:**
- **Host:** Hostinger
- **Domain:** therapair.com.au
- **Deployment:** Git-based (push to Hostinger Git remote)
- **SSL:** Hostinger-managed Let's Encrypt

**Widget:**
- **Host:** Hostinger (Unison account)
- **Path:** `/public_html/therapair-widget/`
- **Deployment:** SSH + SCP via `deploy-widget-only.sh`
- **Integration:** Embedded on Unison Mental Health website

### Deployment Process

**Landing Page:**
```bash
cd therapair-landing-page
git add .
git commit -m "feat: description"
git push origin main  # Auto-deploys to therapair.com.au
```

**Widget:**
```bash
cd therapair-widget-unison
./deploy-widget-only.sh

# Script does:
# 1. Test SSH connection
# 2. Create backup of existing files
# 3. Upload index.html via SCP
# 4. Upload images/ folder
# 5. Upload submit-booking.php and booking-thank-you.html
# 6. Set correct permissions (644 for HTML/PHP, 755 for directories)
# 7. Verify deployment
```

### Environment Configuration

**Landing Page (`config.php`):**
```php
<?php
// OpenAI Configuration
define('OPENAI_API_KEY', 'sk-...');

// Email Configuration
define('ADMIN_EMAIL', 'tino@unisoncounselling.com');
define('FROM_EMAIL', 'hello@therapair.com.au');
define('FROM_NAME', 'Therapair Team');

// Notion Configuration
define('NOTION_API_KEY', 'secret_...');
define('NOTION_DATABASE_ID', 'xxx-xxx-xxx');
?>
```

**Widget (`submit-booking.php`):**
```php
// Hardcoded (no config.php needed)
define('ADMIN_EMAIL', 'tino@unisonmentalhealth.com');
define('FROM_EMAIL', 'bookings@unisonmentalhealth.com');
define('FROM_NAME', 'Unison Mental Health Bookings');
```

---

## Security & Privacy

### Current Security Measures

**1. Input Sanitization:**
```php
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
```

**2. Email Validation:**
```php
filter_var($email, FILTER_VALIDATE_EMAIL)
```

**3. Honeypot Protection:**
- Hidden form field to catch bots
- Legitimate users never see/fill it
- Submissions with honeypot value are rejected

**4. Rate Limiting (Future):**
- Currently relying on Hostinger's server protections
- TODO: Implement PHP session-based rate limiting

**5. HTTPS Enforcement:**
- All pages served over SSL
- Forms only submit over HTTPS

**6. Data Storage:**
- **No PHI stored locally** (not HIPAA-regulated yet)
- Notion stores contact info + preferences only
- No payment info collected
- No session recordings or tracking pixels

### Privacy Considerations

**What We Collect:**

**Landing Page:**
- Name, email, phone (optional), location, message
- Audience-specific preferences (concerns, specialisations, etc.)
- Email preference checkboxes

**Widget:**
- Name, email, phone
- Matching quiz responses (concerns, preferences)
- Selected therapist + specialty

**What We Don't Collect:**
- Credit card or payment info
- Social security numbers or government IDs
- Detailed mental health history
- Session notes or clinical records

**Data Usage:**
- Lead follow-up (email, phone)
- Product updates (with consent)
- Aggregated analytics (anonymised)

**User Rights:**
- Unsubscribe from emails (link in footer)
- Request data deletion (email us)
- Update email preferences (Notion-based opt-out)

### Future Security Enhancements

**Phase 2 (Pilot):**
- [ ] Implement CSRF tokens
- [ ] Add rate limiting (5 submissions/hour per IP)
- [ ] Move to SendGrid (DMARC, SPF, DKIM)
- [ ] Add reCAPTCHA v3 (invisible)

**Phase 3 (Scale):**
- [ ] HIPAA compliance audit
- [ ] Encrypt sensitive data at rest
- [ ] SOC 2 Type II certification
- [ ] Penetration testing
- [ ] Bug bounty program

---

## Performance Optimisation

### Current Optimisations

**1. No JavaScript Frameworks:**
- Zero bundle size overhead
- Instant page load (<1 second)
- Vanilla JS is fast and predictable

**2. Minimal Dependencies:**
- Lucide icons (lightweight SVG library)
- Google Fonts (preconnect for speed)
- No jQuery, no React, no Bootstrap

**3. Efficient CSS:**
- Inline critical CSS in `<head>`
- Utility-first approach (Tailwind-inspired)
- No unused styles

**4. Image Optimisation:**
- Therapist photos compressed (JPEG, 80% quality)
- Lazy loading on images (`loading="lazy"`)
- Responsive images (CSS `max-width: 100%`)

**5. Caching Strategy:**
- `.htaccess` cache control headers
- No-cache for HTML (ensures fresh content)
- Long cache for images (1 year)

**Landing Page `.htaccess`:**
```apache
<IfModule mod_headers.c>
  # Cache images for 1 year
  <FilesMatch "\.(jpg|jpeg|png|gif|svg|webp)$">
    Header set Cache-Control "max-age=31536000, public"
  </FilesMatch>
  
  # No cache for HTML/PHP
  <FilesMatch "\.(html|php)$">
    Header set Cache-Control "no-cache, no-store, must-revalidate"
  </FilesMatch>
</IfModule>
```

**Widget `.htaccess`:**
```apache
<IfModule mod_headers.c>
    Header set Cache-Control "no-cache, no-store, must-revalidate"
    Header set Pragma "no-cache"
    Header set Expires "0"
</IfModule>
```

**6. Form Submission Optimisation:**
- Asynchronous Notion sync (doesn't block redirect)
- OpenAI fallback templates (never blocks user flow)
- Minimal PHP processing time (~100-300ms)

### Performance Metrics (Current)

**Landing Page (therapair.com.au):**
- **Time to First Byte (TTFB):** ~200ms
- **First Contentful Paint (FCP):** ~600ms
- **Largest Contentful Paint (LCP):** ~1.2s
- **Page Weight:** ~150KB (HTML + CSS + JS)
- **Lighthouse Score:** 95+ (Performance)

**Widget (embedded):**
- **Load Time:** ~400ms
- **Time to Interactive (TTI):** ~800ms
- **Page Weight:** ~180KB (includes therapist images)
- **Lighthouse Score:** 92+ (Performance)

---

## Future Architecture Plans

### Phase 2: API-First Architecture

**Goals:**
- Decouple frontend from backend
- Enable mobile app development
- Support multiple widget instances

**New Stack:**
```
Frontend: React (Next.js)
Backend: Node.js (Express) OR Python (FastAPI)
Database: PostgreSQL (Supabase or Railway)
Auth: Clerk or Auth0
File Storage: AWS S3 or Cloudflare R2
Email: SendGrid or Postmark
Hosting: Vercel (frontend) + Railway (backend)
```

**API Endpoints (v1):**
```
POST   /api/leads                  # Submit landing page form
POST   /api/matches                # Submit matching quiz
GET    /api/therapists             # Get therapist list
GET    /api/therapists/:id         # Get therapist details
POST   /api/bookings               # Submit booking request
GET    /api/clinics                # Get clinic list (admin)
POST   /api/clinics                # Create clinic account
GET    /api/analytics              # Get dashboard data
```

### Phase 3: Microservices

**Service Decomposition:**
```
┌────────────────┐
│  API Gateway   │  (Kong or AWS API Gateway)
└───────┬────────┘
        │
    ┌───┴───────────────────────────┐
    │                               │
┌───▼────────┐              ┌──────▼──────┐
│  Matching  │              │    User     │
│  Service   │              │   Service   │
│ (Python)   │              │  (Node.js)  │
└────────────┘              └─────────────┘
    │                               │
┌───▼────────┐              ┌──────▼──────┐
│  Clinic    │              │   Payment   │
│  Service   │              │   Service   │
│ (Node.js)  │              │  (Node.js)  │
└────────────┘              └─────────────┘
    │                               │
┌───▼────────┐              ┌──────▼──────┐
│  Email     │              │  Analytics  │
│  Service   │              │   Service   │
│  (Go)      │              │  (Python)   │
└────────────┘              └─────────────┘
```

**Benefits:**
- Independent scaling (match service scales differently than payment)
- Team autonomy (separate codebases, different languages)
- Fault isolation (one service failure doesn't crash system)
- Easier testing and deployment

### Phase 4: AI-Native Platform

**Advanced Matching Algorithm:**
```python
# Current: Rule-based scoring
def match_therapists(user_responses, therapist_db):
    for therapist in therapist_db:
        score = 0
        if user.lgbtqia_affirming and therapist.lgbtqia_affirming:
            score += 10  # Mandatory
        if user.preferred_gender == therapist.gender:
            score += 5   # Preferred
        # ... etc
    return sorted(therapists, key=lambda t: t.score, reverse=True)

# Future: ML-powered with continuous learning
def match_therapists_ml(user_vector, therapist_vectors, historical_data):
    # 1. Embed user preferences as vector
    user_embedding = embed_user_preferences(user_responses)
    
    # 2. Semantic similarity with therapists
    similarities = cosine_similarity(user_embedding, therapist_vectors)
    
    # 3. Adjust based on historical success rates
    for therapist_id, similarity in similarities:
        success_rate = get_booking_success_rate(therapist_id, user_profile_type)
        adjusted_score = similarity * (1 + success_rate_weight * success_rate)
    
    # 4. Rank and return
    return ranked_matches
```

**Agentic Workflows:**
1. **Automated Therapist Profiling:**
   - NLP extraction from bios/CVs
   - Auto-tag specialisations
   - Suggest profile improvements

2. **Intelligent Scheduling:**
   - Predict availability based on patterns
   - Coordinate timezones
   - Suggest optimal booking times

3. **Proactive Client Support:**
   - Pre-session preparation emails
   - Post-session check-ins (with consent)
   - Dropout risk prediction

4. **Continuous Match Optimisation:**
   - Learn from booking patterns
   - A/B test matching algorithms
   - Personalise quiz questions based on user type

---

## Monitoring & Observability (Future)

**Phase 2: Basic Monitoring**
- [ ] Sentry (error tracking)
- [ ] Uptime monitoring (Pingdom or UptimeRobot)
- [ ] Google Analytics 4
- [ ] Hotjar (session recordings, heatmaps)

**Phase 3: Advanced Observability**
- [ ] DataDog (APM, logs, traces)
- [ ] Mixpanel (product analytics)
- [ ] PagerDuty (incident management)
- [ ] Custom dashboards (Grafana)

**Key Metrics to Track:**
- API response times (p50, p95, p99)
- Error rates by endpoint
- Database query performance
- Email delivery rates
- Conversion funnels (quiz → booking)
- Match quality scores

---

## Development Workflow

### Current (MVP)
```
1. Local development (therapair-standalone.html for widget)
2. Test in browser
3. Commit to Git
4. Deploy manually:
   - Landing page: git push (auto-deploys)
   - Widget: ./deploy-widget-only.sh
5. Test on production
6. Fix bugs, repeat
```

### Future (Phase 2+)
```
1. Feature branch development
2. Local dev server (npm run dev)
3. Unit tests (Jest)
4. E2E tests (Playwright)
5. Pull request → GitHub Actions CI
   - Run tests
   - Lint code
   - Build preview
6. Merge to main → Auto-deploy to staging
7. Manual promotion to production
8. Monitor via DataDog/Sentry
```

---

## Technical Debt & Priorities

### Known Technical Debt

**High Priority:**
1. **Email Deliverability:** PHP mail() is unreliable, need SendGrid/Postmark
2. **No Rate Limiting:** Vulnerable to spam, need rate limiting
3. **Hardcoded Therapist Data:** Widget has therapists in HTML, need database
4. **No Admin Dashboard:** Can't manage therapists/clinics without database

**Medium Priority:**
1. **No Automated Testing:** Manual testing only, need Jest + Playwright
2. **Monolithic PHP Files:** submit-form.php does too much, need separation
3. **No Error Logging:** Using basic PHP error logs, need Sentry
4. **No A/B Testing:** Can't experiment with UI/copy

**Low Priority (Acceptable for MVP):**
1. **No CDN:** Hosting on single server (fine for low traffic)
2. **No Load Balancing:** Not needed at current scale
3. **No Caching Layer:** Redis not needed yet

---

## Conclusion

The current architecture is deliberately simple, optimised for rapid iteration and validation. It's built on a solid foundation that supports:

- **Fast development** (no build tools, minimal dependencies)
- **Easy deployment** (SSH + Git)
- **Low cost** (<$50/month hosting)
- **High performance** (sub-second page loads)

As we prove product-market fit, we'll migrate to a more scalable, API-first architecture with proper microservices, databases, and AI infrastructure.

**The key principle: Build for today, architect for tomorrow.**

---

*Last Updated: October 10, 2025*  
*Next Review: January 2026 (Phase 2 planning)*








