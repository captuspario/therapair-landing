<?php
/**
 * Test Resend Email Function Directly
 * Tests the exact same function used in form submission
 */

require_once __DIR__ . '/config.php';

echo "🧪 Testing Resend Email Function (Direct Test)\n";
echo "==============================================\n\n";

// Get config
$RESEND_API_KEY = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
$USE_RESEND = defined('USE_RESEND') ? USE_RESEND : false;

if (empty($RESEND_API_KEY) || !$USE_RESEND) {
    echo "❌ Resend not configured!\n";
    exit(1);
}

// Copy the exact function from submit-form.php
function sendEmailViaResend($to, $subject, $html, $fromEmail, $fromName, $replyTo = '')
{
    global $RESEND_API_KEY, $USE_RESEND;
    
    if (!$USE_RESEND || empty($RESEND_API_KEY)) {
        error_log("Resend email skipped: USE_RESEND=" . ($USE_RESEND ? 'true' : 'false') . ", API_KEY=" . (empty($RESEND_API_KEY) ? 'empty' : 'set'));
        return false;
    }
    
    // Use exact same format as research campaign (which is working)
    $fromAddress = $fromName . ' <' . $fromEmail . '>';
    
    $emailData = [
        'from' => $fromAddress,
        'to' => $to,
        'subject' => $subject,
        'html' => $html
    ];
    
    if (!empty($replyTo)) {
        $emailData['reply_to'] = $replyTo;
    }
    
    echo "📤 Sending email via Resend API...\n";
    echo "   From: {$fromAddress}\n";
    echo "   To: {$to}\n";
    echo "   Subject: {$subject}\n\n";
    
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
        CURLOPT_TIMEOUT => 30
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    echo "📡 API Response:\n";
    echo "   HTTP Code: {$httpCode}\n";
    
    if ($httpCode === 200 || $httpCode === 201) {
        $responseData = json_decode($response, true);
        if (isset($responseData['id'])) {
            echo "   ✅ Success! Message ID: {$responseData['id']}\n";
            echo "\n   Check Resend dashboard: https://resend.com/emails\n";
            return true;
        } else {
            echo "   ⚠️  Success status but no message ID\n";
            echo "   Response: " . substr($response, 0, 200) . "\n";
        }
    } else {
        $responseData = json_decode($response, true);
        $errorMsg = isset($responseData['message']) ? $responseData['message'] : 'Unknown error';
        echo "   ❌ Error: {$errorMsg}\n";
        echo "   Full Response: " . substr($response, 0, 300) . "\n";
    }
    
    if ($curlError) {
        echo "   cURL Error: {$curlError}\n";
    }
    
    return false;
}

// Test with the email that works in research campaign
echo "📧 Test Email Details:\n";
echo "   To: tinokuhn@gmail.com (same as research campaign)\n";
echo "   From: Therapair Team <onboarding@resend.dev>\n";
echo "   (Matching research campaign format)\n\n";

$testHtml = '<h2>Test Email from Form Submission System</h2><p>This is a test to verify Resend works the same way as the research campaign.</p>';
$testSubject = '🧪 Test: Form Submission Email System';

$result = sendEmailViaResend(
    'tinokuhn@gmail.com',
    $testSubject,
    $testHtml,
    'onboarding@resend.dev',
    'Therapair Team',
    'contact@therapair.com.au'
);

echo "\n" . str_repeat("=", 48) . "\n";
echo "Result: " . ($result ? "✅ SUCCESS" : "❌ FAILED") . "\n";

if ($result) {
    echo "\n✅ Email sent successfully!\n";
    echo "   - Should appear in Resend dashboard\n";
    echo "   - Check inbox: tinokuhn@gmail.com\n";
} else {
    echo "\n❌ Email failed to send\n";
    echo "   - Check error messages above\n";
    echo "   - This matches what happens in form submission\n";
}

?>



