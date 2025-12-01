# Email MCP Server Research

## Summary

After researching existing email MCP servers, here are the best options for Therapair's research campaign needs:

---

## Top Recommendations

### 1. **Resend MCP** ⭐ **BEST FOR DEVELOPERS**
- **URL:** https://resend.com/mcp
- **Why it's great:**
  - Modern, developer-friendly API
  - Excellent deliverability
  - Clean integration with Cursor/Claude
  - Good documentation and quickstart guides
  - Free tier: 3,000 emails/month, 100 emails/day
  - Paid: $20/month for 50,000 emails
- **Best for:** Production email sending with modern tooling

### 2. **Mailtrap Email MCP Server** ⭐ **BEST FOR TESTING + DELIVERY**
- **URL:** https://mailtrap.io/email-mcp-server/
- **Why it's great:**
  - Code-free email sending
  - High deliverability rates
  - In-depth analytics
  - Automatic email authentication (SPF, DKIM, DMARC)
  - 30 days of email logs
  - Free tier: 500 emails/month
  - Paid: $24.99/month for 10,000 emails
- **Best for:** Testing + production with analytics focus

### 3. **MailerSend MCP Server** ⭐ **BEST FOR AUTOMATION**
- **URL:** https://www.mailersend.com/features/mcp
- **Why it's great:**
  - Live data access for AI tools
  - Task automation support
  - Send or schedule emails
  - Troubleshooting tools
  - Free tier: 3,000 emails/month
  - Paid: $35/month for 50,000 emails
- **Best for:** Complex workflows and automation

### 4. **Zapier Email by Zapier MCP** ⭐ **BEST FOR SIMPLICITY**
- **URL:** https://zapier.com/mcp/email
- **Why it's great:**
  - No code required
  - Connects with existing Zapier workflows
  - Simple setup
  - Free tier: Limited
  - Paid: $19.99/month (Zapier Starter)
- **Best for:** Non-technical users, quick setup

### 5. **Postmark MCP Server** ⭐ **BEST FOR TRANSACTIONAL**
- **URL:** https://postmarkapp.com/updates/postmark-labs-new-mcp-server-for-ai-powered-email
- **Why it's great:**
  - Focused on transactional emails
  - Fast delivery (under 5 seconds)
  - Detailed tracking and logs
  - Free tier: 100 emails/month
  - Paid: $15/month for 10,000 emails
- **Best for:** Transactional emails only

### 6. **Gmail MCP Server (Open Source)** ⭐ **BEST FOR GMAIL USERS**
- **URL:** https://github.com/david-strejc/gmail-mcp-server
- **Why it's great:**
  - Free and open source
  - Gmail integration via IMAP/SMTP
  - Search, retrieve, manage labels
  - Send/forward emails
- **Best for:** Gmail users, self-hosted solutions

---

## Comparison Table

| Provider | Free Tier | Paid Tier | Best For | Setup Complexity |
|----------|-----------|-----------|----------|------------------|
| **Resend** | 3,000/mo | $20/50k | Modern devs | ⭐ Easy |
| **Mailtrap** | 500/mo | $25/10k | Testing + Analytics | ⭐ Easy |
| **MailerSend** | 3,000/mo | $35/50k | Automation | ⭐⭐ Medium |
| **Zapier** | Limited | $20/mo | Non-technical | ⭐ Very Easy |
| **Postmark** | 100/mo | $15/10k | Transactional | ⭐ Easy |
| **Gmail MCP** | Free | Free | Gmail users | ⭐⭐⭐ Hard |

---

## Recommendation for Therapair

### **Primary Choice: Resend MCP**

**Why:**
1. ✅ **Modern & Reliable:** Built for 2025, excellent deliverability
2. ✅ **Developer-Friendly:** Clean API, good docs, easy Cursor integration
3. ✅ **Cost-Effective:** Free tier covers testing, $20/month for production
4. ✅ **Perfect Fit:** Matches your PHP backend + AI assistant workflow
5. ✅ **Future-Proof:** Active development, growing ecosystem

### **Secondary Choice: Mailtrap MCP**

**Why:**
1. ✅ **Testing Focus:** Great for validating emails before sending
2. ✅ **Analytics:** Detailed logs and tracking
3. ✅ **Deliverability:** Automatic SPF/DKIM/DMARC setup

### **Hybrid Approach (Recommended):**
- Use **Mailtrap** for testing/development
- Use **Resend** for production sending

---

## Next Steps

1. **Sign up for Resend** (free tier)
2. **Get API key** from Resend dashboard
3. **Install Resend MCP** in Cursor
4. **Test email sending** via AI assistant
5. **Integrate with PHP backend** (optional, can use MCP directly)

---

## Alternative: Build Custom MCP

If none of these fit perfectly, we can build a custom MCP server that:
- Integrates with your existing PHP backend
- Handles token generation
- Manages Notion database updates
- Sends via your preferred provider (SendGrid, Mailgun, etc.)

**Time estimate:** 2-3 hours
**Benefit:** Full control, custom features

---

## Resources

- [Resend MCP Documentation](https://resend.com/mcp)
- [Mailtrap MCP Guide](https://mailtrap.io/email-mcp-server/)
- [Model Context Protocol Spec](https://modelcontextprotocol.io/)
- [MCP Server Directory](https://modelcontextprotocol.io/servers)

---

**Generated:** 2025-01-17
**Research Status:** Complete ✅

