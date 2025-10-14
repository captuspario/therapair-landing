# Therapair Therapist Journey Flow

## 🔄 **Complete Therapist Onboarding Journey**

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           THERAPIST ONBOARDING JOURNEY                          │
└─────────────────────────────────────────────────────────────────────────────────┘

📊 STEP 1: DATA IMPORT
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 1. CSV Import → Parse therapist data → Store in 'therapists' table             │
│ 2. Generate secure token → Store in 'tokens' table                             │
│ 3. Set default status: 'pending'                                               │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    ↓
📧 STEP 2: EMAIL INVITATION
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 1. Send personalized email with secure link                                     │
│ 2. Link: https://therapair.com.au/onboarding/{unique_token}                     │
│ 3. Log email in 'email_logs' table                                             │
│ 4. Token expires in 7 days                                                     │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    ↓
🔗 STEP 3: THERAPIST CLICKS LINK
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 1. Validate token (check expiry, not used)                                     │
│ 2. Load therapist data from 'therapists' table                                 │
│ 3. Display pre-filled form with editable fields                                │
│ 4. Log access in 'submissions_log' table                                       │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    ↓
📝 STEP 4: THERAPIST FILLS FORM
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 1. Edit bio, upload photo, set consent preferences                             │
│ 2. Submit form data                                                             │
│ 3. Save to 'therapist_edits' table with status 'pending'                       │
│ 4. Mark token as used                                                           │
│ 5. Send confirmation email                                                      │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    ↓
👨‍💼 STEP 5: ADMIN REVIEW
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 1. Admin receives notification of pending submission                           │
│ 2. Review therapist profile and changes                                        │
│ 3. Approve or reject with notes                                                │
│ 4. Update 'therapist_edits' status                                             │
└─────────────────────────────────────────────────────────────────────────────────┘
                                    ↓
✅ STEP 6: PROFILE PUBLISHED
┌─────────────────────────────────────────────────────────────────────────────────┐
│ 1. If approved: Merge changes into 'therapists' table                          │
│ 2. Update status to 'published'                                                │
│ 3. Generate public profile page                                                │
│ 4. Send confirmation email to therapist                                        │
│ 5. Profile now visible to potential clients                                    │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────┐
│                              END OF JOURNEY                                    │
└─────────────────────────────────────────────────────────────────────────────────┘
```

## 🔍 **How to View Your Entry as a Therapist**

### **Method 1: Through Onboarding Link (Primary)**
```
1. 📧 Receive invitation email
   ↓
2. 🔗 Click secure link: https://therapair.com.au/onboarding/{your_token}
   ↓
3. 👀 View your pre-filled profile data
   ↓
4. ✏️ Edit bio, upload photo, set preferences
   ↓
5. 📤 Submit for admin approval
   ↓
6. ⏳ Wait for approval (1-2 business days)
   ↓
7. ✅ Receive confirmation when published
```

### **Method 2: Through Public Search (After Approval)**
```
1. 🌐 Visit https://therapair.com.au
   ↓
2. 🔍 Use therapist search function
   ↓
3. 👤 Find your published profile
   ↓
4. 👀 See how it appears to potential clients
```

### **Method 3: Through Admin Dashboard (Future)**
```
1. 🔐 Login to admin panel
   ↓
2. 🔍 Search for your profile
   ↓
3. 📊 View approval status and history
   ↓
4. ✏️ Make additional changes if needed
```

## 📋 **Database Tables & Their Purpose**

### **`therapists` Table**
- **Purpose**: Main therapist data storage
- **Contains**: Name, email, profession, bio, image, consent status
- **Status**: pending → approved → published

### **`therapist_edits` Table**
- **Purpose**: Store form submissions before approval
- **Contains**: Submitted changes, consent flags, admin notes
- **Status**: pending → approved/rejected

### **`tokens` Table**
- **Purpose**: Secure access to onboarding form
- **Contains**: Unique token, expiry date, usage tracking
- **Security**: Time-limited, single-use, IP logged

### **`email_logs` Table**
- **Purpose**: Track email communications
- **Contains**: Sent emails, open rates, click tracking
- **Analytics**: Monitor engagement and delivery

### **`submissions_log` Table**
- **Purpose**: Audit trail of all actions
- **Contains**: IP addresses, user agents, timestamps
- **Security**: Complete activity logging

## 🛡️ **Security & Privacy Features**

### **Token Security**
- ✅ Unique random tokens for each therapist
- ✅ 7-day expiration period
- ✅ Single-use validation
- ✅ IP address logging
- ✅ No password required

### **Data Protection**
- ✅ Input validation and sanitization
- ✅ Secure file upload handling
- ✅ Encrypted data transmission (HTTPS)
- ✅ Australian Privacy Act 1988 compliance
- ✅ Complete audit trail

### **Access Control**
- ✅ Token-based authentication only
- ✅ Admin approval required for publication
- ✅ Rate limiting to prevent abuse
- ✅ Secure admin dashboard access

## 📊 **Example Database Queries**

### **Find Your Profile**
```sql
SELECT * FROM therapists 
WHERE contact_email = 'your.email@example.com';
```

### **Check Your Submission Status**
```sql
SELECT te.status, te.submitted_at, te.admin_notes
FROM therapist_edits te
JOIN therapists t ON te.therapist_id = t.id
WHERE t.contact_email = 'your.email@example.com'
ORDER BY te.submitted_at DESC;
```

### **View All Published Therapists**
```sql
SELECT full_name, profession, suburb, bio, image_url
FROM therapists
WHERE status = 'published' AND consent_to_list = true
ORDER BY updated_at DESC;
```

## 🚀 **Current Implementation Status**

### **✅ Completed**
- Database schema design
- Token generation system
- Email templates
- Form validation logic
- Admin workflow design

### **🔄 In Progress**
- CSV import functionality
- Image upload system
- Email automation
- Admin dashboard

### **📋 Next Steps**
- Deploy to production
- Import first batch of therapists
- Send invitation emails
- Launch onboarding system

This system ensures a secure, user-friendly way for therapists to join the platform while maintaining complete control over their data and consent preferences.
