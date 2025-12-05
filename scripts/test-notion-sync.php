<?php
/**
 * Test Notion Sync for EOI
 * Tests if EOI submissions can be saved to Notion
 */

$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST = [];

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../notion-sync.php';

echo "🧪 Testing Notion Sync for EOI\n";
echo str_repeat("=", 60) . "\n\n";

// Check configuration
echo "📋 Configuration Check:\n";
echo "   USE_NOTION_SYNC: " . (defined('USE_NOTION_SYNC') && USE_NOTION_SYNC ? '✅ true' : '❌ false/not defined') . "\n";
echo "   NOTION_TOKEN: " . (defined('NOTION_TOKEN') && !empty(NOTION_TOKEN) ? '✅ SET' : '❌ EMPTY') . "\n";
echo "   NOTION_DB_EOI: " . (defined('NOTION_DB_EOI') && !empty(NOTION_DB_EOI) ? '✅ ' . NOTION_DB_EOI : '❌ EMPTY') . "\n";
echo "   notion-sync.php exists: " . (file_exists(__DIR__ . '/../notion-sync.php') ? '✅' : '❌') . "\n\n";

if (!defined('USE_NOTION_SYNC') || !USE_NOTION_SYNC) {
    echo "❌ USE_NOTION_SYNC is not enabled in config.php\n";
    exit(1);
}

if (!defined('NOTION_TOKEN') || empty(NOTION_TOKEN)) {
    echo "❌ NOTION_TOKEN is not set in config.php\n";
    exit(1);
}

if (!defined('NOTION_DB_EOI') || empty(NOTION_DB_EOI)) {
    echo "❌ NOTION_DB_EOI is not set in config.php\n";
    exit(1);
}

// Test data for therapist EOI
$testData = [
    'audience' => 'therapist',
    'email' => 'test-eoi-' . time() . '@therapair.com.au',
    'full_name' => 'Test Therapist',
    'professional_title' => 'Psychologist',
    'organization' => 'Test Practice',
    'specializations' => 'Anxiety, Depression'
];

echo "📝 Test Data:\n";
echo "   Audience: " . $testData['audience'] . "\n";
echo "   Email: " . $testData['email'] . "\n";
echo "   Name: " . $testData['full_name'] . "\n\n";

// Build properties
echo "🔧 Building Notion Properties...\n";
$properties = buildNotionProperties($testData, $testData['audience']);
echo "   Properties count: " . count($properties) . "\n";
echo "   Property names: " . implode(', ', array_keys($properties)) . "\n\n";

// Test sync
echo "📤 Attempting to sync to Notion...\n";
$result = syncToNotion($testData, $testData['audience'], NOTION_DB_EOI);

echo "\n" . str_repeat("=", 60) . "\n";
if ($result['success']) {
    echo "✅ SUCCESS! Entry created in Notion\n";
    echo "   Page ID: " . ($result['page_id'] ?? 'unknown') . "\n";
    exit(0);
} else {
    echo "❌ FAILED to create entry\n";
    echo "   Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    if (isset($result['error_message'])) {
        echo "   Message: " . $result['error_message'] . "\n";
    }
    if (isset($result['error_code'])) {
        echo "   Code: " . $result['error_code'] . "\n";
    }
    if (isset($result['http_code'])) {
        echo "   HTTP Code: " . $result['http_code'] . "\n";
    }
    if (isset($result['response'])) {
        echo "   Response: " . substr($result['response'], 0, 500) . "\n";
    }
    exit(1);
}

