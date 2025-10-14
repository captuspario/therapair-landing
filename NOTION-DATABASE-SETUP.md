# Notion Database Setup for Therapair Form Submissions

## 🎯 **Why Notion for MVP?**

Notion is the **perfect choice** for your early stage because:
- ✅ No technical database setup required
- ✅ Beautiful visual interface to view all submissions
- ✅ Easy to share with team members
- ✅ Built-in filtering, sorting, and views
- ✅ Your PHP form already has Notion integration code
- ✅ Free tier is generous for early stage
- ✅ Can export to proper database later when you scale

---

## 🛠️ **Step-by-Step Setup (5 minutes)**

### **Step 1: Create Notion Integration**

1. Go to [Notion Integrations](https://www.notion.so/my-integrations)
2. Click **"+ New integration"**
3. Fill in details:
   - **Name**: Therapair Form Sync
   - **Associated workspace**: Select your workspace
   - **Type**: Internal integration
4. Click **"Submit"**
5. **Copy the "Internal Integration Token"** (starts with `secret_...`)
   - ⚠️ Save this - you'll need it in Step 3

---

### **Step 2: Create Notion Database**

1. Open Notion and create a new page: **"Therapair Form Submissions"**
2. Type `/database` and select **"Table - Inline"**
3. Create these columns:

| Column Name | Type | Description |
|------------|------|-------------|
| **Name** | Title | Full name (auto-filled) |
| **Email** | Email | Contact email |
| **Audience Type** | Select | Individual, Therapist, Organization, Other |
| **Status** | Select | New, Contacted, Converted, Archived |
| **Therapy Interests** | Multi-select | LGBTQ+, Neurodiversity, Trauma, etc. |
| **Additional Thoughts** | Text | Long-form responses |
| **Professional Title** | Text | For therapists |
| **Organization** | Text | For therapists/organizations |
| **Specializations** | Text | For therapists |
| **Partnership Interest** | Text | For organizations |
| **Support Interest** | Text | For supporters |
| **Submitted At** | Date | Timestamp |
| **IP Address** | Text | For security |

4. **Share the database with your integration:**
   - Click **"Share"** (top right)
   - Click **"Invite"**
   - Select **"Therapair Form Sync"** (your integration)
   - Click **"Invite"**

5. **Copy the Database ID:**
   - Your database URL looks like: `https://notion.so/username/DATABASE_ID?v=...`
   - Copy the `DATABASE_ID` part (32 characters, mix of letters/numbers)

---

### **Step 3: Update Your config.php**

Edit `/Users/tino/Projects/therapair-landing-page/config.php`:

```php
<?php
/**
 * Therapair Configuration
 * SECURITY: Keep this file secure and never commit sensitive data to git
 */

// Email Configuration
define('ADMIN_EMAIL', 'contact@therapair.com.au');
define('FROM_EMAIL', 'noreply@therapair.com.au');
define('FROM_NAME', 'Therapair');
define('WEBSITE_URL', 'https://therapair.com.au');

// OpenAI Configuration (for AI-powered emails)
define('OPENAI_API_KEY', 'YOUR_OPENAI_API_KEY_HERE'); // Optional
define('USE_AI_PERSONALIZATION', false); // Set to true if using AI
define('AI_MODEL', 'gpt-4o-mini');

// Notion Configuration
define('USE_NOTION_SYNC', true); // Set to TRUE
define('NOTION_API_KEY', 'secret_YOUR_NOTION_TOKEN_HERE'); // Paste your token
define('NOTION_DATABASE_ID', 'YOUR_DATABASE_ID_HERE'); // Paste your database ID

// Security
define('ALLOWED_ORIGINS', ['https://therapair.com.au', 'http://localhost']);
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 5); // Max submissions per hour per IP
?>
```

---

### **Step 4: Test the Integration**

1. Go to your website: https://therapair.com.au
2. Fill out the "Request Early Access" form
3. Submit the form
4. Check your Notion database - you should see a new entry!

---

## 📊 **Your Notion Database Will Look Like This**

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ Therapair Form Submissions                                                   │
├─────────────┬──────────────────────┬──────────────┬────────┬─────────────────┤
│ Name        │ Email                │ Audience     │ Status │ Submitted At    │
├─────────────┼──────────────────────┼──────────────┼────────┼─────────────────┤
│ Sarah J.    │ sarah@example.com    │ Individual   │ New    │ Jan 15, 2025    │
│ Dr. Chen    │ chen@clinic.com.au   │ Therapist    │ New    │ Jan 15, 2025    │
│ MindCare    │ info@mindcare.com.au │ Organization │ New    │ Jan 14, 2025    │
│ Alex W.     │ alex@example.com     │ Supporter    │ New    │ Jan 14, 2025    │
└─────────────┴──────────────────────┴──────────────┴────────┴─────────────────┘
```

---

## 🎨 **Create Useful Views**

In Notion, create these views for easy management:

### **1. By Status**
- Filter by Status = "New" to see who needs follow-up
- Group by Status to see your pipeline

### **2. By Audience Type**
- Tab 1: Individuals seeking therapy
- Tab 2: Therapists interested in joining
- Tab 3: Organizations for partnerships
- Tab 4: Supporters/Investors

### **3. By Date**
- Timeline view to see submission trends
- Calendar view for scheduled follow-ups

### **4. Quick Stats Dashboard**
Create a linked database with:
- Total submissions
- New this week
- By audience type
- Conversion rates

---

## 🔧 **Check if Notion Sync is Working**

### **Option 1: Check notion-sync.php exists**
```bash
ls -la /Users/tino/Projects/therapair-landing-page/notion-sync.php
```

### **Option 2: Test the integration**
Your `submit-form.php` already has this code:
```php
// Load Notion sync handler
if ($USE_NOTION_SYNC && file_exists(__DIR__ . '/notion-sync.php')) {
    require_once __DIR__ . '/notion-sync.php';
}

// ... later in the code ...

// SYNC TO NOTION DATABASE
if ($USE_NOTION_SYNC) {
    $notionResult = syncToNotion($formData, $audience);
    if (!$notionResult['success']) {
        error_log("Notion sync failed: " . print_r($notionResult, true));
        // Continue anyway - don't block user experience
    }
}
```

---

## 📋 **notion-sync.php Implementation**

If you don't have `notion-sync.php` yet, here's the complete implementation:

```php
<?php
/**
 * Notion Database Sync Handler
 * Syncs form submissions to Notion database
 */

function syncToNotion($formData, $audience) {
    // Check if Notion is enabled
    if (!defined('USE_NOTION_SYNC') || !USE_NOTION_SYNC) {
        return ['success' => false, 'error' => 'Notion sync not enabled'];
    }
    
    if (!defined('NOTION_API_KEY') || !defined('NOTION_DATABASE_ID')) {
        return ['success' => false, 'error' => 'Notion credentials not configured'];
    }
    
    $apiKey = NOTION_API_KEY;
    $databaseId = NOTION_DATABASE_ID;
    
    // Build Notion page properties based on audience type
    $properties = buildNotionProperties($formData, $audience);
    
    // Call Notion API
    $result = createNotionPage($apiKey, $databaseId, $properties);
    
    return $result;
}

function buildNotionProperties($data, $audience) {
    $properties = [
        'Name' => [
            'title' => [
                ['text' => ['content' => getDisplayName($data, $audience)]]
            ]
        ],
        'Email' => [
            'email' => $data['email']
        ],
        'Audience Type' => [
            'select' => ['name' => ucfirst($audience)]
        ],
        'Status' => [
            'select' => ['name' => 'New']
        ],
        'Submitted At' => [
            'date' => ['start' => date('c')]
        ]
    ];
    
    // Add audience-specific fields
    switch ($audience) {
        case 'individual':
            if (!empty($data['therapy_interests'])) {
                $properties['Therapy Interests'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['therapy_interests']]]
                    ]
                ];
            }
            if (!empty($data['additional_thoughts'])) {
                $properties['Additional Thoughts'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['additional_thoughts']]]
                    ]
                ];
            }
            break;
            
        case 'therapist':
            if (!empty($data['full_name'])) {
                $properties['Full Name'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['full_name']]]
                    ]
                ];
            }
            if (!empty($data['professional_title'])) {
                $properties['Professional Title'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['professional_title']]]
                    ]
                ];
            }
            if (!empty($data['organization'])) {
                $properties['Organization'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['organization']]]
                    ]
                ];
            }
            if (!empty($data['specializations'])) {
                $properties['Specializations'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['specializations']]]
                    ]
                ];
            }
            break;
            
        case 'organization':
            if (!empty($data['contact_name'])) {
                $properties['Contact Name'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['contact_name']]]
                    ]
                ];
            }
            if (!empty($data['organization_name'])) {
                $properties['Organization'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['organization_name']]]
                    ]
                ];
            }
            if (!empty($data['partnership_interest'])) {
                $properties['Partnership Interest'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['partnership_interest']]]
                    ]
                ];
            }
            break;
            
        case 'other':
            if (!empty($data['name'])) {
                $properties['Full Name'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['name']]]
                    ]
                ];
            }
            if (!empty($data['support_interest'])) {
                $properties['Support Interest'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['support_interest']]]
                    ]
                ];
            }
            break;
    }
    
    return $properties;
}

function getDisplayName($data, $audience) {
    switch ($audience) {
        case 'therapist':
            return $data['full_name'] ?? $data['email'];
        case 'organization':
            return $data['contact_name'] ?? $data['organization_name'] ?? $data['email'];
        case 'other':
            return $data['name'] ?? $data['email'];
        default:
            return $data['email'];
    }
}

function createNotionPage($apiKey, $databaseId, $properties) {
    $url = 'https://api.notion.com/v1/pages';
    
    $data = [
        'parent' => ['database_id' => $databaseId],
        'properties' => $properties
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        error_log("Notion API Error: HTTP $httpCode - $response");
        return [
            'success' => false,
            'error' => "HTTP $httpCode",
            'response' => $response
        ];
    }
    
    if ($error) {
        error_log("Notion cURL Error: $error");
        return ['success' => false, 'error' => $error];
    }
    
    $responseData = json_decode($response, true);
    
    if (isset($responseData['id'])) {
        return [
            'success' => true,
            'page_id' => $responseData['id']
        ];
    }
    
    return [
        'success' => false,
        'error' => 'Invalid response from Notion',
        'response' => $response
    ];
}
?>
```

---

## ✅ **Advantages Over Database for MVP**

| Feature | Notion | Database |
|---------|--------|----------|
| **Setup Time** | 5 minutes | 2-4 hours |
| **Visual Interface** | ✅ Beautiful | Need to build |
| **Team Access** | ✅ Easy sharing | Need to configure |
| **Mobile Access** | ✅ Native app | Need to build |
| **Filtering/Views** | ✅ Built-in | Need SQL queries |
| **Cost** | Free tier | Hosting costs |
| **Collaboration** | ✅ Comments, mentions | Need to build |
| **Export** | ✅ CSV, Markdown | ✅ Native |
| **Complex Queries** | Limited | ✅ Full SQL |
| **Scale** | 1000s of entries | Millions |

---

## 🚀 **When to Move to Database**

Move to a proper database when you:
- Have 1000+ form submissions
- Need complex querying and analytics
- Build the full therapist onboarding system
- Implement real-time matching algorithms
- Need sub-second query performance
- Require complex relationships between data

For now, **Notion is perfect** for your MVP stage!

---

## 📞 **Next Steps**

1. ✅ Create Notion integration (5 min)
2. ✅ Create Notion database with columns (5 min)
3. ✅ Update config.php with credentials (2 min)
4. ✅ Create notion-sync.php file (already done in your code)
5. ✅ Test by submitting a form (1 min)
6. ✅ Set up views for different audience types (5 min)

**Total setup time: ~20 minutes**

Then you can immediately view all form submissions in a beautiful Notion interface!
