<?php
/**
 * Test All Database Integrations
 * Tests Notion API connections for all databases
 */

require_once __DIR__ . '/../config.php';

$databases = [
    'VIC Therapist DB' => NOTION_DB_USER_TESTING,
    'Research DB' => NOTION_DB_SURVEY,
    'EOI DB' => NOTION_DB_EOI,
    'Feedback DB' => NOTION_DB_SANDBOX,
];

echo "\n🔍 Testing All Notion Database Integrations\n";
echo str_repeat('=', 60) . "\n\n";

$allValid = true;

// Test Notion API token first
echo "📋 Testing Notion API Token...\n";
$ch = curl_init('https://api.notion.com/v1/users/me');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . NOTION_TOKEN,
    'Notion-Version: 2022-06-28',
]);
$response = curl_exec($ch);
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($statusCode === 200) {
    $data = json_decode($response, true);
    echo "  ✅ Notion API token is valid\n";
    echo "     User: " . ($data['name'] ?? $data['id'] ?? 'Unknown') . "\n\n";
} else {
    $error = json_decode($response, true);
    echo "  ❌ Notion API token is invalid\n";
    echo "     Error: " . ($error['message'] ?? 'Unknown error') . "\n";
    echo "     Status: $statusCode\n\n";
    $allValid = false;
}

// Test each database
foreach ($databases as $name => $dbId) {
    echo "📊 Testing $name...\n";
    echo "   Database ID: $dbId\n";
    
    $ch = curl_init("https://api.notion.com/v1/databases/$dbId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . NOTION_TOKEN,
        'Notion-Version: 2022-06-28',
    ]);
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($statusCode === 200) {
        $data = json_decode($response, true);
        $title = $data['title'][0]['plain_text'] ?? 'Untitled';
        echo "   ✅ Accessible\n";
        echo "   📄 Title: $title\n\n";
    } else {
        $error = json_decode($response, true);
        echo "   ❌ Access denied or invalid\n";
        echo "   Error: " . ($error['message'] ?? 'Unknown error') . "\n";
        echo "   Status: $statusCode\n\n";
        $allValid = false;
    }
}

echo str_repeat('=', 60) . "\n";
if ($allValid) {
    echo "✅ All database integrations are working!\n\n";
    exit(0);
} else {
    echo "❌ Some database integrations failed.\n";
    echo "   Please check the errors above and verify:\n";
    echo "   1. Notion token is correct in config.php\n";
    echo "   2. Integration has access to all databases\n";
    echo "   3. Database IDs are correct\n\n";
    exit(1);
}


