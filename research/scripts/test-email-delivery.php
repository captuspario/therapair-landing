<?php
/**
 * Test email delivery to multiple providers
 * Usage: php test-email-delivery.php
 */

$testEmails = [
    'tinoman@me.com',
    // Add more test emails if needed
];

$fromEmail = 'contact@therapair.com.au';
$fromName = 'Therapair Team';
$subject = 'Test Email - Therapair Research';

$message = "
This is a test email from Therapair.

If you receive this, the email server is working.

Time sent: " . date('Y-m-d H:i:s') . "
";

$headers = [];
$headers[] = "From: {$fromName} <{$fromEmail}>";
$headers[] = "Reply-To: {$fromEmail}";
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers[] = "X-Mailer: Therapair Test";

echo "🧪 Testing Email Delivery\n";
echo "=" . str_repeat("=", 50) . "\n\n";

foreach ($testEmails as $email) {
    echo "Sending to: {$email}...\n";
    $sent = mail($email, $subject, $message, implode("\r\n", $headers));
    
    if ($sent) {
        echo "  ✅ PHP mail() returned success\n";
        echo "  ⚠️  BUT: This doesn't guarantee delivery!\n";
        echo "  📧 Check inbox AND spam folder\n";
    } else {
        echo "  ❌ PHP mail() returned false\n";
    }
    echo "\n";
}

echo "=" . str_repeat("=", 50) . "\n";
echo "💡 Next Steps:\n";
echo "1. Check all inboxes (including spam)\n";
echo "2. If nothing received, PHP mail() is not reliable\n";
echo "3. Set up SMTP (SendGrid recommended)\n";
echo "4. See EMAIL-IMPROVEMENTS.md for details\n";
