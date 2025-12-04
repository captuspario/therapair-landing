<?php
/**
 * Add Relation Fields to All Databases
 * Creates relation properties to link databases together
 */

require_once __DIR__ . '/../config.php';

echo "\n🔗 Adding Relation Fields to All Databases\n";
echo str_repeat('=', 60) . "\n\n";

$databases = [
    'VIC Therapist DB' => [
        'id' => NOTION_DB_USER_TESTING,
        'relations' => [
            [
                'name' => 'Related Survey Response',
                'description' => 'Links to Research DB survey responses',
                'database_id' => NOTION_DB_SURVEY,
            ],
            [
                'name' => 'Related Feedback',
                'description' => 'Links to Feedback DB entries',
                'database_id' => NOTION_DB_SANDBOX,
            ],
            [
                'name' => 'Original EOI Entry',
                'description' => 'Links to EOI DB if profile created from EOI',
                'database_id' => NOTION_DB_EOI,
            ],
        ],
    ],
    'Research DB' => [
        'id' => NOTION_DB_SURVEY,
        'relations' => [
            [
                'name' => 'Related Therapist',
                'description' => 'Links to VIC Therapist DB',
                'database_id' => NOTION_DB_USER_TESTING,
            ],
        ],
    ],
    'Feedback DB' => [
        'id' => NOTION_DB_SANDBOX,
        'relations' => [
            [
                'name' => 'Related Therapist',
                'description' => 'Links to VIC Therapist DB when email found',
                'database_id' => NOTION_DB_USER_TESTING,
            ],
            [
                'name' => 'Related Survey Response',
                'description' => 'Links to Research DB if survey token present',
                'database_id' => NOTION_DB_SURVEY,
            ],
        ],
    ],
    'EOI DB' => [
        'id' => NOTION_DB_EOI,
        'relations' => [
            [
                'name' => 'Related Therapist Profile',
                'description' => 'Links to VIC Therapist DB when profile created',
                'database_id' => NOTION_DB_USER_TESTING,
            ],
        ],
    ],
];

function add_relation_property(string $databaseId, array $relation): bool
{
    $token = NOTION_TOKEN;
    $url = "https://api.notion.com/v1/databases/$databaseId";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28',
    ]);
    
    $payload = [
        'properties' => [
            $relation['name'] => [
                'relation' => [
                    'database_id' => $relation['database_id'],
                ],
            ],
        ],
    ];
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($statusCode === 200) {
        return true;
    }
    
    $error = json_decode($response, true);
    $message = $error['message'] ?? 'Unknown error';
    
    // If property already exists, that's okay
    if (strpos($message, 'already exists') !== false || strpos($message, 'duplicate') !== false) {
        return true;
    }
    
    echo "      ⚠️  Error: $message\n";
    return false;
}

foreach ($databases as $dbName => $config) {
    echo "📊 $dbName\n";
    echo "   Database ID: {$config['id']}\n";
    
    foreach ($config['relations'] as $relation) {
        echo "   ➕ Adding relation: {$relation['name']}...\n";
        if (add_relation_property($config['id'], $relation)) {
            echo "      ✅ Added successfully\n";
        } else {
            echo "      ❌ Failed\n";
        }
    }
    echo "\n";
}

echo "✅ Relation fields added!\n\n";
echo "💡 Next steps:\n";
echo "   1. Verify properties in Notion\n";
echo "   2. Run backfill script to link existing entries\n";
echo "   3. Update code to set relations on new entries\n\n";

