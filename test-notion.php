<?php
/**
 * Test Notion API Connection
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/notion-sync.php';

echo "=== Notion API Test ===\n\n";

// Check config
echo "1. Config Check:\n";
echo "   NOTION_TOKEN: " . (defined('NOTION_TOKEN') ? 'SET' : 'NOT SET') . "\n";
echo "   NOTION_DATABASE_ID: " . (defined('NOTION_DATABASE_ID') ? NOTION_DATABASE_ID : 'NOT SET') . "\n";
echo "   USE_NOTION_SYNC: " . (defined('USE_NOTION_SYNC') && USE_NOTION_SYNC ? 'TRUE' : 'FALSE') . "\n\n";

// Test data
$testData = [
    'email' => 'test.debug@therapair.com.au',
    'therapy_interests' => 'LGBTQ+ affirming care, Trauma-informed care',
    'additional_thoughts' => 'This is a test submission to debug Notion integration.'
];

echo "2. Test Form Data:\n";
print_r($testData);
echo "\n";

// Test sync
echo "3. Attempting Notion Sync...\n";
$result = syncToNotion($testData, 'individual');

echo "4. Result:\n";
print_r($result);
echo "\n";

if ($result['success']) {
    echo "✅ SUCCESS! Entry created in Notion.\n";
} else {
    echo "❌ FAILED! Check error details above.\n";
    if (isset($result['response'])) {
        echo "\nAPI Response:\n";
        echo $result['response'] . "\n";
    }
}

?>

