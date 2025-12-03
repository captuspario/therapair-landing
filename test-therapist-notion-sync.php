<?php
/**
 * Test Therapist Notion Sync
 * Tests if therapist EOI entries can be saved to Notion database
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/notion-sync.php';

// Test configuration
echo "=== Therapist Notion Sync Test ===\n\n";

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

// Test with therapist data
echo "2. Testing Notion sync with therapist data...\n";
$testFormData = [
    'email' => 'test-therapist@example.com',
    'full_name' => 'Dr. Test Therapist',
    'professional_title' => 'Clinical Psychologist',
    'organization' => 'Test Clinic',
    'specializations' => 'Anxiety, Depression, Trauma'
];

$testAudience = 'therapist';

echo "   Audience: $testAudience\n";
echo "   Email: {$testFormData['email']}\n";
echo "   Full Name: {$testFormData['full_name']}\n";
echo "   Professional Title: {$testFormData['professional_title']}\n";
echo "   Organisation: {$testFormData['organization']}\n";
echo "   Specialisations: {$testFormData['specializations']}\n";
echo "   Database ID: $notionDbEoi\n\n";

echo "3. Attempting to sync...\n";
$result = syncToNotion($testFormData, $testAudience, $notionDbEoi);

if ($result['success']) {
    $pageId = isset($result['response']['id']) ? $result['response']['id'] : 'unknown';
    echo "   ✅ SUCCESS! Therapist entry created in Notion database.\n";
    echo "   Page ID: $pageId\n";
    echo "\n   You can view it at: https://notion.so/$pageId\n";
    echo "\n   Next steps:\n";
    echo "   1. Check your Notion database (ID: $notionDbEoi) for the test entry\n";
    echo "   2. Verify all therapist properties are correctly populated\n";
    echo "   3. Delete the test entry if needed\n";
    echo "   4. Submit a real therapist form to verify production sync works\n";
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
    
    if (isset($result['properties_sent']) && !empty($result['properties_sent'])) {
        echo "\n   Properties sent:\n";
        foreach ($result['properties_sent'] as $prop) {
            echo "     - $prop\n";
        }
    }
    
    echo "\n   Troubleshooting:\n";
    echo "   1. Check that the database is shared with your Notion integration\n";
    echo "   2. Verify the database ID is correct: $notionDbEoi\n";
    echo "   3. Check that all therapist properties exist in the Notion database:\n";
    echo "      - Full Name (rich_text)\n";
    echo "      - Professional Title (rich_text)\n";
    echo "      - Organisation (rich_text) - note: British spelling\n";
    echo "      - Specialisations (rich_text) - note: British spelling\n";
    echo "      - Verification Status (select) - must have 'Pending' option\n";
    echo "      - Onboarding Stage (select) - must have 'Interest' option\n";
    echo "   4. Verify property names match exactly (case-sensitive)\n";
    echo "   5. Check that select options match exactly\n";
    echo "   6. Check server error logs for more details\n";
    
    if (isset($result['response'])) {
        echo "\n   Full response:\n";
        echo "   " . substr($result['response'], 0, 1000) . "\n";
    }
    
    exit(1);
}

echo "\n=== Test Complete ===\n";

