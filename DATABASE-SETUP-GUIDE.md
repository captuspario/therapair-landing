# How to Access and View the Therapair Database

## 🚨 **Current Status**

**The database is not yet created.** The schema and structure are documented, but you need to set it up first.

---

## 🛠️ **Step-by-Step Setup Guide**

### **Option 1: Using Hostinger's Built-in phpMyAdmin (Easiest)**

#### **Step 1: Access Hostinger Control Panel**
1. Go to [Hostinger Control Panel](https://hpanel.hostinger.com/)
2. Login with your credentials
3. Navigate to **Databases** → **MySQL Databases**

#### **Step 2: Create Database**
1. Click **"Create New Database"**
2. Database Name: `therapair_db`
3. Create a database user:
   - Username: `therapair_user`
   - Password: (strong password - save this!)
4. Grant **ALL PRIVILEGES** to the user

#### **Step 3: Access phpMyAdmin**
1. In Hostinger Control Panel, find your database
2. Click **"Manage"** or **"phpMyAdmin"**
3. Login with your database credentials
4. You'll see a web interface to view/edit your database

#### **Step 4: Create Tables**
1. In phpMyAdmin, click **SQL** tab
2. Paste the SQL schema (see below)
3. Click **"Go"** to execute

---

### **Option 2: Using Prisma (Recommended for Development)**

#### **Step 1: Install Prisma**
```bash
cd /Users/tino/Projects/therapair-landing-page
npm install prisma @prisma/client
npx prisma init
```

#### **Step 2: Configure Prisma**
Create/Edit `.env` file:
```env
DATABASE_URL="mysql://therapair_user:your_password@localhost:3306/therapair_db"
```

#### **Step 3: Create Prisma Schema**
Edit `prisma/schema.prisma`:
```prisma
generator client {
  provider = "prisma-client-js"
}

datasource db {
  provider = "mysql"
  url      = env("DATABASE_URL")
}

model Therapist {
  id                    String   @id @default(uuid())
  fullName              String   @map("full_name")
  contactEmail          String   @unique @map("contact_email")
  profession            String?
  suburb                String?
  region                String?
  modalities            String?  @db.Text
  tags                  String?  @db.Text
  accessibilityFeatures String?  @map("accessibility_features") @db.Text
  imageUrl              String?  @map("image_url")
  bio                   String?  @db.Text
  consentToList         Boolean  @default(false) @map("consent_to_list")
  wantsUpdates          Boolean  @default(false) @map("wants_updates")
  status                String   @default("pending")
  createdAt             DateTime @default(now()) @map("created_at")
  updatedAt             DateTime @updatedAt @map("updated_at")

  edits           TherapistEdit[]
  tokens          Token[]
  emailLogs       EmailLog[]
  submissionLogs  SubmissionLog[]

  @@map("therapists")
}

model TherapistEdit {
  id               String    @id @default(uuid())
  therapistId      String    @map("therapist_id")
  submittedFields  String?   @map("submitted_fields") @db.Text
  consentFlags     String?   @map("consent_flags") @db.Text
  imagePath        String?   @map("image_path")
  status           String    @default("pending")
  submittedAt      DateTime  @default(now()) @map("submitted_at")
  reviewedAt       DateTime? @map("reviewed_at")
  reviewedBy       String?   @map("reviewed_by")
  adminNotes       String?   @map("admin_notes") @db.Text

  therapist Therapist @relation(fields: [therapistId], references: [id], onDelete: Cascade)

  @@map("therapist_edits")
}

model Token {
  id          String    @id @default(uuid())
  therapistId String    @map("therapist_id")
  token       String    @unique
  expiresAt   DateTime  @map("expires_at")
  usedAt      DateTime? @map("used_at")
  usedIp      String?   @map("used_ip")
  createdAt   DateTime  @default(now()) @map("created_at")

  therapist Therapist @relation(fields: [therapistId], references: [id], onDelete: Cascade)

  @@map("tokens")
}

model EmailLog {
  id           String    @id @default(uuid())
  therapistId  String    @map("therapist_id")
  type         String
  sentAt       DateTime  @default(now()) @map("sent_at")
  opened       Boolean   @default(false)
  clicked      Boolean   @default(false)
  emailAddress String?   @map("email_address")

  therapist Therapist @relation(fields: [therapistId], references: [id], onDelete: Cascade)

  @@map("email_logs")
}

model SubmissionLog {
  id          String   @id @default(uuid())
  therapistId String   @map("therapist_id")
  ipAddress   String?  @map("ip_address")
  userAgent   String?  @map("user_agent") @db.Text
  tokenUsed   String?  @map("token_used")
  action      String
  timestamp   DateTime @default(now())

  therapist Therapist @relation(fields: [therapistId], references: [id], onDelete: Cascade)

  @@map("submissions_log")
}
```

#### **Step 4: Create Database Tables**
```bash
npx prisma db push
```

#### **Step 5: Use Prisma Studio to View Database**
```bash
npx prisma studio
```
This opens a web UI at `http://localhost:5555` where you can:
- View all tables
- Browse records
- Add/edit/delete data
- Run queries

---

### **Option 3: Using MySQL Workbench (Desktop App)**

#### **Step 1: Install MySQL Workbench**
- Download from [MySQL Workbench](https://dev.mysql.com/downloads/workbench/)
- Install on your Mac

#### **Step 2: Connect to Database**
1. Open MySQL Workbench
2. Click **"+"** to create new connection
3. Enter connection details:
   - **Connection Name**: Therapair DB
   - **Hostname**: Your Hostinger database host
   - **Port**: 3306
   - **Username**: therapair_user
   - **Password**: (your password)
4. Click **"Test Connection"**
5. Click **"OK"**

#### **Step 3: Browse Database**
- Double-click your connection
- Navigate through schemas and tables
- Run SQL queries in the query tab
- View data in table view

---

## 📊 **SQL Schema to Create Tables**

```sql
-- Create therapists table
CREATE TABLE therapists (
    id VARCHAR(36) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255) UNIQUE NOT NULL,
    profession VARCHAR(100),
    suburb VARCHAR(100),
    region VARCHAR(100),
    modalities TEXT,
    tags TEXT,
    accessibility_features TEXT,
    image_url VARCHAR(500),
    bio TEXT,
    consent_to_list BOOLEAN DEFAULT FALSE,
    wants_updates BOOLEAN DEFAULT FALSE,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (contact_email),
    INDEX idx_status (status)
);

-- Create therapist_edits table
CREATE TABLE therapist_edits (
    id VARCHAR(36) PRIMARY KEY,
    therapist_id VARCHAR(36) NOT NULL,
    submitted_fields TEXT,
    consent_flags TEXT,
    image_path VARCHAR(500),
    status VARCHAR(20) DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by VARCHAR(100),
    admin_notes TEXT,
    FOREIGN KEY (therapist_id) REFERENCES therapists(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_therapist (therapist_id)
);

-- Create tokens table
CREATE TABLE tokens (
    id VARCHAR(36) PRIMARY KEY,
    therapist_id VARCHAR(36) NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL,
    used_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (therapist_id) REFERENCES therapists(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_therapist (therapist_id)
);

-- Create email_logs table
CREATE TABLE email_logs (
    id VARCHAR(36) PRIMARY KEY,
    therapist_id VARCHAR(36) NOT NULL,
    type VARCHAR(50),
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    opened BOOLEAN DEFAULT FALSE,
    clicked BOOLEAN DEFAULT FALSE,
    email_address VARCHAR(255),
    FOREIGN KEY (therapist_id) REFERENCES therapists(id) ON DELETE CASCADE,
    INDEX idx_therapist (therapist_id)
);

-- Create submissions_log table
CREATE TABLE submissions_log (
    id VARCHAR(36) PRIMARY KEY,
    therapist_id VARCHAR(36) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    token_used VARCHAR(255),
    action VARCHAR(100),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (therapist_id) REFERENCES therapists(id) ON DELETE CASCADE,
    INDEX idx_therapist (therapist_id),
    INDEX idx_timestamp (timestamp)
);
```

---

## 🔍 **How to View Entries**

### **Method 1: phpMyAdmin (Web Interface)**
1. Login to phpMyAdmin
2. Select `therapair_db` database
3. Click on table name (e.g., `therapists`)
4. Click **"Browse"** to view all records
5. Use **"Search"** to find specific entries
6. Click **"SQL"** to run custom queries

### **Method 2: Prisma Studio (Best for Development)**
```bash
cd /Users/tino/Projects/therapair-landing-page
npx prisma studio
```
- Opens web UI at http://localhost:5555
- Click on any table to view records
- Filter, sort, and search easily
- Edit records inline

### **Method 3: Command Line**
```bash
# Connect to MySQL
mysql -h your_host -u therapair_user -p

# Switch to database
USE therapair_db;

# View all therapists
SELECT * FROM therapists;

# View therapists with specific status
SELECT * FROM therapists WHERE status = 'published';

# View pending submissions
SELECT t.full_name, te.submitted_at, te.status 
FROM therapists t 
JOIN therapist_edits te ON t.id = te.therapist_id 
WHERE te.status = 'pending';

# Count entries
SELECT COUNT(*) FROM therapists;

# View recent submissions
SELECT * FROM submissions_log ORDER BY timestamp DESC LIMIT 10;
```

---

## 📋 **Useful SQL Queries**

### **View All Therapists**
```sql
SELECT id, full_name, contact_email, profession, status, created_at
FROM therapists
ORDER BY created_at DESC;
```

### **Find Specific Therapist**
```sql
SELECT * FROM therapists 
WHERE contact_email = 'your.email@example.com';
```

### **View Pending Approvals**
```sql
SELECT 
    t.full_name,
    t.contact_email,
    te.submitted_at,
    te.status,
    te.admin_notes
FROM therapist_edits te
JOIN therapists t ON te.therapist_id = t.id
WHERE te.status = 'pending'
ORDER BY te.submitted_at ASC;
```

### **View Published Therapists**
```sql
SELECT full_name, profession, suburb, bio, image_url
FROM therapists
WHERE status = 'published' 
AND consent_to_list = TRUE
ORDER BY updated_at DESC;
```

### **Email Activity**
```sql
SELECT 
    t.full_name,
    el.type,
    el.sent_at,
    el.opened,
    el.clicked
FROM email_logs el
JOIN therapists t ON el.therapist_id = t.id
ORDER BY el.sent_at DESC;
```

### **Token Usage**
```sql
SELECT 
    t.full_name,
    tk.token,
    tk.expires_at,
    tk.used_at,
    tk.used_ip
FROM tokens tk
JOIN therapists t ON tk.therapist_id = t.id
WHERE tk.used_at IS NOT NULL
ORDER BY tk.used_at DESC;
```

---

## 🚀 **Quick Start: View Current Form Submissions**

Your landing page already collects form submissions via PHP. To store these in a database:

1. **Create the database** using Option 1 above
2. **Modify `submit-form.php`** to save to database instead of just sending emails
3. **Create a simple admin page** to view submissions

### **Example: Save to Database in PHP**
```php
// Add to submit-form.php after email sending

// Database connection
$servername = "localhost";
$username = "therapair_user";
$password = "your_password";
$dbname = "therapair_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO form_submissions (audience, email, therapy_interests, submitted_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sss", $audience, $email, $therapyInterests);

// Execute
$stmt->execute();

$stmt->close();
$conn->close();
```

---

## 🎯 **Recommended Approach for Now**

Since you're just starting:

1. **Use Hostinger phpMyAdmin** (easiest to access)
2. **Create the database** using the SQL schema above
3. **Install Prisma** for local development: `npx prisma studio`
4. **Start with simple form submissions** table before full therapist onboarding

---

## 📞 **Need Help?**

If you need help setting this up:
1. I can guide you through phpMyAdmin setup
2. I can create the database migration scripts
3. I can build a simple admin dashboard to view entries
4. I can help integrate database saving into your forms

Let me know which method you prefer, and I'll help you get it set up!
