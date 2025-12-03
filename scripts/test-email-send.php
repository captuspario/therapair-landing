<?php
/**
 * Test Email Sending with Proper Template and Tracking
 * Sends a test EOI confirmation email using the actual formatUserEmail function
 */

// Prevent direct access check from submit-form.php
$_SERVER['REQUEST_METHOD'] = 'POST';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../email-template-base.php';
require_once __DIR__ . '/../submit-form.php';

$testEmail = 'tinokuhn@gmail.com';
$testSubject = 'Test EOI Confirmation Email - Therapist';

echo "📧 Testing Email Sending with Proper Template\n";
echo "============================================\n\n";

// Create test form data matching what submit-form.php expects
$formData = [
    'audience' => 'therapist',
    'email' => $testEmail,
    'full_name' => 'Test User',
    'professional_title' => 'Psychologist',
    'organization' => 'Test Practice',
    'specializations' => 'Anxiety, Depression'
];

// Use the actual formatUserEmail function
$userMessage = formatUserEmail($formData, 'therapist');

// Add tracking URLs (same as submit-form.php does)
$userMessage = addTrackingToEmailLinks($userMessage, $testEmail, 'therapist');

// Check if tracking was added
$hasTracking = strpos($userMessage, 'track.php') !== false;
$hasSandboxTracking = strpos($userMessage, 'track.php?email=') !== false && strpos($userMessage, 'dest=sandbox') !== false;

echo "Email Content Check:\n";
echo "   - Uses formatUserEmail: ✅\n";
echo "   - Tracking URLs added: " . ($hasTracking ? '✅' : '❌') . "\n";
echo "   - Sandbox link tracked: " . ($hasSandboxTracking ? '✅' : '❌') . "\n";
echo "\n";

// Extract sandbox URL from email to verify
if (preg_match('/href="([^"]*sandbox[^"]*)"/', $userMessage, $matches)) {
    $sandboxUrl = $matches[1];
    echo "Sandbox URL in email: $sandboxUrl\n";
    if (strpos($sandboxUrl, 'track.php') !== false) {
        echo "   ✅ Sandbox link has tracking!\n";
    } else {
        echo "   ❌ Sandbox link missing tracking!\n";
    }
    echo "\n";
}

// Send email
$FROM_EMAIL = defined('FROM_EMAIL') ? FROM_EMAIL : 'contact@therapair.com.au';
$FROM_NAME = defined('FROM_NAME') ? FROM_NAME : 'Therapair Team';
$ADMIN_EMAIL = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@therapair.com.au';

echo "Sending email...\n";
echo "   From: $FROM_NAME <$FROM_EMAIL>\n";
echo "   To: $testEmail\n";
echo "   Subject: $testSubject\n\n";

$result = sendEmailViaResend(
    $testEmail,
    $testSubject,
    $userMessage,
    $FROM_EMAIL,
    $FROM_NAME,
    $ADMIN_EMAIL
);

if ($result) {
    echo "✅ Test email sent successfully!\n";
    echo "   Please check your inbox: $testEmail\n";
    echo "   (Check spam folder if not in inbox)\n";
    echo "\n";
    echo "📋 What to verify:\n";
    echo "   1. Email has proper copy (not simplified test version)\n";
    echo "   2. Sandbox link includes tracking (should contain 'track.php')\n";
    echo "   3. Email preferences link includes tracking\n";
} else {
    echo "❌ Test email failed to send!\n";
    echo "   Please check the error messages above.\n";
}
