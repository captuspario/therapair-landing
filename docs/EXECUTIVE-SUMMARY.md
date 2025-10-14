# Therapair: Executive Summary
**Intelligent Therapy Matching Platform**

---

## Vision Statement

Therapair is building the future of mental health accessibility through AI-powered matching technology that connects individuals with therapists who truly understand their unique identity, values, and needs. We're creating a scalable, agentic system that will transform how people discover and access mental health care globally.

---

## Current State: MVP 1.0 (October 2025)

### What We've Built

**1. Core Matching Technology**
- Conversational AI-powered matching quiz (9 questions)
- Multi-factor matching algorithm considering:
  - Identity (LGBTQIA+, cultural background, neurodiversity)
  - Therapeutic approach preferences
  - Practical requirements (online/in-person, location)
  - Specialty needs (trauma, anxiety, depression, relationships)
- Real-time matching with therapist database

**2. Live Demo Partnership: Unison Mental Health**
- **URL:** https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/
- **Purpose:** Proof of concept, user testing, data collection
- **Status:** Fully operational with 8 therapists
- **Features:**
  - Widget-based integration (embeddable)
  - Booking request system
  - Email notifications (admin + user confirmations)
  - Mobile-responsive design

**3. Marketing & Lead Generation**
- **Landing Page:** https://therapair.com.au
- **Features:**
  - Interest capture for 4 audience segments:
    - Individuals seeking therapy
    - Mental health practitioners
    - Clinics & organisations
    - Supporters & investors
  - AI-powered personalised email confirmations
  - Notion database integration for CRM
  - Australian English localisation

### Technical Infrastructure

**Current Stack:**
- **Frontend:** Vanilla JavaScript, HTML5, CSS3 (deliberate choice for performance)
- **Backend:** PHP (Hostinger deployment)
- **AI:** OpenAI GPT-4 API (email personalisation)
- **Database:** Notion API (MVP CRM)
- **Email:** PHP mail() with HTML templates
- **Deployment:** Git-based SSH automation
- **Hosting:** 
  - Landing page: Hostinger
  - Widget: Integrated on partner sites

**Why These Choices:**
- Zero frameworks = maximum performance, minimal dependencies
- Easy deployment to standard web hosting
- Scalable foundation (can migrate components as needed)
- Rapid iteration and testing

---

## Market Position

### The Problem We Solve

**For Individuals:**
1. Finding the right therapist is overwhelming (200+ profiles, unclear fit)
2. Traditional directories don't account for identity, values, trauma-informed care
3. Trial-and-error approach is expensive, emotionally draining, time-consuming
4. No way to know if a therapist truly "gets" you before booking

**For Therapists:**
1. Marketing themselves is time-consuming and expensive
2. Attracting ideal clients who are a good fit
3. Managing inquiries from mismatched prospects
4. Standing out in crowded directories

**For Clinics:**
1. Manual intake and matching process
2. High admin overhead for booking coordination
3. Client retention issues due to poor initial matches
4. Difficulty showcasing team diversity and specialisations

### Our Solution

**Intelligent, identity-first matching** that considers:
- Cultural & linguistic background
- LGBTQIA+ affirmation requirements
- Neurodivergence understanding
- Trauma specialisation
- Therapeutic modality preferences
- Practical logistics

**Result:** Higher match quality → Better therapeutic outcomes → Improved retention → Lower costs

---

## Business Model (Evolution)

### Phase 1: Proof of Concept (Current)
- **Revenue:** $0 (building product, gathering data)
- **Focus:** User validation, product refinement, partnership development
- **Timeline:** Oct 2025 - Mar 2026

### Phase 2: Pilot Program (Q2 2026)
- **Revenue Model:** SaaS for clinics
  - $99-299/month per clinic (based on size)
  - White-label widget integration
  - Basic analytics dashboard
- **Target:** 5-10 pilot clinics
- **Focus:** Refine onboarding, prove ROI, gather testimonials

### Phase 3: Scale (Q3 2026+)
- **Multiple Revenue Streams:**
  - **Clinics:** $299-999/month (tiered by practice size)
  - **Individual Therapists:** $29/month (directory listing + matches)
  - **Enterprise:** Custom pricing for hospital systems, EAP providers
  - **API Access:** $499+/month for platforms integrating matching

### Phase 4: Platform Expansion (2027+)
- Premium features for clients (session booking, progress tracking)
- AI therapy preparation tools
- Outcome measurement & continuous matching optimisation
- International expansion
- Insurance integration

---

## Scalability Architecture

### Current: Monolithic MVP
```
┌─────────────────────────────┐
│   Landing Page (PHP)        │
│   - Form handling           │
│   - Email sending           │
│   - Notion sync             │
└─────────────────────────────┘
            +
┌─────────────────────────────┐
│   Widget (JavaScript)       │
│   - Matching logic          │
│   - Therapist display       │
│   - Booking form            │
└─────────────────────────────┘
```

### Phase 2: Modular Services
```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Frontend    │───▶│   API Layer  │───▶│   Database   │
│  (React?)    │    │  (Node.js/   │    │  (PostgreSQL)│
│              │    │   Python)    │    │              │
└──────────────┘    └──────────────┘    └──────────────┘
                           │
                    ┌──────┴─────────┐
                    │  AI Matching   │
                    │   Service      │
                    └────────────────┘
```

### Phase 3: Microservices
```
                    ┌─── API Gateway ───┐
                    │                   │
        ┌───────────┼───────────┬───────┼──────────┐
        │           │           │       │          │
   ┌────▼───┐  ┌───▼────┐  ┌──▼────┐ ┌▼──────┐ ┌─▼─────┐
   │ Match  │  │ User   │  │ Clinic│ │Payment│ │ Email │
   │Service │  │Service │  │Service│ │Service│ │Service│
   └────────┘  └────────┘  └───────┘ └───────┘ └───────┘
        │           │           │         │         │
        └───────────┴───────────┴─────────┴─────────┘
                            │
                     ┌──────▼──────┐
                     │  Event Bus  │
                     │  (Analytics)│
                     └─────────────┘
```

### Phase 4: AI-Native Platform
- **Agentic Workflows:**
  - Automated therapist profiling (extract specialities from bios)
  - Continuous match optimisation (learning from outcomes)
  - Intelligent scheduling (timezone coordination, availability prediction)
  - Proactive client support (pre-session prep, post-session check-ins)
  
- **Advanced Features:**
  - NLP-powered profile analysis
  - Sentiment analysis on client feedback
  - Predictive analytics for dropout risk
  - Automated insurance verification
  - Multi-language support with cultural adaptation

---

## Key Metrics (Current & Targets)

### Current Metrics (MVP)
- **Widget:** Live on 1 partner site (Unison Mental Health)
- **Landing Page:** Live interest capture form
- **Therapist Database:** 8 practitioners (Unison)
- **Email Confirmations:** 100% delivery rate (admin + user)
- **User Journey Completion:** Testing in progress
- **Notion Integration:** Active CRM with segmented audiences

### Q1 2026 Targets
- **Users:** 100+ completed matching flows
- **Partnerships:** 3-5 clinics expressing interest
- **Email List:** 50+ practitioners, 200+ individuals
- **Conversion Rate:** 15%+ quiz completion
- **Match Quality:** 80%+ satisfaction with recommendations

### 2026 End-of-Year Targets
- **Clinics:** 10 paying customers
- **Therapists in Network:** 100+
- **Monthly Matches:** 500+
- **Revenue:** $30K+ MRR
- **Match Success:** 75%+ book with recommended therapist

---

## Competitive Advantages

### 1. **Identity-First Approach**
- Unlike Psychology Today or BetterHelp, we lead with identity affirmation
- LGBTQIA+, neurodivergent, culturally diverse matching is core, not an afterthought

### 2. **AI-Powered Intelligence**
- Conversational quiz feels natural, not like filling out a form
- Multi-factor matching beyond simple filters
- Personalised email confirmations build trust immediately

### 3. **Embeddable Widget**
- Clinics don't lose their branding or client relationship
- Seamless integration into existing websites
- White-label ready for enterprise

### 4. **Scalable Foundation**
- Built for automation from day one
- Clean separation of concerns
- API-ready architecture

### 5. **Market Timing**
- Mental health demand at all-time high (post-pandemic)
- Gen Z & Millennials expect AI-powered personalisation
- Therapists overwhelmed with inquiries, need better filtering

---

## Risks & Mitigation

### Technical Risks
| Risk | Mitigation |
|------|------------|
| **Scalability bottlenecks** | Modular architecture allows incremental migration to cloud services |
| **AI hallucination in emails** | Template fallbacks, human review option, prompt engineering |
| **Data privacy/HIPAA** | Not handling PHI in MVP; HIPAA compliance roadmap for Phase 3 |

### Business Risks
| Risk | Mitigation |
|------|------------|
| **Therapist adoption** | Start with clinics (built-in therapist base), prove ROI first |
| **Client trust in AI matching** | Human therapists always review, AI augments not replaces |
| **Competition from incumbents** | Move fast, focus on underserved identity-first niche |

### Market Risks
| Risk | Mitigation |
|------|------------|
| **Economic downturn affects therapy spend** | Target EAP providers, insurance integration |
| **Regulatory changes** | Monitor telehealth regulations, adapt quickly |

---

## Investment & Funding Strategy

### Current: Bootstrap Phase
- **Investment:** $0 (founder-funded development)
- **Runway:** Operating on minimal hosting costs (<$50/month)
- **Focus:** Product validation, user testimonials, partnership LOIs

### Next: Pre-Seed (Q2 2026)
- **Target:** $150K-300K
- **Use of Funds:**
  - Full-time founder salary (6-12 months)
  - Developer contractor (frontend + backend migration)
  - Sales & marketing (clinic outreach)
  - Legal (terms, privacy, contracts)
- **Milestones:** 10 paying clinics, 100+ therapists, $30K MRR

### Future: Seed Round (2027)
- **Target:** $1M-2M
- **Use of Funds:**
  - Team expansion (3-5 hires: engineering, sales, ops)
  - Advanced AI development (matching algorithm v2.0)
  - US market expansion
  - Insurance integrations

---

## Roadmap: Product Evolution

### ✅ **Phase 0: MVP** (Oct 2025 - CURRENT)
**Goal:** Prove the matching concept works
- [x] Core matching algorithm
- [x] Unison partnership widget
- [x] Landing page with interest capture
- [x] Email automation
- [x] Notion CRM integration

### 🔄 **Phase 1: Validation** (Nov 2025 - Mar 2026)
**Goal:** Gather user data, refine matching, secure pilot clinics
- [ ] User testing with 50+ individuals
- [ ] Therapist feedback interviews (20+)
- [ ] Match quality tracking system
- [ ] 3-5 clinic partnership agreements
- [ ] Analytics dashboard (basic)

### 📋 **Phase 2: Pilot Program** (Apr - Sep 2026)
**Goal:** Prove ROI for clinics, establish pricing model
- [ ] White-label widget for 5-10 clinics
- [ ] Therapist onboarding system
- [ ] Admin dashboard for clinics
- [ ] Payment integration (Stripe)
- [ ] Advanced analytics (match success rates, booking conversions)
- [ ] Mobile app (React Native) - client-facing

### 🚀 **Phase 3: Scale** (Oct 2026 - Dec 2027)
**Goal:** 100+ clinics, 1000+ therapists, profitable
- [ ] Self-service onboarding for clinics
- [ ] Marketplace for individual therapists
- [ ] Advanced AI matching (learning from outcomes)
- [ ] Video integration (Zoom/Doxy.me)
- [ ] Insurance verification automation
- [ ] Multi-language support (Spanish, Mandarin)

### 🌍 **Phase 4: Platform** (2028+)
**Goal:** Become the global standard for therapy matching
- [ ] International expansion (UK, Canada, EU)
- [ ] Agentic workflows (automated scheduling, session prep, follow-ups)
- [ ] Outcome measurement & continuous optimisation
- [ ] B2B2C partnerships (EAP providers, universities, employers)
- [ ] Research partnerships (publish efficacy studies)

---

## Technology Roadmap

### Current Stack (MVP)
```
Frontend: Vanilla JS + HTML + CSS
Backend: PHP (Hostinger)
AI: OpenAI GPT-4 API
Database: Notion API (CRM)
Email: PHP mail()
Hosting: Shared hosting (Hostinger)
Deployment: SSH + Git
```

### Phase 2: Modular Migration
```
Frontend: React (TypeScript)
Backend: Node.js + Express OR Python FastAPI
AI: OpenAI + Custom matching logic
Database: PostgreSQL (Supabase or Railway)
Email: SendGrid or Postmark
Hosting: Vercel (frontend) + Railway (backend)
Auth: Clerk or Auth0
Payments: Stripe
```

### Phase 3: Microservices
```
Frontend: Next.js (React framework)
Backend: Multiple services
  - Matching Service (Python + ML)
  - User Service (Node.js)
  - Clinic Service (Node.js)
  - Payment Service (Node.js + Stripe)
  - Email Service (Go + SendGrid)
Database: PostgreSQL (primary) + Redis (cache)
Infrastructure: AWS or GCP
CI/CD: GitHub Actions
Monitoring: Sentry, DataDog
Analytics: Segment + Mixpanel
```

### Phase 4: AI-Native
```
AI Infrastructure:
  - Custom ML models (TensorFlow/PyTorch)
  - Vector database (Pinecone/Weaviate)
  - LLM orchestration (LangChain)
  - Continuous learning pipeline
  
Agentic System:
  - Autonomous profile enhancement
  - Proactive client engagement
  - Intelligent scheduling & routing
  - Outcome prediction & optimisation
```

---

## Team & Expertise Needed

### Current Team
- **Tino (Founder):** Product, design, full-stack development, mental health sector knowledge

### Phase 1 Needs (Contractors/Advisors)
- **UX/UI Designer:** Refine quiz flow, dashboard design
- **Mental Health Advisor:** Clinical psychologist to validate matching logic
- **Sales/BD Advisor:** Clinic outreach strategy

### Phase 2 Hires (Full-Time)
1. **Lead Engineer:** Backend + AI development
2. **Sales Lead:** Clinic partnerships, enterprise deals
3. **Customer Success:** Onboarding, support, retention

### Phase 3+ Team (10-15 people)
- Engineering (4-5): Frontend, backend, ML/AI, DevOps
- Sales & Marketing (3): Enterprise sales, marketing, content
- Operations (2): Customer success, operations manager
- Product (1): Product manager
- Clinical (1): Clinical advisor (part-time)

---

## Success Metrics & KPIs

### Product Metrics
- **Match Quality Score:** User rating of therapist recommendations
- **Quiz Completion Rate:** % who finish all 9 questions
- **Booking Conversion:** % who request booking after seeing matches
- **Time to Match:** Average seconds to show results
- **Filter Diversity:** % of users using identity/culture filters

### Business Metrics
- **MRR (Monthly Recurring Revenue)**
- **Customer Acquisition Cost (CAC)**
- **Lifetime Value (LTV)**
- **Churn Rate** (clinic cancellations)
- **Net Promoter Score (NPS)**

### Clinical Impact Metrics (Future)
- **First-Session No-Show Rate:** (lower = better match)
- **Therapeutic Alliance Score:** Early-session connection rating
- **Client Retention:** Sessions attended over 6 months
- **Outcome Improvement:** Standardised measure (PHQ-9, GAD-7)

---

## Why Now? Market Timing

### 1. **Mental Health Crisis**
- 1 in 5 Australians experience mental health conditions annually
- Post-pandemic surge in demand for services
- Therapists overwhelmed, waiting lists 3-6 months

### 2. **Technology Adoption**
- Gen Z & Millennials expect AI-powered recommendations (Netflix, Spotify model)
- Telehealth normalised, online therapy mainstream
- Therapists comfortable with digital tools

### 3. **Identity Awareness**
- Growing recognition that identity matters in therapy
- LGBTQIA+ affirmation, cultural competency, neurodivergent understanding increasingly sought
- Traditional directories don't adequately serve these needs

### 4. **Economic Shift**
- Mental health budgets increasing (government, corporate)
- Value-based care models favour better matching (outcomes over volume)
- SaaS adoption in healthcare accelerating

---

## Exit Strategy (5-10 Year Horizon)

### Potential Acquirers

**1. Major Telehealth Platforms**
- BetterHelp, Talkspace, Headspace Health
- **Why:** Add intelligent matching to their platform
- **Valuation Range:** $50M-200M

**2. Healthcare Technology Companies**
- Epic, Cerner, Athenahealth
- **Why:** EHR integration, patient intake optimisation
- **Valuation Range:** $100M-500M

**3. Insurance/Benefits Providers**
- Cigna, UnitedHealth, Anthem
- **Why:** Improve mental health utilisation, reduce costs
- **Valuation Range:** $200M-1B

**4. Tech Giants**
- Google Health, Amazon Care, Apple Health
- **Why:** Mental health + AI expertise
- **Valuation Range:** $500M-2B+

### Alternative: Independent Growth
- Build to profitability ($10M+ ARR)
- Remain independent, founder-controlled
- Possible IPO at $50M+ ARR (unlikely but possible)

---

## Ask: What We Need

### Immediate (Q4 2025)
1. **User Testers:** Individuals and therapists to trial the platform
2. **Clinic Partners:** 3-5 practices interested in pilot program
3. **Advisors:** Mental health professionals, SaaS entrepreneurs
4. **Feedback:** On UX, messaging, value proposition

### Q1-Q2 2026
1. **Pre-Seed Investment:** $150K-300K
2. **Strategic Partners:** EAP providers, university counselling centres
3. **Technical Contractors:** React developer, AI/ML engineer
4. **Design Support:** Brand evolution, dashboard UX

---

## Contact & Links

**Website:** https://therapair.com.au  
**Live Demo:** https://unisonmentalhealth.com/find-a-therapist-who-is-right-for-you/  
**Email:** tino@unisoncounselling.com

**GitHub:**
- Landing Page: `therapair-landing-page`
- Widget: `therapair-widget-unison`

---

## Appendix: Core Belief

**Therapy works best when there's a genuine connection between client and therapist.**

Current systems force clients to:
1. Read hundreds of profiles
2. Guess who might "get" them
3. Book consultations with mismatches
4. Waste time, money, and emotional energy

**Therapair makes finding the right therapist feel like magic, not a chore.**

We're building a system where:
- Identity is honoured, not overlooked
- Preferences are understood, not ignored  
- Matches are intelligent, not random
- The experience is human, not robotic

**This is just the beginning.**

---

*Last Updated: October 10, 2025*  
*Version: 1.0 - MVP Executive Summary*








