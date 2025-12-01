<?php
/**
 * Check Resend Email Status
 * Shows if emails are being sent via Resend or falling back to PHP mail()
 */

require_once __DIR__ . '/config.php';

echo "🔍 Checking Resend Email Status\n";
echo "================================\n\n";

// Get config values
$RESEND_API_KEY = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
$USE_RESEND = defined('USE_RESEND') ? USE_RESEND : false;
$ADMIN_EMAIL = defined('ADMIN_EMAIL') ? ADMIN_EMAIL : 'contact@therapair.com.au';

echo "1️⃣ Configuration:\n";
echo "   RESEND_API_KEY: " . (empty($RESEND_API_KEY) ? '❌ NOT SET' : '✅ SET') . "\n";
echo "   USE_RESEND: " . ($USE_RESEND ? '✅ YES' : '❌ NO') . "\n";
echo "\n";

if (empty($RESEND_API_KEY) || !$USE_RESEND) {
    echo "⚠️  Resend is not enabled or API key is missing!\n";
    echo "   Emails are likely falling back to PHP mail() (unreliable)\n";
    exit(1);
}

// Check recent Resend API activity by testing connection
echo "2️⃣ Testing Resend API Connection:\n";

$testPayload = [
    'from' => 'Therapair Team <onboarding@resend.dev>',
    'to' => $ADMIN_EMAIL,  // This will fail if not verified, but shows API is working
    'subject' => '🧪 Resend API Test',
    'html' => '<p>This is a test to verify Resend API connection.</p>'
];

$ch = curl_init('https://api.resend.com/emails');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($testPayload),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $RESEND_API_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Code: {$httpCode}\n";

if ($httpCode === 200 || $httpCode === 201) {
    $data = json_decode($response, true);
    if (isset($data['id'])) {
        echo "   ✅ API is working! Message ID: {$data['id']}\n";
        echo "   ✅ Check Resend dashboard: https://resend.com/emails\n";
    }
} else {
    $errorData = json_decode($response, true);
    $errorMsg = isset($errorData['message']) ? $errorData['message'] : 'Unknown error';
    echo "   ⚠️  API Response: {$errorMsg}\n";
    
    if (strpos($errorMsg, 'testing emails') !== false) {
        echo "\n   📋 NOTE: Account is in testing mode\n";
        echo "   - Can only send to verified test email\n";
        echo "   - Emails to other addresses won't appear in Resend dashboard\n";
        echo "   - They'll fall back to PHP mail() (unreliable)\n";
    }
}

echo "\n";

// Check server error logs for Resend activity
echo "3️⃣ Recent Resend Activity in Logs:\n";
echo "   Check server error logs for 'Resend' entries\n";
echo "   Look for lines containing:\n";
echo "   - 'Resend email sent successfully'\n";
echo "   - 'Resend email failed'\n";
echo "   - 'Resend API Request'\n";
echo "\n";

// Check if PHP mail() is being used instead
echo "4️⃣ Possible Issues:\n";
echo "   ⚠️  If Resend fails, emails fall back to PHP mail()\n";
echo "   ⚠️  PHP mail() emails won't appear in Resend dashboard\n";
echo "   ⚠️  PHP mail() is unreliable and often fails silently\n";
echo "\n";

echo "5️⃣ How to Check What's Happening:\n";
echo "   a) Check Resend dashboard: https://resend.com/emails\n";
echo "   b) Check server error logs for 'Resend' entries\n";
echo "   c) Test form submission and watch logs in real-time\n";
echo "\n";

echo "6️⃣ To Fix:\n";
echo "   - Verify domain in Resend: https://resend.com/domains\n";
echo "   - Check error logs: tail -f /var/log/php-errors.log\n";
echo "   - Submit form and check logs immediately\n";
echo "\n";

?>

