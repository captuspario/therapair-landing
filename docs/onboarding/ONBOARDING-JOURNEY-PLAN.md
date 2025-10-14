# Therapair Onboarding Journey Plan

## 🎯 **Overview**

Based on the `therapair_onboarding_system.md` document, this plan outlines the complete onboarding journey for therapists to join the Therapair platform. The system will be built using Node.js, PostgreSQL with Prisma ORM, and deployed on Hostinger.

---

## 🏗️ **Technical Architecture**

### **Tech Stack**
- **Backend**: Node.js + Express or Next.js API Routes
- **Database**: PostgreSQL with Prisma ORM
- **Frontend**: HTML/JS or React (depending on routing preference)
- **Image Hosting**: Hostinger server `/public/uploads`
- **Email**: NodeMailer using SMTP credentials
- **Security**: Token-based authentication, IP logging, HTTPS endpoints

### **Database Schema**

#### **`therapists` Table**
```sql
- id (UUID)
- full_name
- contact_email
- profession
- suburb, region
- modalities, tags (array)
- accessibility_features (array)
- image_url
- bio
- consent_to_list (bool)
- wants_updates (bool)
- created_at / updated_at
```

#### **`therapist_edits` Table**
```sql
- id
- therapist_id (FK)
- submitted_fields (JSON)
- consent_flags
- image_path
- status (pending / approved / rejected)
- submitted_at / reviewed_at
- reviewed_by (admin ID)
```

#### **`tokens` Table**
```sql
- id
- therapist_id (FK)
- token (secure string)
- expires_at
- used_at / used_ip
```

#### **`email_logs` Table**
```sql
- id
- therapist_id (FK)
- type (invite, reminder, opt-out)
- sent_at
- opened (bool)
- clicked (bool)
```

#### **`submissions_log` Table**
```sql
- id
- therapist_id
- ip_address
- user_agent
- token_used
- action (submitted_form, updated_consent, uploaded_image)
- timestamp
```

---

## 🔄 **Complete Onboarding Journey**

### **Phase 1: Data Import & Token Generation**

1. **CSV Import Process**
   - Parse therapist CSV data into PostgreSQL
   - Normalize fields (arrays for modalities, tags, locations)
   - Store consent flags with default values
   - Generate unique tokens for each therapist

2. **Token Management**
   - Create secure, time-limited tokens (7-30 days expiry)
   - Store in `tokens` table with therapist association
   - Log token generation and usage

### **Phase 2: Email Invitation System**

3. **Personalized Email Invitations**
   - Use NodeMailer to send onboarding invitations
   - Include therapist name and purpose explanation
   - Provide secure onboarding link (`/onboarding/:token`)
   - Include unsubscribe/opt-out options
   - Log all email activities

4. **Email Templates**
   - Welcome message explaining Therapair's mission
   - Clear consent explanation (Join Demo, Keep Informed, Opt-out)
   - Professional, inclusive tone
   - Mobile-responsive design

### **Phase 3: Secure Onboarding Form**

5. **Token-Protected Access**
   - Validate token on `/onboarding/:token` route
   - Load therapist profile (read-only preview)
   - Render editable form with pre-filled data
   - Allow submission for admin approval

6. **Editable Form Fields**
   - **Bio/Public Description**: Rich text editor
   - **Tag-based Selections**: 
     - Identities supported (LGBTQ+, neurodivergent, etc.)
     - Modalities (CBT, DBT, EMDR, etc.)
     - Specializations (trauma, relationships, etc.)
   - **Image Upload**: JPG/PNG, min 500px, max 5MB
   - **Consent Flags**:
     - ✅ Join Demo (participate in current demo)
     - 🔁 Keep Me Informed (future updates)
     - ❌ Opt-out/Remove Me (permanent removal)
   - **Optional Survey**: Referral preferences, monetization interest

### **Phase 4: Admin Approval System**

7. **Submission Processing**
   - Save submissions to `therapist_edits` table
   - Admin dashboard for pending reviews
   - Approve/reject workflow with comments
   - Automatic status updates and notifications

8. **Admin Dashboard Features**
   - View pending submissions
   - Compare original vs. edited data
   - Bulk approval/rejection
   - Audit trail with timestamps and admin IDs

### **Phase 5: Profile Publishing**

9. **Approval Workflow**
   - If approved: Merge changes into `therapists` table
   - Update status to "published"
   - Generate therapist profile page
   - Send confirmation email

10. **Profile Page Generation**
    - Create individual therapist profile pages
    - Include all approved information
    - Optimize for SEO and accessibility
    - Mobile-responsive design

---

## 🛡️ **Security & Privacy Implementation**

### **Security Measures**
- **No Passwords**: Token-based authentication only
- **Input Validation**: Sanitize all form inputs
- **File Upload Security**: Limit formats, sizes, scan for malware
- **Rate Limiting**: Prevent spam submissions
- **IP Logging**: Track all access and submissions
- **HTTPS Only**: Secure all endpoints

### **Privacy Compliance**
- **Australian Privacy Act 1988**: Full compliance
- **Data Minimization**: Collect only necessary information
- **Consent Management**: Clear opt-in/opt-out mechanisms
- **Data Retention**: Automatic cleanup of expired tokens
- **Audit Trail**: Complete logging of all actions

---

## 📁 **File Structure**

```
/therapair-onboarding/
├── /src
│   ├── /routes
│   │   ├── /onboarding
│   │   │   └── [token].js
│   │   └── /admin
│   │       └── index.js
│   ├── /forms
│   │   ├── onboardingForm.html
│   │   └── adminDashboard.html
│   ├── /utils
│   │   ├── parseCSV.js
│   │   ├── imageUpload.js
│   │   └── tokenGenerator.js
│   └── /emails
│       ├── sendOnboardingInvite.js
│       └── /templates
│           ├── invitation.html
│           └── confirmation.html
├── /db
│   ├── schema.prisma
│   └── seed.js
├── /public
│   └── /uploads
└── .env
```

---

## 🚀 **Implementation Phases**

### **Phase 1: Foundation (Week 1-2)**
- [ ] Set up PostgreSQL database with Prisma
- [ ] Create database schema and migrations
- [ ] Implement CSV import functionality
- [ ] Generate secure tokens for each therapist

### **Phase 2: Email System (Week 2-3)**
- [ ] Set up NodeMailer with SMTP credentials
- [ ] Create email templates (invitation, confirmation)
- [ ] Implement email sending functionality
- [ ] Add email logging and tracking

### **Phase 3: Onboarding Form (Week 3-4)**
- [ ] Create token-protected onboarding route
- [ ] Build editable form with pre-filled data
- [ ] Implement image upload functionality
- [ ] Add form validation and security measures

### **Phase 4: Admin System (Week 4-5)**
- [ ] Build admin dashboard for submissions
- [ ] Implement approve/reject workflow
- [ ] Add audit logging and notifications
- [ ] Create bulk processing features

### **Phase 5: Profile Pages (Week 5-6)**
- [ ] Generate individual therapist profile pages
- [ ] Implement SEO optimization
- [ ] Add mobile responsiveness
- [ ] Create profile management system

---

## 📊 **Success Metrics**

### **Onboarding Metrics**
- **Email Open Rate**: Target 60%+
- **Click-through Rate**: Target 25%+
- **Form Completion Rate**: Target 70%+
- **Approval Rate**: Target 80%+

### **Technical Metrics**
- **Page Load Time**: < 2 seconds
- **Form Submission Success**: 99%+
- **Email Delivery Rate**: 98%+
- **Security Incidents**: 0

---

## 🔧 **Next Steps**

1. **Database Setup**: Create PostgreSQL database and Prisma schema
2. **CSV Import**: Implement therapist data import functionality
3. **Token System**: Build secure token generation and validation
4. **Email Templates**: Design and code email templates
5. **Onboarding Form**: Create the main onboarding interface
6. **Admin Dashboard**: Build approval and management system
7. **Testing**: Comprehensive testing of all components
8. **Deployment**: Deploy to Hostinger with proper security

---

## 💡 **Key Considerations**

- **User Experience**: Make the onboarding process as smooth as possible
- **Security First**: Implement all security measures from the start
- **Scalability**: Design for future growth and additional features
- **Compliance**: Ensure full Australian privacy law compliance
- **Automation**: Minimize manual work through smart automation
- **Feedback Loop**: Collect feedback to improve the process

This onboarding system will provide therapists with a professional, secure, and user-friendly way to join the Therapair platform while maintaining the highest standards of privacy and security.
