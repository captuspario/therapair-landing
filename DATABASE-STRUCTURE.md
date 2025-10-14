# Therapair Database Structure & Therapist Journey

## 🗄️ **Database Schema Overview**

### **Core Tables**

#### **1. `therapists` Table**
```sql
CREATE TABLE therapists (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    full_name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255) UNIQUE NOT NULL,
    profession VARCHAR(100),
    suburb VARCHAR(100),
    region VARCHAR(100),
    modalities TEXT[], -- Array: ['CBT', 'DBT', 'EMDR']
    tags TEXT[], -- Array: ['LGBTQ+', 'Neurodivergent', 'Trauma']
    accessibility_features TEXT[], -- Array: ['Wheelchair Access', 'Online Sessions']
    image_url VARCHAR(500),
    bio TEXT,
    consent_to_list BOOLEAN DEFAULT false,
    wants_updates BOOLEAN DEFAULT false,
    status VARCHAR(20) DEFAULT 'pending', -- pending, approved, published, rejected
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### **2. `therapist_edits` Table**
```sql
CREATE TABLE therapist_edits (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    therapist_id UUID REFERENCES therapists(id) ON DELETE CASCADE,
    submitted_fields JSONB, -- Store all form data as JSON
    consent_flags JSONB, -- Store consent choices
    image_path VARCHAR(500),
    status VARCHAR(20) DEFAULT 'pending', -- pending, approved, rejected
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP,
    reviewed_by VARCHAR(100), -- Admin username/ID
    admin_notes TEXT
);
```

#### **3. `tokens` Table**
```sql
CREATE TABLE tokens (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    therapist_id UUID REFERENCES therapists(id) ON DELETE CASCADE,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP,
    used_ip INET,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### **4. `email_logs` Table**
```sql
CREATE TABLE email_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    therapist_id UUID REFERENCES therapists(id) ON DELETE CASCADE,
    type VARCHAR(50), -- invite, reminder, opt-out, confirmation
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    opened BOOLEAN DEFAULT false,
    clicked BOOLEAN DEFAULT false,
    email_address VARCHAR(255)
);
```

#### **5. `submissions_log` Table**
```sql
CREATE TABLE submissions_log (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    therapist_id UUID REFERENCES therapists(id) ON DELETE CASCADE,
    ip_address INET,
    user_agent TEXT,
    token_used VARCHAR(255),
    action VARCHAR(100), -- submitted_form, updated_consent, uploaded_image
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🔄 **Complete Therapist Journey**

### **Step 1: Data Import & Token Generation**

```javascript
// Example: Import therapist from CSV
const therapist = {
    id: "550e8400-e29b-41d4-a716-446655440000",
    full_name: "Dr. Sarah Johnson",
    contact_email: "sarah.johnson@example.com",
    profession: "Clinical Psychologist",
    suburb: "Fitzroy",
    region: "Melbourne",
    modalities: ["CBT", "DBT", "EMDR"],
    tags: ["LGBTQ+", "Trauma", "Anxiety"],
    accessibility_features: ["Wheelchair Access", "Online Sessions"],
    consent_to_list: false, // Default - needs to be confirmed
    wants_updates: false,   // Default - needs to be confirmed
    status: "pending"
};

// Generate secure token
const token = {
    id: "660e8400-e29b-41d4-a716-446655440001",
    therapist_id: "550e8400-e29b-41d4-a716-446655440000",
    token: "secure_random_string_12345",
    expires_at: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000), // 7 days
    used_at: null,
    used_ip: null
};
```

### **Step 2: Email Invitation Sent**

```javascript
// Email sent to: sarah.johnson@example.com
const emailContent = {
    to: "sarah.johnson@example.com",
    subject: "Invitation to join Therapair - Smart therapy matching platform",
    body: `
        Hi Dr. Sarah Johnson,
        
        We're excited to invite you to join Therapair, Australia's new smart therapy matching platform.
        
        Your secure onboarding link: https://therapair.com.au/onboarding/secure_random_string_12345
        
        This link expires in 7 days and is unique to you.
        
        Best regards,
        The Therapair Team
    `
};

// Log email sent
const emailLog = {
    therapist_id: "550e8400-e29b-41d4-a716-446655440000",
    type: "invite",
    sent_at: new Date(),
    opened: false,
    clicked: false
};
```

### **Step 3: Therapist Clicks Link & Views Profile**

```javascript
// URL: https://therapair.com.au/onboarding/secure_random_string_12345
// System validates token and loads therapist data

const therapistProfile = {
    // Pre-filled data from CSV
    full_name: "Dr. Sarah Johnson",
    profession: "Clinical Psychologist",
    suburb: "Fitzroy",
    region: "Melbourne",
    modalities: ["CBT", "DBT", "EMDR"],
    tags: ["LGBTQ+", "Trauma", "Anxiety"],
    accessibility_features: ["Wheelchair Access", "Online Sessions"],
    
    // Empty fields to be filled
    bio: "",
    image_url: "",
    consent_to_list: false,
    wants_updates: false
};
```

### **Step 4: Therapist Fills Out Form**

```javascript
// Form submission data
const formData = {
    bio: "I specialize in working with LGBTQ+ individuals and trauma survivors. I believe in creating a safe, inclusive space for healing.",
    image_upload: "sarah_johnson_profile.jpg",
    consent_flags: {
        join_demo: true,
        keep_informed: true,
        opt_out: false
    },
    additional_survey: {
        referral_preferences: "Word of mouth and professional networks",
        monetization_interest: "Open to discussion"
    }
};

// Save to therapist_edits table
const editRecord = {
    therapist_id: "550e8400-e29b-41d4-a716-446655440000",
    submitted_fields: formData,
    consent_flags: formData.consent_flags,
    image_path: "/uploads/sarah_johnson_profile.jpg",
    status: "pending",
    submitted_at: new Date()
};
```

### **Step 5: Admin Reviews & Approves**

```javascript
// Admin dashboard shows pending submissions
const pendingSubmissions = [
    {
        therapist_name: "Dr. Sarah Johnson",
        submitted_at: "2025-01-15 14:30:00",
        changes: {
            bio: "Added comprehensive bio",
            image: "Uploaded professional photo",
            consent: "Agreed to join demo and receive updates"
        },
        status: "pending"
    }
];

// Admin approves
const approval = {
    status: "approved",
    reviewed_at: new Date(),
    reviewed_by: "admin_user",
    admin_notes: "Great profile, approved for publication"
};
```

### **Step 6: Profile Published**

```javascript
// Merge approved changes into main therapists table
const updatedTherapist = {
    id: "550e8400-e29b-41d4-a716-446655440000",
    full_name: "Dr. Sarah Johnson",
    bio: "I specialize in working with LGBTQ+ individuals and trauma survivors...",
    image_url: "/uploads/sarah_johnson_profile.jpg",
    consent_to_list: true,
    wants_updates: true,
    status: "published",
    updated_at: new Date()
};

// Generate public profile page
const profileUrl = "https://therapair.com.au/therapist/dr-sarah-johnson";
```

---

## 🔍 **How to View Your Entry as a Therapist**

### **Option 1: Through Onboarding Link**
1. **Receive Email**: Get invitation email with secure link
2. **Click Link**: `https://therapair.com.au/onboarding/your_unique_token`
3. **View Profile**: See your pre-filled data and edit form
4. **Submit Changes**: Fill out bio, upload photo, set consent preferences
5. **Wait for Approval**: Admin reviews and approves your submission
6. **Get Confirmation**: Receive email when profile is published

### **Option 2: Through Admin Dashboard** (Future)
1. **Login**: Access admin panel with credentials
2. **Search**: Find your profile by name or email
3. **View Status**: See approval status and any pending changes
4. **Edit**: Make additional changes if needed

### **Option 3: Through Public Profile** (After Approval)
1. **Search**: Use therapist search on main site
2. **Find Profile**: Locate your published profile
3. **View**: See how it appears to potential clients

---

## 📊 **Database Queries Examples**

### **Find Therapist by Email**
```sql
SELECT * FROM therapists 
WHERE contact_email = 'sarah.johnson@example.com';
```

### **Get Pending Submissions**
```sql
SELECT t.full_name, t.contact_email, te.submitted_at, te.status
FROM therapists t
JOIN therapist_edits te ON t.id = te.therapist_id
WHERE te.status = 'pending'
ORDER BY te.submitted_at DESC;
```

### **Check Token Validity**
```sql
SELECT t.*, tk.expires_at, tk.used_at
FROM therapists t
JOIN tokens tk ON t.id = tk.therapist_id
WHERE tk.token = 'secure_random_string_12345'
AND tk.expires_at > NOW()
AND tk.used_at IS NULL;
```

### **Get Published Therapists**
```sql
SELECT full_name, profession, suburb, modalities, tags, bio, image_url
FROM therapists
WHERE status = 'published'
AND consent_to_list = true
ORDER BY updated_at DESC;
```

---

## 🛡️ **Security Features**

### **Token Security**
- **Unique Tokens**: Each therapist gets a unique, random token
- **Time Limited**: Tokens expire after 7-30 days
- **Single Use**: Tokens can be marked as used
- **IP Logging**: Track where tokens are accessed from

### **Data Protection**
- **Input Validation**: All form data is sanitized
- **File Upload Security**: Images are scanned and validated
- **Audit Trail**: All actions are logged with timestamps
- **Privacy Compliance**: Follows Australian Privacy Act 1988

### **Access Control**
- **No Passwords**: Token-based authentication only
- **Admin Permissions**: Separate admin access for approvals
- **Rate Limiting**: Prevent spam and abuse
- **HTTPS Only**: All communications encrypted

---

## 🚀 **Implementation Status**

### **✅ Completed**
- Database schema design
- Token generation system
- Email invitation templates
- Form validation logic
- Admin approval workflow

### **🔄 In Progress**
- CSV import functionality
- Image upload handling
- Email automation system
- Admin dashboard interface

### **📋 Next Steps**
- Deploy database to production
- Set up email sending system
- Create admin dashboard
- Test complete workflow
- Launch with first batch of therapists

This system provides a secure, automated way for therapists to join the platform while maintaining full control over their data and consent preferences.
