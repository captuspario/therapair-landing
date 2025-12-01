<?php
/**
 * Test script for form submission and email delivery
 * Usage: php test-form-submission.php
 */

require_once __DIR__ . '/config.php';

// Test email addresses (update these)
$TEST_ADMIN_EMAIL = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@therapair.com.au';
$TEST_USER_EMAIL = 'test@example.com'; // Change this to your test email

echo "🧪 Testing Therapair Form Submission Engine\n";
echo "==========================================\n\n";

// Check configuration
echo "📋 Configuration Check:\n";
echo "  ADMIN_EMAIL: " . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : '❌ NOT DEFINED') . "\n";
echo "  FROM_EMAIL: " . (defined('FROM_EMAIL') ? FROM_EMAIL : '❌ NOT DEFINED') . "\n";
echo "  FROM_NAME: " . (defined('FROM_NAME') ? FROM_NAME : '❌ NOT DEFINED') . "\n";
echo "  USE_NOTION_SYNC: " . (defined('USE_NOTION_SYNC') && USE_NOTION_SYNC ? '✅ YES' : '❌ NO') . "\n";
echo "\n";

// Test 1: Check PHP mail() function
echo "🔍 Test 1: PHP mail() function availability\n";
if (function_exists('mail')) {
    echo "  ✅ mail() function is available\n";
} else {
    echo "  ❌ mail() function is NOT available\n";
    exit(1);
}
echo "\n";

// Test 2: Send test email to admin
echo "📧 Test 2: Sending test email to admin (" . $TEST_ADMIN_EMAIL . ")\n";
$testSubject = "🧪 Test: Therapair Form Submission Engine";
$testMessage = "<h2>Test Email from Therapair Form Submission Engine</h2>
<p>This is a test email to verify that the email system is working correctly.</p>
<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>
<p>If you receive this email, the form submission engine is configured correctly.</p>";

$headers = "From: " . (defined('FROM_NAME') ? FROM_NAME : 'Therapair Team') . " <" . (defined('FROM_EMAIL') ? FROM_EMAIL : 'contact@therapair.com.au') . ">\r\n";
$headers .= "Reply-To: " . (defined('FROM_EMAIL') ? FROM_EMAIL : 'contact@therapair.com.au') . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
$headers .= "X-Mailer: Therapair Test Script\r\n";

$adminSent = @mail($TEST_ADMIN_EMAIL, $testSubject, $testMessage, $headers);
if ($adminSent) {
    echo "  ✅ Email sent successfully (returned true)\n";
    echo "  ⚠️  Note: mail() returning true doesn't guarantee delivery\n";
} else {
    echo "  ❌ Failed to send email\n";
}
echo "\n";

// Test 3: Simulate form submission data
echo "📝 Test 3: Simulating form submission\n";
$_POST = [
    'Audience_Type' => 'individual',
    'Email' => $TEST_USER_EMAIL,
    'Name' => 'Test User',
    'Therapy_Interests' => 'LGBTQ+ affirming care, Cultural competency',
    'Additional_Thoughts' => 'This is a test submission',
    'Email_Consent' => 'yes',
    '_honey' => '' // Honeypot (empty = valid)
];

echo "  Form data:\n";
echo "    Audience Type: " . $_POST['Audience_Type'] . "\n";
echo "    Email: " . $_POST['Email'] . "\n";
echo "    Name: " . $_POST['Name'] . "\n";
echo "    Consent: " . $_POST['Email_Consent'] . "\n";
echo "\n";

// Test 4: Validate form data
echo "✅ Test 4: Form validation\n";
$errors = [];

if (empty($_POST['Audience_Type'])) {
    $errors[] = "Audience type is required";
} else {
    echo "  ✅ Audience type: " . $_POST['Audience_Type'] . "\n";
}

if (empty($_POST['Email'])) {
    $errors[] = "Email is required";
} elseif (!filter_var($_POST['Email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
} else {
    echo "  ✅ Email: " . $_POST['Email'] . "\n";
}

if (empty($_POST['Email_Consent']) || $_POST['Email_Consent'] !== 'yes') {
    $errors[] = "Email consent is required";
} else {
    echo "  ✅ Email consent: Given\n";
}

if (!empty($errors)) {
    echo "  ❌ Validation errors:\n";
    foreach ($errors as $error) {
        echo "    - " . $error . "\n";
    }
} else {
    echo "  ✅ All validations passed\n";
}
echo "\n";

// Test 5: Check email headers
echo "📋 Test 5: Email headers check\n";
if (defined('FROM_EMAIL') && !empty(FROM_EMAIL)) {
    echo "  ✅ FROM_EMAIL configured: " . FROM_EMAIL . "\n";
} else {
    echo "  ❌ FROM_EMAIL not configured\n";
}

if (defined('ADMIN_EMAIL') && !empty(ADMIN_EMAIL)) {
    echo "  ✅ ADMIN_EMAIL configured: " . ADMIN_EMAIL . "\n";
} else {
    echo "  ❌ ADMIN_EMAIL not configured\n";
}
echo "\n";

// Summary
echo "==========================================\n";
echo "📊 Test Summary\n";
echo "==========================================\n";
echo "✅ Configuration: " . (defined('ADMIN_EMAIL') && defined('FROM_EMAIL') ? "OK" : "MISSING VALUES") . "\n";
echo "✅ PHP mail(): " . (function_exists('mail') ? "Available" : "NOT AVAILABLE") . "\n";
echo "✅ Test email: " . ($adminSent ? "Sent" : "Failed") . "\n";
echo "✅ Form validation: " . (empty($errors) ? "Passed" : "Failed") . "\n";
echo "\n";

echo "⚠️  IMPORTANT NOTES:\n";
echo "  1. PHP mail() may return true even if email isn't delivered\n";
echo "  2. Check spam/junk folders for test emails\n";
echo "  3. Consider using SMTP (SendGrid/Mailgun) for better deliverability\n";
echo "  4. Test with a real email address to verify delivery\n";
echo "\n";

echo "🔧 Next Steps:\n";
echo "  1. Check " . $TEST_ADMIN_EMAIL . " inbox (and spam folder)\n";
echo "  2. If emails don't arrive, check server logs: /var/log/mail.log\n";
echo "  3. Consider setting up SMTP for better reliability\n";
echo "  4. Test the actual form at: https://therapair.com.au/#get-started\n";
echo "\n";

?>

