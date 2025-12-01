<?php
/**
 * Test Resend Email Function
 * Usage: php test-resend-email.php
 */

require_once __DIR__ . '/config.php';

// Get config values
$RESEND_API_KEY = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
$USE_RESEND = defined('USE_RESEND') ? USE_RESEND : false;
$ADMIN_EMAIL = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@therapair.com.au';
$FROM_EMAIL = defined('FROM_EMAIL') ? FROM_EMAIL : 'contact@therapair.com.au';
$FROM_NAME = defined('FROM_NAME') ? FROM_NAME : 'Therapair Team';

echo "🧪 Testing Resend Email Function\n";
echo "================================\n\n";

// Check configuration
echo "📋 Configuration:\n";
echo "  RESEND_API_KEY: " . (empty($RESEND_API_KEY) ? '❌ NOT SET' : '✅ SET (' . substr($RESEND_API_KEY, 0, 10) . '...)') . "\n";
echo "  USE_RESEND: " . ($USE_RESEND ? '✅ YES' : '❌ NO') . "\n";
echo "  ADMIN_EMAIL: {$ADMIN_EMAIL}\n";
echo "  FROM_EMAIL: {$FROM_EMAIL}\n";
echo "\n";

if (empty($RESEND_API_KEY) || !$USE_RESEND) {
    echo "❌ Resend is not properly configured!\n";
    echo "   Please set RESEND_API_KEY and USE_RESEND in config.php\n";
    exit(1);
}

// Test email data
$testTo = $ADMIN_EMAIL;
$testSubject = '🧪 Test: Resend Email from Therapair Form';
$testHtml = '<h2>Test Email</h2><p>This is a test email to verify Resend integration is working.</p><p>Timestamp: ' . date('Y-m-d H:i:s') . '</p>';

echo "📧 Test Email Details:\n";
echo "  To: {$testTo}\n";
echo "  Subject: {$testSubject}\n";
echo "  From: {$FROM_NAME} <onboarding@resend.dev>\n";
echo "\n";

// Test the sendEmailViaResend function
function sendEmailViaResend($to, $subject, $html, $fromEmail, $fromName, $replyTo = '')
{
    global $RESEND_API_KEY, $USE_RESEND;
    
    // If Resend is disabled or API key is missing, return false
    if (!$USE_RESEND || empty($RESEND_API_KEY)) {
        error_log("Resend email skipped: USE_RESEND=" . ($USE_RESEND ? 'true' : 'false') . ", API_KEY=" . (empty($RESEND_API_KEY) ? 'empty' : 'set'));
        return ['success' => false, 'error' => 'Resend disabled or API key missing'];
    }
    
    // Prepare email data for Resend API
    // Handle sender format (can be string like "Therapair <onboarding@resend.dev>" or just email)
    $fromAddress = $fromEmail;
    if (strpos($fromEmail, '<') !== false) {
        // Already formatted as "Name <email>"
        $fromAddress = $fromEmail;
    } else {
        // Format as "Name <email>"
        $fromAddress = $fromName . ' <' . $fromEmail . '>';
    }
    
    $emailData = [
        'from' => $fromAddress,
        'to' => [$to],
        'subject' => $subject,
        'html' => $html
    ];
    
    if (!empty($replyTo)) {
        $emailData['reply_to'] = $replyTo;
    }
    
    echo "🔍 Sending via Resend API...\n";
    echo "  From: {$fromAddress}\n";
    echo "  To: {$to}\n";
    echo "  Payload: " . json_encode($emailData, JSON_PRETTY_PRINT) . "\n\n";
    
    // Send via Resend API
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($emailData),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $RESEND_API_KEY,
            'Content-Type: application/json'
        ],
        CURLOPT_VERBOSE => true
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "📡 Response:\n";
    echo "  HTTP Code: {$httpCode}\n";
    echo "  Response: " . substr($response, 0, 500) . "\n";
    if ($curlError) {
        echo "  cURL Error: {$curlError}\n";
    }
    echo "\n";
    
    if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        if (isset($responseData['id'])) {
            echo "✅ Email sent successfully!\n";
            echo "  Message ID: {$responseData['id']}\n";
            return ['success' => true, 'message_id' => $responseData['id']];
        }
    }
    
    // Log error details
    $errorMsg = "Resend email failed. HTTP Code: {$httpCode}";
    if ($curlError) {
        $errorMsg .= ", cURL Error: {$curlError}";
    }
    if ($response) {
        $errorMsg .= ", Response: " . substr($response, 0, 200);
    }
    error_log($errorMsg);
    echo "❌ Failed to send email\n";
    echo "  Error: {$errorMsg}\n";
    
    return ['success' => false, 'error' => $errorMsg, 'response' => $response, 'http_code' => $httpCode];
}

// Test sending
$result = sendEmailViaResend(
    $testTo,
    $testSubject,
    $testHtml,
    'onboarding@resend.dev',
    $FROM_NAME,
    $FROM_EMAIL
);

echo "\n================================\n";
if ($result['success']) {
    echo "✅ TEST PASSED\n";
    echo "Check your inbox ({$testTo}) for the test email.\n";
    echo "Also check spam/junk folder.\n";
} else {
    echo "❌ TEST FAILED\n";
    echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    if (isset($result['response'])) {
        echo "Full response: " . $result['response'] . "\n";
    }
}

echo "\n";

?>

