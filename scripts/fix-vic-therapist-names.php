<?php
/**
 * Fix VIC Therapist Database Name Properties
 * 
 * Goal:
 * - "First Name" (title property) = first name only
 * - "Fullname" = full name (first + last)
 * - "Last Name" = last name only
 * 
 * Currently, most "First Name" entries have the full name, which is wrong.
 * This script will parse and fix all entries.
 */

require_once __DIR__ . '/../config.php';

$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$vicDbId = defined('NOTION_DB_USER_TESTING') ? NOTION_DB_USER_TESTING : '28c5c25944da80a48d85fd43119f4ec1';

if (empty($notionToken)) {
    die("❌ Error: NOTION_TOKEN not configured\n");
}

echo "🔍 Reviewing VIC Therapist Database\n";
echo "=" . str_repeat("=", 60) . "\n";
echo "Database ID: $vicDbId\n\n";

// Fetch all therapists
$url = "https://api.notion.com/v1/databases/$vicDbId/query";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $notionToken,
    'Content-Type: application/json',
    'Notion-Version: 2022-06-28'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'page_size' => 100
]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $error = json_decode($response, true);
    die("❌ Error fetching database: " . ($error['message'] ?? "HTTP $httpCode") . "\n");
}

$data = json_decode($response, true);
$therapists = $data['results'] ?? [];

echo "📊 Found " . count($therapists) . " therapist entries\n\n";

// Analyze current state
$needsFixing = [];
$correct = [];
$issues = [];

foreach ($therapists as $therapist) {
    $pageId = $therapist['id'];
    $props = $therapist['properties'] ?? [];
    
    // Get current values
    $firstName = '';
    $fullname = '';
    $lastName = '';
    
    // First Name (title property)
    if (isset($props['First Name']['title']) && !empty($props['First Name']['title'])) {
        $firstName = $props['First Name']['title'][0]['text']['content'] ?? '';
    }
    
    // Fullname (rich_text)
    if (isset($props['Fullname']['rich_text']) && !empty($props['Fullname']['rich_text'])) {
        $fullname = $props['Fullname']['rich_text'][0]['text']['content'] ?? '';
    }
    
    // Last Name (rich_text)
    if (isset($props['Last Name']['rich_text']) && !empty($props['Last Name']['rich_text'])) {
        $lastName = $props['Last Name']['rich_text'][0]['text']['content'] ?? '';
    }
    
    // Check if First Name contains full name (has space and multiple words)
    $firstNameWords = explode(' ', trim($firstName));
    $hasMultipleWords = count($firstNameWords) > 1;
    
    if ($hasMultipleWords) {
        // First Name has full name - needs fixing
        $needsFixing[] = [
            'page_id' => $pageId,
            'current_first_name' => $firstName,
            'current_fullname' => $fullname,
            'current_last_name' => $lastName,
        ];
    } elseif (!empty($firstName) && !empty($lastName)) {
        // Already correct
        $correct[] = $pageId;
    } else {
        // Missing data
        $issues[] = [
            'page_id' => $pageId,
            'first_name' => $firstName,
            'fullname' => $fullname,
            'last_name' => $lastName,
        ];
    }
}

echo "📈 Analysis:\n";
echo "   ✅ Correct: " . count($correct) . " entries\n";
echo "   ⚠️  Needs fixing: " . count($needsFixing) . " entries\n";
echo "   ❓ Issues/Missing: " . count($issues) . " entries\n\n";

if (count($needsFixing) === 0) {
    echo "✅ All entries are already correct!\n";
    exit(0);
}

echo "🔧 Fixing " . count($needsFixing) . " entries...\n\n";

$fixed = 0;
$errors = 0;

foreach ($needsFixing as $entry) {
    $pageId = $entry['page_id'];
    $currentFirstName = $entry['current_first_name'];
    
    // Parse the name
    $nameParts = explode(' ', trim($currentFirstName));
    $newFirstName = $nameParts[0] ?? '';
    $newLastName = trim(implode(' ', array_slice($nameParts, 1)));
    
    // Build full name: first + last
    // Start with the original full name from First Name field (which currently has the full name)
    $newFullname = trim($currentFirstName);
    
    // If we already have a fullname stored, prefer it (might have middle names, etc.)
    if (!empty($entry['current_fullname']) && $entry['current_fullname'] !== $currentFirstName) {
        $newFullname = $entry['current_fullname'];
        // Try to extract last name from fullname if we don't have it
        if (empty($newLastName)) {
            $fullnameParts = explode(' ', trim($newFullname));
            if (count($fullnameParts) > 1) {
                $newLastName = trim(implode(' ', array_slice($fullnameParts, 1)));
            }
        }
    }
    
    // If we have a stored last name, use it to build fullname
    if (!empty($entry['current_last_name']) && empty($newLastName)) {
        $newLastName = $entry['current_last_name'];
    }
    
    // Ensure fullname is first + last (reconstruct if we have both)
    if (!empty($newFirstName) && !empty($newLastName)) {
        $newFullname = trim($newFirstName . ' ' . $newLastName);
    } elseif (empty($newFullname)) {
        // Fallback to just first name if nothing else
        $newFullname = $newFirstName;
    }
    
    // Build update payload
    $updatePayload = [
        'properties' => [
            'First Name' => [
                'title' => [
                    ['text' => ['content' => $newFirstName]]
                ]
            ],
            'Fullname' => [
                'rich_text' => [
                    ['text' => ['content' => $newFullname]]
                ]
            ],
        ]
    ];
    
    // Add Last Name if we have it
    if (!empty($newLastName)) {
        $updatePayload['properties']['Last Name'] = [
            'rich_text' => [
                ['text' => ['content' => $newLastName]]
            ]
        ];
    }
    
    // Update in Notion
    $updateUrl = "https://api.notion.com/v1/pages/$pageId";
    $ch = curl_init($updateUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updatePayload));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $fixed++;
        echo "  ✅ Fixed: \"$currentFirstName\" → First: \"$newFirstName\", Last: \"$newLastName\", Full: \"$newFullname\"\n";
    } else {
        $errors++;
        $errorData = json_decode($response, true);
        $errorMsg = $errorData['message'] ?? "HTTP $httpCode";
        echo "  ❌ Error updating $pageId: $errorMsg\n";
    }
    
    // Rate limiting - Notion allows 3 requests per second
    usleep(350000); // ~350ms delay
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 Results:\n";
echo "   ✅ Fixed: $fixed entries\n";
if ($errors > 0) {
    echo "   ❌ Errors: $errors entries\n";
}
echo "\n✅ Name property fix complete!\n";

