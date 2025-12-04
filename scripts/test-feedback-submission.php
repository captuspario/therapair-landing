<?php
/**
 * Test Feedback Submission Endpoint
 * Simulates a feedback submission to test the API
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../api/research/bootstrap.php';

echo "\n🧪 Testing Feedback Submission Endpoint\n";
echo str_repeat('=', 60) . "\n\n";

// Test the notion_request function directly
echo "📋 Testing Notion API Connection...\n";
try {
    $testResponse = notion_request('GET', 'https://api.notion.com/v1/users/me', null);
    echo "  ✅ Notion API token is valid\n";
    echo "     User: " . ($testResponse['name'] ?? $testResponse['id'] ?? 'Unknown') . "\n\n";
} catch (Exception $e) {
    echo "  ❌ Notion API token error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test database access
echo "📊 Testing Feedback Database Access...\n";
$feedbackDbId = NOTION_DB_SANDBOX;
echo "   Database ID: $feedbackDbId\n";

try {
    $dbResponse = notion_request('GET', "https://api.notion.com/v1/databases/$feedbackDbId", null);
    $title = $dbResponse['title'][0]['plain_text'] ?? 'Untitled';
    echo "  ✅ Database accessible\n";
    echo "     Title: $title\n\n";
} catch (Exception $e) {
    echo "  ❌ Database access error: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test creating a feedback entry
echo "📝 Testing Feedback Entry Creation...\n";
$testProperties = [
    'Name' => [
        'title' => [
            [
                'text' => [
                    'content' => 'Test Feedback - ' . date('Y-m-d H:i:s')
                ]
            ]
        ]
    ],
    'Rating' => [
        'number' => 5
    ],
    'Feedback' => [
        'rich_text' => [
            [
                'text' => [
                    'content' => 'This is a test feedback entry created by the test script.'
                ]
            ]
        ]
    ],
    'Submission Date' => [
        'date' => [
            'start' => date('c')
        ]
    ],
    'Audience Type' => [
        'select' => [
            'name' => 'Sandbox'
        ]
    ],
];

try {
    $createResponse = notion_request('POST', 'https://api.notion.com/v1/pages', [
        'parent' => [
            'database_id' => $feedbackDbId
        ],
        'properties' => $testProperties
    ]);
    
    echo "  ✅ Feedback entry created successfully!\n";
    echo "     Page ID: " . ($createResponse['id'] ?? 'N/A') . "\n";
    echo "     URL: https://notion.so/" . str_replace('-', '', $createResponse['id'] ?? '') . "\n\n";
    
    // Clean up - delete the test entry
    echo "🧹 Cleaning up test entry...\n";
    try {
        notion_request('PATCH', 'https://api.notion.com/v1/pages/' . $createResponse['id'], [
            'archived' => true
        ]);
        echo "  ✅ Test entry archived\n\n";
    } catch (Exception $e) {
        echo "  ⚠️  Could not archive test entry: " . $e->getMessage() . "\n";
        echo "     You may need to delete it manually: " . $createResponse['id'] . "\n\n";
    }
    
} catch (Exception $e) {
    echo "  ❌ Failed to create feedback entry\n";
    echo "     Error: " . $e->getMessage() . "\n\n";
    
    // Check if it's a property error
    if (strpos($e->getMessage(), 'property') !== false) {
        echo "💡 This might be a property name mismatch.\n";
        echo "   Check that all property names match your Notion database schema.\n\n";
    }
    
    exit(1);
}

echo "✅ All feedback tests passed!\n\n";

