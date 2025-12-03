<?php
/**
 * QA Test Execution Script
 * Tests all EOI journeys and tracking
 */

require_once __DIR__ . '/../config.php';

echo "🧪 QA Test Execution - Therapair User Journey Tracking\n";
echo "====================================================\n\n";

$testEmail = 'tinokuhn@gmail.com';
$testAliases = [
    'therapist' => $testEmail . '+therapist',
    'clinic' => $testEmail . '+clinic',
    'individual' => $testEmail . '+individual',
    'supporter' => $testEmail . '+supporter'
];

echo "📧 Test Email: $testEmail\n";
echo "📋 Test Aliases:\n";
foreach ($testAliases as $audience => $email) {
    echo "   - $audience: $email\n";
}
echo "\n";

echo "✅ Configuration Check:\n";
$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$notionDbEoi = defined('NOTION_DB_EOI') ? NOTION_DB_EOI : '';
$resendWebhookSecret = defined('RESEND_WEBHOOK_SECRET') ? RESEND_WEBHOOK_SECRET : '';

echo "   - NOTION_TOKEN: " . (!empty($notionToken) ? '✅ SET' : '❌ NOT SET') . "\n";
echo "   - NOTION_DB_EOI: " . (!empty($notionDbEoi) ? '✅ SET' : '❌ NOT SET') . "\n";
echo "   - RESEND_WEBHOOK_SECRET: " . (!empty($resendWebhookSecret) ? '✅ SET' : '❌ NOT SET') . "\n";
echo "\n";

if (empty($notionToken) || empty($notionDbEoi)) {
    die("❌ ERROR: Required configuration missing. Please check config.php\n");
}

echo "📝 Test Instructions:\n";
echo "   1. Submit EOI forms for each audience at:\n";
echo "      - Therapist: https://therapair.com.au/eoi/therapist\n";
echo "      - Clinic: https://therapair.com.au/eoi/clinic\n";
echo "      - Individual: https://therapair.com.au/eoi/individual\n";
echo "      - Supporter: https://therapair.com.au/eoi/supporter\n";
echo "\n";
echo "   2. For each submission, verify:\n";
echo "      ✅ Entry created in Notion EOI database\n";
echo "      ✅ Confirmation email received\n";
echo "      ✅ Open email → Check 'Email Opened Date' updated in Notion\n";
echo "      ✅ Click sandbox link → Check 'Sandbox Clicked Date' updated\n";
echo "      ✅ Click preferences link → Check 'Email Preferences Clicked Date' updated\n";
echo "      ✅ Submit feedback from sandbox → Check feedback saved with tracking ID\n";
echo "      ✅ Verify cross-database linking (EOI → Feedback)\n";
echo "\n";
echo "   3. Check Notion databases:\n";
echo "      - EOI Database: https://www.notion.so/$notionDbEoi\n";
echo "      - Feedback Database: Check NOTION_DB_SANDBOX in config\n";
echo "\n";
echo "🎯 Ready to test! Use the email addresses above for submissions.\n";
echo "\n";
echo "📊 After testing, check:\n";
echo "   - All EOI entries in Notion\n";
echo "   - All tracking properties updated\n";
echo "   - All feedback entries linked\n";
echo "\n";

