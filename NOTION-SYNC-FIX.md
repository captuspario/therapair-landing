# Notion Database Sync Fix

## Issue
EOI (Expression of Interest) entries were not being saved to the Notion database `2875c25944da80c0b14afbbdf2510bb0`.

## Root Cause
1. The form submission logic was routing 'individual' audience entries to a different database (`NOTION_DB_SURVEY`) instead of the EOI database
2. Error logging was insufficient to diagnose sync failures
3. The sync function lacked proper fallback to `NOTION_DB_EOI` constant

## Fixes Applied

### 1. **Unified Database Routing** (`submit-form.php`)
- Changed all EOI form submissions to go to `NOTION_DB_EOI` database
- Removed the logic that routed individuals to a different database
- All audience types (individual, therapist, organization, other) now go to the EOI database

### 2. **Enhanced Error Logging** (`notion-sync.php`)
- Added detailed logging for configuration checks
- Logs token status (without exposing full token)
- Logs database ID being used
- Enhanced API error logging with HTTP codes, error codes, and messages
- Logs successful syncs with page IDs

### 3. **Improved Fallback Logic** (`notion-sync.php`)
- Added fallback to `NOTION_DB_EOI` if `NOTION_DATABASE_ID` is not defined
- Better error messages explaining what's missing

### 4. **Test Script** (`test-notion-sync.php`)
- Created diagnostic script to test Notion sync connection
- Verifies configuration is correct
- Tests actual sync with sample data
- Provides troubleshooting steps if sync fails

## Configuration Required

Make sure `config.php` has:

```php
define('USE_NOTION_SYNC', true);
define('NOTION_TOKEN', 'your_token_here');
define('NOTION_DB_EOI', '2875c25944da80c0b14afbbdf2510bb0');
```

## Testing

1. **Test the connection:**
   ```bash
   php test-notion-sync.php
   ```

2. **Submit a test form** on the landing page and check:
   - Server error logs for sync status
   - Notion database for the new entry

3. **Check server logs** for messages like:
   - "Notion sync: Success! Entry created in EOI database. Page ID: ..."
   - Or error messages explaining what went wrong

## Troubleshooting

If entries still aren't saving:

1. **Check configuration:**
   - Verify `USE_NOTION_SYNC` is `true`
   - Verify `NOTION_TOKEN` is set and valid
   - Verify `NOTION_DB_EOI` matches your database ID

2. **Check Notion integration:**
   - Database must be shared with the Notion integration
   - Integration must have access to the workspace
   - Token must be active and not revoked

3. **Check server error logs:**
   - Look for "Notion sync" messages
   - Check for API error codes (401 = unauthorized, 404 = database not found, etc.)

4. **Run the test script:**
   ```bash
   php test-notion-sync.php
   ```
   This will diagnose the exact issue.

## Database ID
The EOI database ID is: `2875c25944da80c0b14afbbdf2510bb0`

## Files Changed
- `submit-form.php` - Updated database routing logic
- `notion-sync.php` - Enhanced error logging and fallbacks
- `test-notion-sync.php` - New diagnostic script



