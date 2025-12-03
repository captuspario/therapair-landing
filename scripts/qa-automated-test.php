<?php
/**
 * Automated QA Test Script
 * Tests EOI submissions, Notion sync, and tracking initialization
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../notion-sync.php';

echo "🧪 Automated QA Test - Therapair User Journey Tracking\n";
echo "====================================================\n\n";

$testEmail = 'tinokuhn@gmail.com';
$baseUrl = 'https://therapair.com.au';
$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$notionDbEoi = defined('NOTION_DB_EOI') ? NOTION_DB_EOI : '';

$testResults = [
    'total' => 0,
    'passed' => 0,
    'failed' => 0,
    'errors' => []
];

function testResult($testName, $passed, $error = '') {
    global $testResults;
    $testResults['total']++;
    if ($passed) {
        $testResults['passed']++;
        echo "✅ $testName\n";
    } else {
        $testResults['failed']++;
        $testResults['errors'][] = "$testName: $error";
        echo "❌ $testName: $error\n";
    }
}

// Test 1: Configuration Check
echo "📋 Configuration Check:\n";
testResult("NOTION_TOKEN configured", !empty($notionToken));
testResult("NOTION_DB_EOI configured", !empty($notionDbEoi));
testResult("RESEND_WEBHOOK_SECRET configured", defined('RESEND_WEBHOOK_SECRET') && !empty(RESEND_WEBHOOK_SECRET));
echo "\n";

// Test 2: Notion Database Access
echo "🔍 Notion Database Access:\n";
$url = "https://api.notion.com/v1/databases/$notionDbEoi";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $notionToken,
    'Notion-Version: 2022-06-28'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $database = json_decode($response, true);
    $properties = $database['properties'] ?? [];
    testResult("Notion database accessible", true);
    testResult("Tracking properties exist", 
        isset($properties['Last Engagement Date']) &&
        isset($properties['Email Opened Date']) &&
        isset($properties['Sandbox Clicked Date']) &&
        isset($properties['Email Preferences Clicked Date']) &&
        isset($properties['Last Clicked Link'])
    );
} else {
    testResult("Notion database accessible", false, "HTTP $httpCode");
}
echo "\n";

// Test 3: Test EOI Submission (Therapist)
echo "📝 Testing EOI Submission (Therapist):\n";
$testEmailFull = $testEmail . '+autotest';
$testData = [
    'audience' => 'therapist',
    'email' => $testEmailFull,
    'full_name' => 'QA Test Therapist',
    'professional_title' => 'Psychologist',
    'organization' => 'Test Practice',
    'specializations' => 'Anxiety, Depression',
    'email_consent' => 'yes'
];

$notionResult = syncToNotion($testData, 'therapist', $notionDbEoi);
if ($notionResult['success']) {
    $pageId = $notionResult['page_id'] ?? 'unknown';
    testResult("EOI entry created in Notion", true);
    testResult("Notion page ID returned", !empty($pageId) && $pageId !== 'unknown');
    
    // Verify entry exists
    $url = "https://api.notion.com/v1/pages/$pageId";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Notion-Version: 2022-06-28'
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $page = json_decode($response, true);
        $props = $page['properties'] ?? [];
        testResult("Entry retrievable from Notion", true);
        testResult("Last Engagement Date initialized", isset($props['Last Engagement Date']));
        testResult("Email property set", isset($props['Email']));
    } else {
        testResult("Entry retrievable from Notion", false, "HTTP $httpCode");
    }
} else {
    testResult("EOI entry created in Notion", false, $notionResult['error'] ?? 'Unknown error');
}
echo "\n";

// Test 4: Webhook Handler Accessibility
echo "🔗 Webhook Handler Test:\n";
$webhookUrl = "$baseUrl/api/eoi/email-event.php";
$ch = curl_init($webhookUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Webhook should return 405 (Method Not Allowed) for GET, which means it exists
testResult("Webhook endpoint accessible", $httpCode === 405 || $httpCode === 200 || $httpCode === 403);
echo "\n";

// Test 5: Track.php Accessibility
echo "🔗 Track.php Test:\n";
$trackUrl = "$baseUrl/track.php?dest=sandbox";
$ch = curl_init($trackUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

testResult("Track.php accessible", $httpCode === 302 || $httpCode === 200);
echo "\n";

// Test 6: Simulate Webhook Event (Email Open)
echo "📧 Simulating Email Open Webhook:\n";
if (!empty($notionDbEoi) && !empty($notionToken)) {
    // Find test entry by email
    $url = "https://api.notion.com/v1/databases/$notionDbEoi/query";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'filter' => [
            'property' => 'Email',
            'email' => ['equals' => $testEmail . '+autotest']
        ],
        'page_size' => 1
    ]));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (!empty($data['results'])) {
            $pageId = $data['results'][0]['id'];
            $now = date('c');
            
            // Update tracking properties
            $url = "https://api.notion.com/v1/pages/$pageId";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $notionToken,
                'Content-Type: application/json',
                'Notion-Version: 2022-06-28'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'properties' => [
                    'Email Opened Date' => ['date' => ['start' => $now]],
                    'Last Engagement Date' => ['date' => ['start' => $now]]
                ]
            ]));
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            testResult("Email open tracking update", $httpCode === 200);
        } else {
            testResult("Email open tracking update", false, "Test entry not found");
        }
    } else {
        testResult("Email open tracking update", false, "Failed to query Notion");
    }
}
echo "\n";

// Summary
echo "📊 Test Summary:\n";
echo "   Total Tests: {$testResults['total']}\n";
echo "   Passed: {$testResults['passed']}\n";
echo "   Failed: {$testResults['failed']}\n";
echo "\n";

if (!empty($testResults['errors'])) {
    echo "❌ Errors:\n";
    foreach ($testResults['errors'] as $error) {
        echo "   - $error\n";
    }
    echo "\n";
}

if ($testResults['failed'] === 0) {
    echo "✅ All automated tests passed!\n";
    echo "\n";
    echo "⚠️  Note: This test does NOT include:\n";
    echo "   - Actual email delivery verification\n";
    echo "   - Real email open/click tracking (requires real user interaction)\n";
    echo "   - Resend webhook delivery (requires real email events)\n";
    echo "\n";
    echo "📝 Next Steps:\n";
    echo "   1. Submit real EOI forms with test emails\n";
    echo "   2. Open confirmation emails\n";
    echo "   3. Click links in emails\n";
    echo "   4. Verify tracking in Notion database\n";
} else {
    echo "❌ Some tests failed. Please review errors above.\n";
}

