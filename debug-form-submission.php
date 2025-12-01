<?php
/**
 * Debug Form Submission and Email Issues
 * Usage: php debug-form-submission.php
 */

require_once __DIR__ . '/config.php';

echo "🔍 Therapair Form Submission Debug\n";
echo "==================================\n\n";

// Check configuration
echo "1️⃣ Configuration Check:\n";
echo "   ADMIN_EMAIL: " . (defined('ADMIN_EMAIL') ? ADMIN_EMAIL : '❌ NOT DEFINED') . "\n";
echo "   FROM_EMAIL: " . (defined('FROM_EMAIL') ? FROM_EMAIL : '❌ NOT DEFINED') . "\n";
echo "   RESEND_API_KEY: " . (defined('RESEND_API_KEY') && !empty(RESEND_API_KEY) ? '✅ SET (' . substr(RESEND_API_KEY, 0, 10) . '...)' : '❌ NOT SET') . "\n";
echo "   USE_RESEND: " . (defined('USE_RESEND') && USE_RESEND ? '✅ YES' : '❌ NO') . "\n";
echo "\n";

// Check if cURL is available
echo "2️⃣ cURL Check:\n";
if (function_exists('curl_init')) {
    echo "   ✅ cURL is available\n";
    
    // Test cURL with Resend API
    $testApiKey = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
    if (!empty($testApiKey)) {
        echo "   Testing Resend API connection...\n";
        
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode([
                'from' => 'Therapair <onboarding@resend.dev>',
                'to' => defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@therapair.com.au',
                'subject' => '🧪 Test Email from Debug Script',
                'html' => '<h1>Test</h1><p>This is a test email from the debug script.</p>'
            ]),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $testApiKey,
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        echo "   HTTP Code: {$httpCode}\n";
        if ($curlError) {
            echo "   cURL Error: {$curlError}\n";
        }
        
        if ($httpCode === 200 || $httpCode === 201) {
            $data = json_decode($response, true);
            if (isset($data['id'])) {
                echo "   ✅ Test email sent successfully! ID: {$data['id']}\n";
            } else {
                echo "   ⚠️  API returned success but no message ID\n";
                echo "   Response: " . substr($response, 0, 200) . "\n";
            }
        } else {
            echo "   ❌ API request failed\n";
            echo "   Response: " . substr($response, 0, 300) . "\n";
            
            $errorData = json_decode($response, true);
            if (isset($errorData['message'])) {
                echo "   Error Message: {$errorData['message']}\n";
            }
        }
    } else {
        echo "   ⚠️  Cannot test: RESEND_API_KEY not set\n";
    }
} else {
    echo "   ❌ cURL is NOT available\n";
}
echo "\n";

// Check PHP mail() function
echo "3️⃣ PHP mail() Check:\n";
if (function_exists('mail')) {
    echo "   ✅ PHP mail() function is available\n";
    echo "   ⚠️  Note: mail() is unreliable and often blocked\n";
} else {
    echo "   ❌ PHP mail() function is NOT available\n";
}
echo "\n";

// Check error logs location
echo "4️⃣ Error Logging:\n";
$errorLog = ini_get('error_log');
echo "   PHP error_log setting: " . ($errorLog ? $errorLog : 'default') . "\n";
echo "   Current error reporting: " . error_reporting() . "\n";
echo "\n";

// Check form submission handler
echo "5️⃣ Form Handler Check:\n";
$formHandler = __DIR__ . '/submit-form.php';
if (file_exists($formHandler)) {
    echo "   ✅ submit-form.php exists\n";
    
    // Check if sendEmailViaResend function exists
    $content = file_get_contents($formHandler);
    if (strpos($content, 'function sendEmailViaResend') !== false) {
        echo "   ✅ sendEmailViaResend function found\n";
    } else {
        echo "   ❌ sendEmailViaResend function NOT found\n";
    }
} else {
    echo "   ❌ submit-form.php NOT found\n";
}
echo "\n";

echo "==================================\n";
echo "📋 Recommendations:\n";
echo "1. Check server error logs for detailed error messages\n";
echo "2. Verify RESEND_API_KEY is correct in config.php\n";
echo "3. Test Resend API directly using the test script\n";
echo "4. Check Resend dashboard: https://resend.com/emails\n";
echo "\n";

?>

