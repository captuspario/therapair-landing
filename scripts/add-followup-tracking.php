<?php
/**
 * Add Follow-up Tracking Properties to VIC Therapist DB
 * Adds count + date fields for follow-up status tracking
 */

require_once __DIR__ . '/../config.php';

echo "\n📧 Adding Follow-up Tracking Properties\n";
echo str_repeat('=', 60) . "\n\n";

$databaseId = NOTION_DB_USER_TESTING;
$token = NOTION_TOKEN;

$properties = [
    [
        'name' => 'Research Follow-up Count',
        'type' => 'number',
        'description' => 'Number of follow-up emails sent',
    ],
    [
        'name' => 'Research Last Follow-up Date',
        'type' => 'date',
        'description' => 'Date of last follow-up email sent',
    ],
    [
        'name' => 'Research Follow-up Status',
        'type' => 'select',
        'description' => 'Current follow-up status',
        'options' => [
            'No Follow-up',
            'Follow-up 1 Sent',
            'Follow-up 2 Sent',
            'Follow-up 3 Sent',
            'Follow-up Complete',
            'No More Follow-ups',
        ],
    ],
    [
        'name' => 'Profile Creation Source',
        'type' => 'select',
        'description' => 'How this profile was created',
        'options' => [
            'VIC Import',
            'EOI Submission',
            'Manual',
            'Research Participant',
        ],
    ],
];

function add_property(string $databaseId, array $property): bool
{
    global $token;
    $url = "https://api.notion.com/v1/databases/$databaseId";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28',
    ]);
    
    $propertyConfig = [];
    
    switch ($property['type']) {
        case 'number':
            $propertyConfig = ['number' => []];
            break;
        case 'date':
            $propertyConfig = ['date' => []];
            break;
        case 'select':
            $propertyConfig = [
                'select' => [
                    'options' => array_map(function($opt) {
                        return ['name' => $opt];
                    }, $property['options']),
                ],
            ];
            break;
    }
    
    $payload = [
        'properties' => [
            $property['name'] => $propertyConfig,
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

echo "📊 VIC Therapist DB\n";
echo "   Database ID: $databaseId\n\n";

foreach ($properties as $property) {
    echo "   ➕ Adding: {$property['name']} ({$property['type']})...\n";
    if (add_property($databaseId, $property)) {
        echo "      ✅ Added successfully\n";
    } else {
        echo "      ❌ Failed\n";
    }
}

echo "\n✅ Follow-up tracking properties added!\n\n";

