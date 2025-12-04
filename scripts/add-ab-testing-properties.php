<?php
/**
 * Add A/B Testing Properties to VIC Therapist Database
 * 
 * Adds all required properties for tracking A/B test performance
 */

require_once __DIR__ . '/../config.php';

$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$vicDbId = defined('NOTION_DB_USER_TESTING') ? NOTION_DB_USER_TESTING : '28c5c25944da80a48d85fd43119f4ec1';

if (empty($notionToken)) {
    die("❌ Error: NOTION_TOKEN not configured\n");
}

echo "🔧 Adding A/B Testing Properties to VIC Therapist Database\n";
echo "=" . str_repeat("=", 60) . "\n";
echo "Database ID: $vicDbId\n\n";

// Properties to add
$properties = [
    // A/B Test Assignment
    'Research Variant' => [
        'type' => 'select',
        'select' => [
            'options' => [
                ['name' => 'A', 'color' => 'blue'],
                ['name' => 'B', 'color' => 'green'],
            ]
        ]
    ],
    'Research Experiment ID' => [
        'type' => 'rich_text',
    ],
    
    // Invite Tracking
    'Research Invite Sent' => [
        'type' => 'checkbox',
    ],
    'Research Invite Sent Date' => [
        'type' => 'date',
    ],
    
    // Engagement Tracking
    'Research Email Opened' => [
        'type' => 'checkbox',
    ],
    'Research Email Opened Date' => [
        'type' => 'date',
    ],
    'Research Email Opens Count' => [
        'type' => 'number',
    ],
    
    'Research Survey Clicked' => [
        'type' => 'checkbox',
    ],
    'Research Survey Clicked Date' => [
        'type' => 'date',
    ],
    'Research Survey Clicks Count' => [
        'type' => 'number',
    ],
    
    'Research Survey Completed' => [
        'type' => 'checkbox',
    ],
    'Research Survey Completed Date' => [
        'type' => 'date',
    ],
    
    'Research Sandbox Clicked' => [
        'type' => 'checkbox',
    ],
    'Research Sandbox Clicked Date' => [
        'type' => 'date',
    ],
    
    // Status
    'Research Status' => [
        'type' => 'select',
        'select' => [
            'options' => [
                ['name' => 'Not Invited', 'color' => 'gray'],
                ['name' => 'Invited', 'color' => 'yellow'],
                ['name' => 'Opened', 'color' => 'orange'],
                ['name' => 'Clicked Survey', 'color' => 'blue'],
                ['name' => 'Completed Survey', 'color' => 'green'],
                ['name' => 'Clicked Sandbox', 'color' => 'purple'],
            ]
        ]
    ],
    
    // Notes
    'Research Notes' => [
        'type' => 'rich_text',
    ],
];

$url = "https://api.notion.com/v1/databases/$vicDbId";

// Get current database schema
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $notionToken,
    'Notion-Version: 2022-06-28'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $error = json_decode($response, true);
    die("❌ Error fetching database: " . ($error['message'] ?? "HTTP $httpCode") . "\n");
}

$db = json_decode($response, true);
$existingProperties = $db['properties'] ?? [];

echo "📋 Current properties: " . count($existingProperties) . "\n";
echo "📋 Properties to add: " . count($properties) . "\n\n";

$added = 0;
$skipped = 0;
$errors = 0;

foreach ($properties as $propName => $propConfig) {
    // Check if property already exists
    if (isset($existingProperties[$propName])) {
        echo "   ⏭️  Skipped: \"$propName\" (already exists)\n";
        $skipped++;
        continue;
    }
    
    // Build property payload
    $propertyPayload = [];
    
    switch ($propConfig['type']) {
        case 'select':
            $propertyPayload = [
                $propName => [
                    'select' => $propConfig['select']
                ]
            ];
            break;
            
        case 'checkbox':
            $propertyPayload = [
                $propName => [
                    'checkbox' => []
                ]
            ];
            break;
            
        case 'date':
            $propertyPayload = [
                $propName => [
                    'date' => []
                ]
            ];
            break;
            
        case 'number':
            $propertyPayload = [
                $propName => [
                    'number' => []
                ]
            ];
            break;
            
        case 'rich_text':
            $propertyPayload = [
                $propName => [
                    'rich_text' => []
                ]
            ];
            break;
    }
    
    // Update database
    $updateUrl = "https://api.notion.com/v1/databases/$vicDbId";
    $ch = curl_init($updateUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'properties' => $propertyPayload
    ]));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        echo "   ✅ Added: \"$propName\" ({$propConfig['type']})\n";
        $added++;
    } else {
        $error = json_decode($response, true);
        $errorMsg = $error['message'] ?? "HTTP $httpCode";
        echo "   ❌ Error adding \"$propName\": $errorMsg\n";
        $errors++;
    }
    
    // Rate limiting
    usleep(350000); // ~350ms delay
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 Results:\n";
echo "   ✅ Added: $added properties\n";
echo "   ⏭️  Skipped: $skipped properties (already exist)\n";
if ($errors > 0) {
    echo "   ❌ Errors: $errors properties\n";
}
echo "\n✅ A/B testing properties setup complete!\n";
echo "\n💡 Next: Create dashboard views in Notion (see AB-TEST-DASHBOARD-SETUP.md)\n";

