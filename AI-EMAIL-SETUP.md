# 🤖 AI-Powered Personalized Email System

## ✨ What You Have Now

Your Therapair landing page now has **AI-powered personalized email responses** using OpenAI GPT-4o-mini!

Every user who submits the form will receive a **unique, contextual email** that:
- ✅ Addresses their specific interests and needs
- ✅ References their therapy concerns/specializations/partnership goals
- ✅ Feels warm, personal, and human-written
- ✅ Signs as "Tino" from Therapair Team
- ✅ Costs only ~$0.005 per email

---

## 🚀 Setup Instructions (5 minutes)

### **Step 1: Get Your OpenAI API Key**

1. Go to: https://platform.openai.com/api-keys
2. Sign in (or create account)
3. Click **"Create new secret key"**
4. **Name it:** "Therapair Email System"
5. **Copy the key** (starts with `sk-proj-...`)
6. **Save it somewhere safe** (you can't see it again!)

---

### **Step 2: Create config.php on Hostinger**

**Option A: Via Hostinger File Manager**
1. Log in to Hostinger → File Manager
2. Navigate to `domains/therapair.com.au/public_html/`
3. Click **"New File"** → Name it `config.php`
4. Copy the contents from `config.example.php`
5. **Replace** `YOUR_OPENAI_API_KEY_HERE` with your actual API key
6. Save

**Option B: Via SSH (Faster)**
```bash
# On your Mac
cd /Users/tino/Projects/therapair-landing-page
cp config.example.php config.php

# Edit config.php and add your real API key
# Then upload to Hostinger via FTP or:
scp -P 65002 config.php u549396201@45.87.81.159:/home/u549396201/domains/therapair.com.au/public_html/
```

---

### **Step 3: Test the System**

1. **Visit:** https://therapair.com.au
2. **Submit the form** with real interests
3. **Check your email** (the one you submitted)
4. **You should receive** a personalized AI-generated email!

---

## 📧 Example AI-Generated Emails

### **Individual - LGBTQ+ Care + Trauma**

```
Hi there,

Thank you for reaching out to Therapair. I can see that finding LGBTQ+ 
affirming care and trauma-informed support is really important to you. 
These are incredibly valid needs, and I want you to know that we have 
therapists who specialize in exactly these areas and understand the 
unique experiences of LGBTQ+ individuals.

Our matching process will prioritize therapists with strong backgrounds 
in trauma-informed care who create truly affirming spaces. You deserve 
a therapist who sees all of you and provides the culturally competent 
care you need.

I'll personally review your submission today and reach out within 24 
hours with next steps.

Warm regards,
Tino
Therapair Team
```

---

### **Therapist - Trauma Specialist**

```
Hi Dr. Smith,

Thank you for your interest in joining the Therapair network as a 
Licensed Clinical Psychologist. We're building a community of inclusive, 
culturally competent practitioners, and your expertise in trauma therapy 
and EMDR aligns perfectly with our mission.

We're particularly excited about therapists who bring specialized skills 
to underserved populations. Your background could make a real difference 
for clients seeking trauma-informed care.

I'd love to discuss how we can work together to serve our community. 
I'll reach out within 24 hours to schedule a conversation.

Warm regards,
Tino
Therapair Team
```

---

### **Organization Partnership**

```
Hi Sarah,

Thank you for reaching out on behalf of ABC Clinic. We're excited about 
the possibility of partnering with forward-thinking organizations like 
yours that share our commitment to inclusive mental health care.

Your interest in referral partnerships is exactly the kind of collaboration 
we're looking to build. We believe Therapair can help your organization 
better serve diverse populations with our intelligent matching system.

I'll personally reach out within 24 hours to discuss how we can work 
together to achieve your goals.

Warm regards,
Tino
Therapair Team
```

---

## 🎯 How It Works

### **The AI Prompt System:**

```
System Prompt: "You are Tino from Therapair. Write warm, empathetic 
emails. Use first person ('I'). Be professional but approachable..."

User Context:
- Audience: Individual
- Interests: LGBTQ+ care, Trauma
- Email: user@example.com

AI Response: [Generates unique, contextual email]
```

### **What The AI Sees:**

**For each submission, the AI receives:**
- Audience type (Individual, Therapist, Organization, Supporter)
- All form fields they filled out
- Their specific interests/concerns/specializations
- Context about what they're looking for

### **What The AI Generates:**
- Personalized greeting (uses their name if provided)
- Acknowledgment of their specific interests
- Relevant information about Therapair's offerings
- Warm, encouraging language
- Promise to follow up within 24 hours
- Signed as "Tino, Therapair Team"

---

## 💰 Cost Breakdown

### **OpenAI API Pricing (GPT-4o-mini):**

| Volume | Input Tokens | Output Tokens | Total Cost |
|--------|--------------|---------------|------------|
| 1 email | ~200 tokens | ~200 tokens | ~$0.005 |
| 50 emails/month | 10k tokens | 10k tokens | ~$0.25 |
| 100 emails/month | 20k tokens | 20k tokens | ~$0.50 |
| 500 emails/month | 100k tokens | 100k tokens | ~$2.50 |

**Your budget:** <$10/month  
**Estimated usage:** ~50-100 emails/month  
**Actual cost:** ~$0.25-$0.50/month 🎉

**You're well under budget!**

---

## 🔒 Security Best Practices

### **✅ What I've Set Up:**

1. **API Key in Separate File** (`config.php`)
2. **config.php in .gitignore** (won't be committed to GitHub)
3. **Example config** (`config.example.php`) for reference
4. **Fallback to static template** if AI fails
5. **Error logging** for debugging

### **⚠️ Important:**

**NEVER commit your real API key to git!**
- ✅ `config.example.php` → Safe to commit (has placeholder)
- ❌ `config.php` → NEVER commit (has real key)

The `.gitignore` file now blocks `config.php` from being committed.

---

## 🎛️ Configuration Options

### **Enable/Disable AI:**

In `config.php`:
```php
define('USE_AI_PERSONALIZATION', true);  // AI-powered emails
define('USE_AI_PERSONALIZATION', false); // Static template emails
```

### **Change AI Model:**

```php
// Most cost-effective (recommended)
define('AI_MODEL', 'gpt-4o-mini');

// Best quality (4x more expensive)
define('AI_MODEL', 'gpt-4o');

// Cheapest (lower quality)
define('AI_MODEL', 'gpt-3.5-turbo');
```

### **Adjust AI Tone:**

Edit the system prompt in `submit-form.php` (line ~100):
```php
$systemPrompt = "You are Tino from the Therapair team...
- More casual / More formal
- Longer / Shorter
- Different personality
```

---

## 🐛 Troubleshooting

### **Problem: AI emails not sending**

**Check:**
1. ✅ Is `config.php` created on Hostinger?
2. ✅ Is your OpenAI API key correct?
3. ✅ Does `USE_AI_PERSONALIZATION` = true?
4. ✅ Check Hostinger error logs

**Fallback:** If AI fails, system automatically sends static template email (you still get emails!)

---

### **Problem: Emails are weird/generic**

**Solution:** Adjust the AI prompt in `submit-form.php`:
- Make it more specific
- Add examples of good emails
- Adjust temperature (0.7 = creative, 0.3 = conservative)

---

### **Problem: Too expensive**

**Solution:** 
- Switch to `gpt-3.5-turbo` (cheaper)
- Or disable AI: `USE_AI_PERSONALIZATION = false`
- Monitor usage at: https://platform.openai.com/usage

---

## 📊 Monitoring & Analytics

### **Track API Usage:**

1. Go to: https://platform.openai.com/usage
2. View daily/monthly costs
3. Set spending limits if desired

### **Set Spending Limit (Recommended):**

1. Go to: https://platform.openai.com/account/limits
2. Set **Monthly budget:** $5 or $10
3. You'll get email alerts when approaching limit

---

## 🔄 How Form Submission Works Now

### **User Journey:**

```
1. User fills form → Clicks Submit
   ↓
2. submit-form.php receives data
   ↓
3. Sends admin notification (you) → contact@therapair.com.au
   ↓
4. Calls OpenAI API with user's specific data
   ↓
5. AI generates personalized response in 1-2 seconds
   ↓
6. Sends beautiful email to user
   ↓
7. Redirects to thank-you page
   ↓
8. DONE! (Total time: ~2-3 seconds)
```

### **What You See (Admin Email):**

```
Subject: 🎯 New Interest: Individual Seeking Therapy

Audience Type: Individual
Email: user@example.com
Therapy Interests: LGBTQ+ affirming care, Trauma-informed care

[All form data formatted nicely]
```

### **What User Sees (AI-Generated):**

```
Subject: Thank you for your interest in Therapair

Hi there,

[Unique AI-generated message specifically addressing their 
interests, concerns, and needs - every email is different!]

Warm regards,
Tino
Therapair Team
```

---

## 📝 Files in Your System

| File | Purpose | Commit to Git? |
|------|---------|----------------|
| `submit-form.php` | Main form handler with AI | ✅ Yes |
| `config.example.php` | Example configuration | ✅ Yes |
| `config.php` | **YOUR API KEY** | ❌ NO! (.gitignored) |
| `index.html` | Landing page with form | ✅ Yes |
| `thank-you.html` | Success page | ✅ Yes |
| `.gitignore` | Security settings | ✅ Yes |

---

## 🚀 Deployment Checklist

- [ ] **Create config.php** on Hostinger from config.example.php
- [ ] **Add your real OpenAI API key** to config.php
- [ ] **Upload submit-form.php** to Hostinger (or git pull)
- [ ] **Test form submission** with your email
- [ ] **Check you receive** AI-generated confirmation
- [ ] **Verify API usage** on OpenAI dashboard
- [ ] **Set spending limit** on OpenAI ($5-10/month)

---

## 🎉 Benefits of AI-Powered Emails

### **vs. Static Templates:**

| Feature | Static Template | AI-Powered |
|---------|----------------|------------|
| **Personalization** | Limited | Deep contextual |
| **Variety** | Same every time | Unique every time |
| **Tone** | Generic | Matches situation |
| **Engagement** | Lower | Higher |
| **Maintenance** | Need to write templates | Auto-generates |
| **Cost** | Free | ~$0.50/month |

### **User Experience:**

**Static:** "Thanks for your interest. We'll be in touch."

**AI-Powered:** "I can see you're particularly interested in LGBTQ+ affirming care and trauma support. We have therapists who specialize in exactly these areas and understand the unique challenges..."

**Result:** Users feel heard, understood, and valued! 🎯

---

## 💡 Advanced Options (Future)

### **Add More Context:**

You could enhance the AI with:
- Information about your available therapists
- Current availability/wait times
- Specific matching capabilities
- Pricing information

### **Sentiment Analysis:**

Detect urgency/tone in submissions:
- High urgency → Faster follow-up promise
- Specific trauma → More empathetic language
- Professional inquiry → More business-focused

### **Multi-Language:**

Automatically detect language and respond accordingly:
```php
// AI can generate responses in user's language
$userPrompt .= "\nRespond in the same language as the user's submission.";
```

---

## 📞 Support

### **OpenAI Documentation:**
- API Reference: https://platform.openai.com/docs/api-reference
- Pricing: https://openai.com/pricing
- Usage Dashboard: https://platform.openai.com/usage

### **Your Files:**
- Main handler: `submit-form.php`
- Config example: `config.example.php`
- This guide: `AI-EMAIL-SETUP.md`

---

## ✅ Quick Start

```bash
# 1. Copy example config
cp config.example.php config.php

# 2. Edit config.php and add your OpenAI API key

# 3. Upload to Hostinger (or use git pull)

# 4. Test the form!
```

**That's it! You now have AI-powered personalized emails!** 🤖✨

---

## 🎯 Cost Estimate for Your Volume

**Assumptions:**
- 50-100 form submissions per month
- Average email: ~400 tokens total (200 input + 200 output)
- Model: gpt-4o-mini

**Monthly Cost:**
```
100 emails × 400 tokens × ($0.15/1M input + $0.60/1M output)
= 100 × 400 × $0.000375
= ~$0.15/month
```

**Actual cost will likely be:** $0.15-$0.50/month 🎉

**Well under your $10/month budget!**

