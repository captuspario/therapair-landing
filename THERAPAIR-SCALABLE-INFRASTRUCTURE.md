# Therapair Scalable Infrastructure

**Version**: 1.0  
**Last Updated**: 2025-10-10  
**Purpose**: Template system for scaling email and database across all Therapair projects

---

## 📧 Scalable Email System

### Architecture Overview

```
┌─────────────────────────────────────────┐
│  Any Therapair Project                  │
│  (Landing Page, Widget, Future Apps)    │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Shared Email Handler Module            │
│  - Route by project/type                │
│  - Load appropriate template             │
│  - AI personalization                    │
│  - Send via configured provider          │
└──────────────┬──────────────────────────┘
               │
               ├─→ Admin Notification
               ├─→ User Confirmation (AI)
               └─→ Preference Management
```

### Email Handler Template

**File**: `shared/email-handler.php` (to create)

```php
<?php
/**
 * Therapair Unified Email Handler
 * Handles emails for all Therapair projects
 */

class TherapairEmailHandler {
    private $config;
    private $openai;
    private $templates;
    
    public function __construct($config) {
        $this->config = $config;
        $this->openai = new OpenAIService($config['OPENAI_API_KEY']);
        $this->templates = new EmailTemplates();
    }
    
    /**
     * Send email based on type and project
     * 
     * @param string $type - 'admin_notification' | 'user_confirmation' | 'booking_confirmation'
     * @param string $project - 'landing_page' | 'widget_unison'
     * @param array $data - Form/booking data
     * @param array $options - Additional options (AI, template override, etc.)
     */
    public function send($type, $project, $data, $options = []) {
        // Route to appropriate handler
        switch ($type) {
            case 'admin_notification':
                return $this->sendAdminNotification($project, $data);
                
            case 'user_confirmation':
                return $this->sendUserConfirmation($project, $data, $options);
                
            case 'booking_confirmation':
                return $this->sendBookingConfirmation($project, $data, $options);
                
            default:
                throw new Exception("Unknown email type: {$type}");
        }
    }
    
    private function sendUserConfirmation($project, $data, $options) {
        // Use AI if enabled
        if ($options['use_ai'] ?? true) {
            $content = $this->openai->generateEmail(
                $this->getSystemPrompt($project, 'user_confirmation'),
                $this->buildContext($data, $project)
            );
        } else {
            $content = $this->templates->getUserConfirmation($project, $data);
        }
        
        $html = $this->templates->wrapInTemplate($content, $project);
        
        return $this->sendEmail(
            $data['email'],
            'Thank you for your interest in Therapair',
            $html
        );
    }
    
    private function getSystemPrompt($project, $emailType) {
        // Load from project-specific or shared prompt files
        $promptFile = __DIR__ . "/prompts/{$project}-{$emailType}.md";
        
        if (file_exists($promptFile)) {
            return file_get_contents($promptFile);
        }
        
        // Fallback to default
        return $this->getDefaultPrompt($emailType);
    }
}

// Usage example:
$handler = new TherapairEmailHandler($config);

$handler->send('user_confirmation', 'landing_page', [
    'email' => 'user@example.com',
    'audience_type' => 'individual',
    'therapy_interests' => 'LGBTQ+ affirming care',
], ['use_ai' => true]);
```

---

## 🗄️ Scalable Database System

### Unified Schema Design

#### **Tables** (When migrating from Notion to PostgreSQL)

```sql
-- Users table (deduplicated across all sources)
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    
    -- Consent & Preferences
    unsubscribed BOOLEAN DEFAULT FALSE,
    email_preferences JSONB DEFAULT '[]',
    
    -- Source tracking
    first_source VARCHAR(50),  -- 'landing_page' | 'widget_unison'
    all_sources VARCHAR[] DEFAULT ARRAY[]::VARCHAR[],
    
    -- Metadata
    total_submissions INTEGER DEFAULT 1,
    last_submission_at TIMESTAMP,
    
    -- Indexes
    INDEX idx_email (email),
    INDEX idx_unsubscribed (unsubscribed)
);

-- Submissions table (all form entries)
CREATE TABLE submissions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    
    -- Source & Type
    source VARCHAR(50) NOT NULL,  -- 'landing_page' | 'widget_unison'
    entry_type VARCHAR(50) NOT NULL,  -- 'interest' | 'booking'
    audience_type VARCHAR(50),  -- 'individual' | 'therapist' | 'organisation' | 'supporter'
    
    -- Timestamps
    submitted_at TIMESTAMP DEFAULT NOW(),
    
    -- Data (JSONB for flexibility)
    form_data JSONB NOT NULL,
    
    -- Status
    status VARCHAR(50) DEFAULT 'new',  -- 'new' | 'contacted' | 'engaged' | 'converted'
    
    -- Metadata
    admin_notified BOOLEAN DEFAULT FALSE,
    user_confirmed BOOLEAN DEFAULT FALSE,
    synced_to_notion BOOLEAN DEFAULT FALSE,
    
    -- Indexes
    INDEX idx_user (user_id),
    INDEX idx_source (source),
    INDEX idx_type (entry_type),
    INDEX idx_submitted (submitted_at),
    INDEX idx_status (status)
);

-- Bookings table (widget-specific)
CREATE TABLE bookings (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    submission_id UUID REFERENCES submissions(id),
    user_id UUID REFERENCES users(id),
    
    -- Booking details
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    selected_therapist VARCHAR(100),
    
    -- Preferences (from quiz)
    preferences JSONB,
    
    -- Status
    booking_status VARCHAR(50) DEFAULT 'pending',  -- 'pending' | 'confirmed' | 'completed' | 'cancelled'
    session_date TIMESTAMP,
    session_notes TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    
    -- Indexes
    INDEX idx_therapist (selected_therapist),
    INDEX idx_status (booking_status),
    INDEX idx_session_date (session_date)
);

-- Email logs table (track all sent emails)
CREATE TABLE email_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID REFERENCES users(id),
    
    -- Email details
    email_type VARCHAR(50),  -- 'admin_notification' | 'user_confirmation' | etc.
    recipient VARCHAR(255),
    subject TEXT,
    
    -- Metadata
    sent_at TIMESTAMP DEFAULT NOW(),
    provider VARCHAR(50),  -- 'php_mail' | 'sendgrid' | 'postmark'
    status VARCHAR(50),  -- 'sent' | 'failed' | 'bounced'
    error_message TEXT,
    
    -- Tracking
    opened_at TIMESTAMP,
    clicked_at TIMESTAMP,
    
    -- Indexes
    INDEX idx_user (user_id),
    INDEX idx_type (email_type),
    INDEX idx_sent (sent_at)
);
```

### JSONB Data Structure

**Landing Page Submission**:
```json
{
  "therapy_interests": ["LGBTQ+ affirming care", "Trauma-informed care"],
  "additional_thoughts": "Looking for someone who understands...",
  "professional_title": "Clinical Psychologist",
  "organisation": "Private Practice",
  "specialisations": "Trauma therapy, LGBTQ+ care",
  ...
}
```

**Widget Booking**:
```json
{
  "user_responses": {
    "gender": "female",
    "age": "similar",
    "session_type": "online",
    "concerns": ["anxiety", "trauma"],
    "approach": "person-centred",
    "urgency": "fortnight"
  },
  "match_score": 8,
  "preferences_summary": "Female therapist, online sessions, trauma-focused..."
}
```

---

## 🔄 Migration Strategy

### From Notion to PostgreSQL

**Phase 1: Dual Write**
```
Form Submit → PostgreSQL (new) + Notion (existing)
Read from: Notion (team still uses it)
```

**Phase 2: Dual Write + Dual Read**
```
Form Submit → PostgreSQL + Notion
Read from: PostgreSQL (API) OR Notion (team)
```

**Phase 3: PostgreSQL Primary**
```
Form Submit → PostgreSQL → Sync to Notion (read-only)
Read from: PostgreSQL (API)
Notion: View-only for team
```

**Phase 4: PostgreSQL Only**
```
Form Submit → PostgreSQL
Read from: PostgreSQL
Notion: Deprecated or archived
```

### Deduplication Logic

```sql
-- When new submission comes in:
1. Check if email exists in users table
2. If exists:
   - Link submission to existing user
   - Update user.last_submission_at
   - Add source to user.all_sources if new
   - Increment user.total_submissions
3. If new:
   - Create new user record
   - Set user.first_source
   - Add to user.all_sources
   - Link submission
```

---

## 📊 Analytics Schema

### Event Tracking Table

```sql
CREATE TABLE analytics_events (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    
    -- Event details
    event_name VARCHAR(100) NOT NULL,  -- 'form_submit' | 'quiz_complete' | 'booking_submit'
    event_category VARCHAR(50),  -- 'conversion' | 'engagement' | 'navigation'
    
    -- Context
    project_source VARCHAR(50),  -- 'landing_page' | 'widget_unison'
    user_id UUID REFERENCES users(id),
    session_id VARCHAR(100),
    
    -- Event data
    properties JSONB,
    
    -- Timestamps
    occurred_at TIMESTAMP DEFAULT NOW(),
    
    -- User agent
    user_agent TEXT,
    ip_address INET,
    
    -- Indexes
    INDEX idx_event_name (event_name),
    INDEX idx_project (project_source),
    INDEX idx_occurred (occurred_at)
);
```

### Example Event

```json
{
  "event_name": "form_submit",
  "event_category": "conversion",
  "project_source": "landing_page",
  "properties": {
    "audience_type": "individual",
    "therapy_interests": ["lgbtq", "trauma"],
    "completion_time_seconds": 45
  }
}
```

---

## 🔗 API Design (Future)

### RESTful Endpoints

```
POST   /api/v1/submissions
GET    /api/v1/submissions/:id
PATCH  /api/v1/submissions/:id
DELETE /api/v1/submissions/:id

POST   /api/v1/bookings
GET    /api/v1/bookings/:id
PATCH  /api/v1/bookings/:id/confirm

GET    /api/v1/users/:email
PATCH  /api/v1/users/:email/preferences
POST   /api/v1/users/:email/unsubscribe

POST   /api/v1/emails/send
GET    /api/v1/emails/logs
```

### Example API Call

```javascript
// Submit landing page form
fetch('/api/v1/submissions', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    source: 'landing_page',
    entry_type: 'interest',
    audience_type: 'individual',
    form_data: {
      email: 'user@example.com',
      therapy_interests: ['lgbtq', 'trauma'],
      additional_thoughts: '...'
    }
  })
});

// Response
{
  "success": true,
  "submission_id": "uuid",
  "user_id": "uuid",
  "emails_sent": true,
  "notion_synced": true
}
```

---

## 📦 Shared Module Structure

### Proposed Directory Structure

```
therapair/
├── shared/
│   ├── email/
│   │   ├── email-handler.php
│   │   ├── templates/
│   │   │   ├── admin-notification.html
│   │   │   ├── user-confirmation.html
│   │   │   ├── booking-confirmation.html
│   │   │   └── preference-update.html
│   │   └── prompts/
│   │       ├── landing-page-confirmation.md
│   │       ├── widget-booking-confirmation.md
│   │       └── shared-prompt-guidelines.md
│   │
│   ├── database/
│   │   ├── notion-client.php
│   │   ├── notion-schemas.json
│   │   └── database-abstraction.php
│   │
│   ├── styles/
│   │   ├── therapair-design-system.css
│   │   ├── variables.css
│   │   └── components.css
│   │
│   └── utils/
│       ├── validation.php
│       ├── sanitization.php
│       └── formatting.php
│
├── therapair-landing-page/
│   ├── index.html
│   ├── submit-form.php (uses shared/email)
│   └── ...
│
└── therapair-widget-unison/
    ├── src/therapair-standalone.html
    ├── booking-handler.php (uses shared/email)
    └── ...
```

---

## 🗄️ Unified Notion Database Schema

### Complete Field List

```javascript
{
  // CORE FIELDS (All entries)
  "Name": "title",                    // Auto-generated or from form
  "Email": "email",                   // Required
  "Source": "select",                 // landing_page | widget_unison | future_app
  "Entry Type": "select",             // interest | booking | other
  "Audience Type": "select",          // individual | therapist | organisation | supporter
  "Submission Date": "date",          // Auto-timestamp
  "Status": "status",                 // New | Contacted | Engaged | Converted
  "Email Preferences": "multi_select", // Preference categories
  "Unsubscribed": "checkbox",         // Opt-out status
  "Last Contacted": "date",           // When last reached out
  "Notes": "rich_text",               // Internal notes
  
  // LANDING PAGE FIELDS
  "Therapy Interests": "multi_select",    // Individual only
  "Additional Thoughts": "rich_text",     // Individual only
  "Full Name": "text",                    // Therapist only
  "Professional Title": "text",           // Therapist only
  "Organisation": "text",                 // Therapist only
  "Specialisations": "rich_text",         // Therapist only
  "Contact Name": "text",                 // Organisation only
  "Position": "text",                     // Organisation only
  "Organisation Name": "text",            // Organisation only
  "Partnership Interest": "rich_text",    // Organisation only
  "Support Interest": "rich_text",        // Supporter only
  
  // WIDGET FIELDS
  "Phone": "phone_number",                // Widget bookings
  "Selected Therapist": "select",         // Emma | Nicki | Adam | etc.
  "Gender Preference": "select",          // Quiz response
  "Age Preference": "select",             // Quiz response
  "Session Type": "select",               // in-person | online | either
  "Primary Concerns": "multi_select",     // Quiz multi-select
  "Therapeutic Approach": "select",       // Quiz response
  "Urgency": "select",                    // ASAP | fortnight | few_weeks | flexible
  "Match Score": "number",                // Algorithm score (1-10)
  "Booking Status": "status",             // Pending | Confirmed | Completed | Cancelled
  "Session Date": "date",                 // Scheduled appointment
  "Session Notes": "rich_text",           // Post-session notes
  
  // COMPUTED/FORMULA FIELDS
  "Display Name": "formula",              // Smart name display
  "Days Since Submission": "formula",     // Auto-calculated
  "Email Preference Count": "formula",    // Number of preferences
  "Days Since Last Contact": "formula",   // Follow-up tracking
  
  // MANAGEMENT FIELDS
  "Interest Level": "select",             // High | Medium | Low
  "Launch Priority": "multi_select",      // Early Access | General Launch | Waitlist
  "Verification Status": "select",        // For therapists
  "Onboarding Stage": "select",           // For therapists
  "Partnership Type": "select",           // For organisations
  "Organisation Size": "select",          // For organisations
  "Support Type": "select",               // For supporters
  "Investment Level": "select",           // For investors
  "Engagement Level": "select"            // For supporters
}
```

### Notion Property Mapping

**PHP Array → Notion Properties**:

```php
function mapToNotionProperties($data, $source, $entryType, $audienceType) {
    $props = [];
    
    // CORE (always included)
    $props['Name'] = ['title' => [['text' => ['content' => generateName($data, $source, $entryType)]]]];
    $props['Email'] = ['email' => $data['email']];
    $props['Source'] = ['select' => ['name' => $source]];
    $props['Entry Type'] = ['select' => ['name' => $entryType]];
    $props['Audience Type'] = ['select' => ['name' => $audienceType]];
    $props['Submission Date'] = ['date' => ['start' => date('c')]];
    $props['Status'] = ['status' => ['name' => 'New']];
    
    // EMAIL PREFERENCES (default based on audience)
    $props['Email Preferences'] = [
        'multi_select' => getDefaultPreferences($audienceType, $source)
    ];
    
    // SOURCE-SPECIFIC FIELDS
    if ($source === 'landing_page') {
        $props = array_merge($props, mapLandingPageFields($data, $audienceType));
    }
    
    if ($source === 'widget_unison' && $entryType === 'booking') {
        $props = array_merge($props, mapWidgetBookingFields($data));
    }
    
    return $props;
}
```

---

## 📧 Email Template System

### Template Hierarchy

```
shared/email/templates/
├── base/
│   ├── header.html          (Logo, branding)
│   ├── footer.html          (Contact, preferences link)
│   └── styles.css           (Inline styles for emails)
│
├── landing-page/
│   ├── admin-notification.html
│   ├── user-confirmation.html
│   └── preference-update.html
│
├── widget/
│   ├── admin-booking-notification.html
│   ├── user-booking-confirmation.html
│   └── booking-reminder.html
│
└── shared/
    ├── welcome-series/      (Future nurture emails)
    ├── newsletter/          (Future announcements)
    └── transactional/       (Password reset, etc.)
```

### Template Variables System

**Shared Variables** (all templates):
```handlebars
{{firstName}}
{{email}}
{{submissionDate}}
{{projectName}}          // "Therapair" | "Unison Counselling"
{{preferencesUrl}}
{{contactEmail}}
{{websiteUrl}}
```

**Landing Page Variables**:
```handlebars
{{audienceType}}
{{therapyInterests}}
{{additionalThoughts}}
{{professionalTitle}}
{{organisationName}}
```

**Widget Variables**:
```handlebars
{{selectedTherapist}}
{{therapistBio}}
{{bookingDate}}
{{primaryConcerns}}
{{sessionType}}
{{matchScore}}
```

### Template Example

```html
<!-- shared/email/templates/base/header.html -->
<div style="background: linear-gradient(135deg, #9B74B7, #4F064F); padding: 2rem; text-align: center;">
    <h1 style="color: white; margin: 0; font-size: 28px;">
        {{projectName}}
    </h1>
</div>

<!-- Landing page user confirmation -->
<div class="content" style="padding: 2rem; background: white;">
    <p><strong>Hi there,</strong></p>
    
    <p>{{aiGeneratedContent}}</p>
    
    <div class="box" style="background: #f8f4ff; padding: 1.5rem; border-radius: 12px; margin: 1.5rem 0;">
        <h3 style="color: #4F064F; margin: 0 0 1rem 0;">📋 What happens next?</h3>
        <p>✓ You're one of the very first people to explore this with us</p>
        <p>✓ We'll be in touch soon as we build our service</p>
        <p>✓ We'll keep you updated on our progress</p>
    </div>
</div>

<!-- shared/email/templates/base/footer.html -->
<div style="padding: 2rem; color: #64748b; font-size: 0.875rem; text-align: center;">
    <p>Warm regards,<br>{{projectName}} Team</p>
    <p style="margin-top: 1rem;">
        📧 <a href="mailto:{{contactEmail}}">{{contactEmail}}</a><br>
        🌐 <a href="{{websiteUrl}}">{{websiteUrl}}</a><br>
        ⚙️ <a href="{{preferencesUrl}}">Manage Email Preferences</a>
    </p>
</div>
```

---

## 🚀 Implementation Roadmap

### Immediate (Current MVP)

**Landing Page**:
- ✅ PHP + Notion integration working
- ✅ AI emails implemented
- ✅ Preference management built

**Widget**:
- ✅ FormSubmit.co emails working
- ❌ No database integration
- ❌ No AI emails
- ❌ No preference management

### Phase 1: Widget Upgrade (Next 2 weeks)

**Tasks**:
1. [ ] Create `widget-booking-handler.php` (copy from submit-form.php)
2. [ ] Add Notion sync for widget bookings
3. [ ] Add AI email personalization for bookings
4. [ ] Add email preference management link
5. [ ] Test complete flow
6. [ ] Deploy to Unison

**Benefits**:
- Widget bookings stored in Notion
- Better user experience (personalized emails)
- Unified database across projects
- Email preference management

### Phase 2: Shared Modules (1 month)

**Tasks**:
1. [ ] Create `shared/` directory
2. [ ] Extract email handler to shared module
3. [ ] Extract Notion client to shared module
4. [ ] Create shared email templates
5. [ ] Refactor both projects to use shared code
6. [ ] Test thoroughly
7. [ ] Deploy both projects

**Benefits**:
- DRY (Don't Repeat Yourself)
- Easier maintenance
- Consistent behavior
- Single source of truth

### Phase 3: Database Migration (3 months)

**Tasks**:
1. [ ] Set up PostgreSQL database
2. [ ] Create schema (see SQL above)
3. [ ] Build API layer
4. [ ] Implement dual-write (PostgreSQL + Notion)
5. [ ] Migrate existing Notion data
6. [ ] Test thoroughly
7. [ ] Switch to PostgreSQL primary
8. [ ] Keep Notion as read-only view

**Benefits**:
- Scalable to 10,000+ users
- Better query performance
- Data integrity guarantees
- API-ready for future features

---

## 📝 Code Templates

### Widget Booking Handler Template

**File**: `therapair-widget-unison/booking-handler.php` (to create)

```php
<?php
require_once __DIR__ . '/../shared/email/email-handler.php';
require_once __DIR__ . '/../shared/database/notion-client.php';
require_once __DIR__ . '/config.php';

// Get booking data
$bookingData = [
    'full_name' => sanitize($_POST['Full_Name']),
    'email' => sanitize($_POST['Email']),
    'phone' => sanitize($_POST['Phone']),
    'selected_therapist' => sanitize($_POST['Selected_Therapist']),
    'preferences_summary' => sanitize($_POST['Preferences_Summary']),
];

// Initialize services
$emailHandler = new TherapairEmailHandler($config);
$notionClient = new NotionClient($config);

// 1. Send admin notification
$emailHandler->send('admin_notification', 'widget_unison', $bookingData, [
    'to' => 'tino@unisoncounselling.com',
    'subject' => "📅 New Booking: {$bookingData['selected_therapist']}"
]);

// 2. Send user confirmation (AI-powered)
$emailHandler->send('booking_confirmation', 'widget_unison', $bookingData, [
    'use_ai' => true,
    'to' => $bookingData['email']
]);

// 3. Sync to Notion
$notionClient->createEntry('widget_unison', 'booking', $bookingData);

// 4. Redirect to success page
header('Location: /booking-success.html');
exit;
?>
```

---

## 🔧 Configuration Template

### Widget Config

**File**: `therapair-widget-unison/config.php` (to create)

```php
<?php
// Email Configuration
define('ADMIN_EMAIL', 'tino@unisoncounselling.com');
define('FROM_EMAIL', 'bookings@unisoncounselling.com');
define('FROM_NAME', 'Unison Counselling');

// OpenAI API (shared with landing page)
define('OPENAI_API_KEY', 'sk-proj-xxx');
define('USE_AI_PERSONALIZATION', true);
define('AI_MODEL', 'gpt-4o-mini');

// Notion API (shared database)
define('NOTION_TOKEN', 'ntn_xxx');
define('NOTION_DATABASE_ID', '2875c25944da80c0b14afbbdf2510bb0'); // Same as landing page
define('USE_NOTION_SYNC', true);

// Project Settings
define('PROJECT_SOURCE', 'widget_unison');
define('WEBSITE_URL', 'https://unisoncounselling.com');
define('SUCCESS_URL', '/booking-success.html');

// Shared Modules Path
define('SHARED_PATH', __DIR__ . '/../shared');
?>
```

---

## 📊 Success Metrics

### Widget-Specific KPIs

**Engagement**:
- Quiz start rate (% of page visitors who start)
- Quiz completion rate (% who finish)
- Average completion time
- Question abandonment (where users drop off)

**Matching**:
- Match distribution (which therapists matched most)
- User satisfaction with matches
- Override rate (users choose non-top match)

**Conversion**:
- Booking form submission rate
- Booking confirmation rate (from team)
- Session attendance rate
- Rebooking rate

**Data Quality**:
- Complete profiles (all questions answered)
- Email deliverability
- Phone number validation
- Booking no-show rate

---

## 🔄 Continuous Improvement

### A/B Testing Opportunities

1. **Quiz Length**: 6 questions vs. 4 questions
2. **Match Display**: Top 3 vs. all matching therapists
3. **CTA Copy**: "Book Now" vs. "Request Appointment"
4. **Order**: Questions in different sequence
5. **Therapist Cards**: Different layouts/info prominence

### Feedback Collection

**Add to booking confirmation email**:
```
How was your matching experience?
[⭐⭐⭐⭐⭐] Rate 1-5 stars

Link to brief survey (optional)
```

---

## Version History

### v1.0 (2025-10-10)
- Initial widget documentation
- Scalable infrastructure designed
- Integration with landing page system planned
- Future roadmap established

---

**Maintained By**: Therapair Development Team  
**Last Review**: 2025-10-10

---

**End of Documentation**

