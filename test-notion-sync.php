<?php
/**
 * Test Notion Sync Connection
 * Tests if EOI entries can be saved to Notion database
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/notion-sync.php';

// Test configuration
echo "=== Notion Sync Test ===\n\n";

// Check configuration
echo "1. Checking configuration...\n";
$useNotionSync = defined('USE_NOTION_SYNC') ? USE_NOTION_SYNC : false;
$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$notionDbEoi = defined('NOTION_DB_EOI') ? NOTION_DB_EOI : '';

echo "   USE_NOTION_SYNC: " . ($useNotionSync ? "ENABLED" : "DISABLED") . "\n";
echo "   NOTION_TOKEN: " . (!empty($notionToken) ? "SET (" . substr($notionToken, 0, 10) . "...)" : "NOT SET") . "\n";
echo "   NOTION_DB_EOI: " . ($notionDbEoi ?: "NOT SET") . "\n\n";

if (!$useNotionSync) {
    echo "❌ USE_NOTION_SYNC is disabled. Enable it in config.php\n";
    exit(1);
}

if (empty($notionToken)) {
    echo "❌ NOTION_TOKEN is not set in config.php\n";
    exit(1);
}

if (empty($notionDbEoi)) {
    echo "❌ NOTION_DB_EOI is not set in config.php\n";
    exit(1);
}

// Test with sample data
echo "2. Testing Notion sync with sample data...\n";
$testFormData = [
    'email' => 'test@example.com',
    'name' => 'Test User',
    'therapy_interests' => 'Anxiety, Depression',
    'additional_thoughts' => 'This is a test entry from the diagnostic script.'
];

$testAudience = 'individual';

echo "   Audience: $testAudience\n";
echo "   Email: {$testFormData['email']}\n";
echo "   Database ID: $notionDbEoi\n\n";

echo "3. Attempting to sync...\n";
$result = syncToNotion($testFormData, $testAudience, $notionDbEoi);

if ($result['success']) {
    $pageId = isset($result['response']['id']) ? $result['response']['id'] : 'unknown';
    echo "   ✅ SUCCESS! Entry created in Notion database.\n";
    echo "   Page ID: $pageId\n";
    echo "\n   You can view it at: https://notion.so/$pageId\n";
    echo "\n   Next steps:\n";
    echo "   1. Check your Notion database (ID: $notionDbEoi) for the test entry\n";
    echo "   2. Delete the test entry if needed\n";
    echo "   3. Submit a real form to verify production sync works\n";
} else {
    echo "   ❌ FAILED!\n";
    echo "   Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    
    if (isset($result['http_code'])) {
        echo "   HTTP Code: " . $result['http_code'] . "\n";
    }
    
    if (isset($result['error_message'])) {
        echo "   Error Message: " . $result['error_message'] . "\n";
    }
    
    if (isset($result['error_code'])) {
        echo "   Error Code: " . $result['error_code'] . "\n";
    }
    
    echo "\n   Troubleshooting:\n";
    echo "   1. Check that the database is shared with your Notion integration\n";
    echo "   2. Verify the database ID is correct: $notionDbEoi\n";
    echo "   3. Check that the integration has access to the workspace\n";
    echo "   4. Verify the NOTION_TOKEN is valid and active\n";
    echo "   5. Check server error logs for more details\n";
    
    if (isset($result['response'])) {
        echo "\n   Full response:\n";
        echo "   " . substr($result['response'], 0, 500) . "\n";
    }
    
    exit(1);
}

echo "\n=== Test Complete ===\n";

