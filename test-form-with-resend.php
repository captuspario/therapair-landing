<?php
/**
 * Test Form Submission with Resend
 * Simulates form submission to test Resend email delivery
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/submit-form.php';

// Override POST data to simulate form submission
$_POST = [
    'Audience_Type' => 'individual',
    'Email' => 'tinokuhn@gmail.com', // Test with the email that works in research campaign
    'Name' => 'Test User',
    'Email_Consent' => 'yes'
];

echo "🧪 Testing Form Submission with Resend\n";
echo "=====================================\n\n";

echo "📋 Test Configuration:\n";
echo "   Email: tinokuhn@gmail.com (same as research campaign)\n";
echo "   Audience: Individual\n";
echo "   Email Consent: Yes\n\n";

// Check Resend config
$RESEND_API_KEY = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
$USE_RESEND = defined('USE_RESEND') ? USE_RESEND : false;

echo "🔑 Resend Configuration:\n";
echo "   API Key: " . (empty($RESEND_API_KEY) ? '❌ NOT SET' : '✅ SET') . "\n";
echo "   Use Resend: " . ($USE_RESEND ? '✅ YES' : '❌ NO') . "\n\n";

if (empty($RESEND_API_KEY) || !$USE_RESEND) {
    echo "❌ Resend is not configured properly!\n";
    exit(1);
}

echo "📧 Testing Resend Email Function Directly...\n\n";

// Test the sendEmailViaResend function directly
$testHtml = '<h2>Test Email</h2><p>This is a test email from the form submission system.</p>';
$testSubject = '🧪 Test: Form Submission Email';

// Use same sender format as research campaign
$fromEmail = 'onboarding@resend.dev';
$fromName = 'Therapair Team';
$replyTo = 'contact@therapair.com.au';

$result = sendEmailViaResend(
    'tinokuhn@gmail.com',
    $testSubject,
    $testHtml,
    $fromEmail,
    $fromName,
    $replyTo
);

echo "Result: " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "\n\n";

if ($result) {
    echo "✅ Email sent successfully via Resend!\n";
    echo "   Check Resend dashboard: https://resend.com/emails\n";
    echo "   Check inbox: tinokuhn@gmail.com\n";
} else {
    echo "❌ Failed to send email via Resend\n";
    echo "   Check error logs for details\n";
    echo "   Check server logs: tail -f /var/log/php-errors.log\n";
}

echo "\n";

?>



