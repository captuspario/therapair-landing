# Email Tracking Setup - Required Actions

## ✅ What Has Been Implemented

### Code Changes:
1. **Tracking URLs Added** - All email links now use tracking redirects with UTM parameters
2. **UTM Parameters** - All links include:
   - `utm_source=email`
   - `utm_medium=eoi_confirmation`
   - `utm_campaign={link_type}` (sandbox_demo, email_preferences, website)
   - `utm_content={audience}` (therapist, individual, organization, other)
3. **track.php Enhanced** - Now tracks clicks and updates Notion database
4. **Email Hash Tracking** - Uses email hash to identify users (since Notion sync happens after email send)

---

## ⚠️ REQUIRED: Add Tracking Properties to Notion EOI Database

You **MUST** add these properties to your Notion EOI database (`NOTION_DB_EOI`) for tracking to work:

### Required Properties:

| Property Name | Type | Description |
|---------------|------|-------------|
| **Last Engagement Date** | Date | Most recent engagement timestamp |
| **Sandbox Clicked Date** | Date | When sandbox demo link was clicked |
| **Email Preferences Clicked Date** | Date | When email preferences link was clicked |
| **Last Clicked Link** | Rich Text | Name of the last link clicked |

### Optional Properties (Recommended):

| Property Name | Type | Description |
|---------------|------|-------------|
| **Email Opens Count** | Number | Total number of email opens |
| **Email Clicks Count** | Number | Total number of link clicks |
| **UTM Source** | Rich Text | Source from UTM parameters |
| **UTM Campaign** | Rich Text | Campaign from UTM parameters |

---

## 🔧 How to Add Properties in Notion

1. Open your EOI database in Notion
2. Click the **"+"** button at the top right to add a new property
3. For each property above:
   - Enter the exact property name (case-sensitive!)
   - Select the correct type (Date, Rich Text, Number)
   - Click "Add"

**Important:** Property names must match **exactly** (including capitalization) as shown in the table above.

---

## 🧪 Testing

After adding the properties:

1. Submit a test EOI form
2. Check your email for the confirmation
3. Click the "View Sandbox Demo" link
4. Check your Notion database - you should see:
   - `Last Engagement Date` updated
   - `Sandbox Clicked Date` updated
   - `Last Clicked Link` = "sandbox_demo"

---

## 📊 What Gets Tracked

### Currently Tracked:
- ✅ Sandbox demo link clicks
- ✅ Email preferences link clicks
- ✅ UTM parameters (for analytics)
- ✅ Click timestamps

### Not Yet Tracked (Future Enhancement):
- Email opens (requires Resend webhook setup)
- Click counts (requires reading current value first)
- Multiple clicks per user (currently only tracks last click)

---

## 🚀 Next Steps

1. **Add tracking properties to Notion** (REQUIRED)
2. Test with a real EOI submission
3. Verify tracking works in Notion database
4. (Optional) Set up Resend webhooks for email open tracking
5. (Optional) Add click counting logic

---

## 🔍 Troubleshooting

### Tracking not working?
1. Check that property names match exactly in Notion
2. Check server error logs for Notion API errors
3. Verify `NOTION_TOKEN` is set correctly
4. Verify `NOTION_DB_EOI` is set correctly

### Links not redirecting?
1. Check that `track.php` is accessible
2. Verify destination URLs are correct
3. Check server error logs

---

## 📝 Notes

- Tracking uses email hash (MD5) to identify users since Notion sync happens after email send
- Future enhancement: Store email hash → Notion page ID mapping for faster lookups
- UTM parameters are preserved through the redirect for analytics tools (Google Analytics, etc.)

