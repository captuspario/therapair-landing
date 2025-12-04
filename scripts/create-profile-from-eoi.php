<?php
/**
 * Create Therapist Profile from EOI Entry
 * Converts an EOI submission to a VIC Therapist profile
 * 
 * Usage: php create-profile-from-eoi.php <EOI_PAGE_ID> [admin_name]
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../api/research/bootstrap.php';
require_once __DIR__ . '/../api/research/directory-helpers.php';

if ($argc < 2) {
    echo "Usage: php create-profile-from-eoi.php <EOI_PAGE_ID> [admin_name]\n";
    echo "Example: php create-profile-from-eoi.php abc123def456 \"John Admin\"\n";
    exit(1);
}

$eoiPageId = $argv[1];
$adminName = $argv[2] ?? 'System';

echo "\n🔄 Creating Therapist Profile from EOI Entry\n";
echo str_repeat('=', 60) . "\n\n";

// Step 1: Fetch EOI entry
echo "📋 Step 1: Fetching EOI entry...\n";
try {
    $eoiEntry = notion_request('GET', "https://api.notion.com/v1/pages/$eoiPageId");
    echo "   ✅ EOI entry found\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 2: Extract data from EOI entry
echo "\n📊 Step 2: Extracting data from EOI entry...\n";
$eoiProps = $eoiEntry['properties'] ?? [];

// Helper to extract property value
function get_prop_value($prop, $type = 'text') {
    if (!isset($prop[$type])) return null;
    
    switch ($type) {
        case 'title':
            return $prop['title'][0]['plain_text'] ?? null;
        case 'rich_text':
            return $prop['rich_text'][0]['plain_text'] ?? null;
        case 'email':
            return $prop['email'] ?? null;
        case 'select':
            return $prop['select']['name'] ?? null;
        case 'multi_select':
            return array_map(fn($opt) => $opt['name'], $prop['multi_select'] ?? []);
        default:
            return null;
    }
}

// Map EOI properties to VIC Therapist properties
$mapping = [
    // Identity & Contact
    'Email Address' => get_prop_value($eoiProps['Email'] ?? [], 'email'),
    'First Name' => get_prop_value($eoiProps['Name'] ?? [], 'title'), // May need parsing
    'Fullname' => get_prop_value($eoiProps['Name'] ?? [], 'title'),
    
    // Professional (if Audience Type is Therapist)
    'Profession' => get_prop_value($eoiProps['Professional Title'] ?? [], 'rich_text'),
    'Organisation' => get_prop_value($eoiProps['Organisation'] ?? [], 'rich_text'),
    
    // Specializations
    'Specialisations' => get_prop_value($eoiProps['Specialisations'] ?? [], 'rich_text'),
];

// Check if this is a therapist EOI
$audienceType = get_prop_value($eoiProps['Audience Type'] ?? [], 'select');
if ($audienceType !== 'Therapist') {
    echo "   ⚠️  Warning: EOI is for '$audienceType', not 'Therapist'\n";
    echo "   This script is designed for therapist EOI entries.\n";
    echo "   Continue anyway? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) !== 'y') {
        exit(0);
    }
}

// Step 3: Check if profile already exists
echo "\n🔍 Step 3: Checking for existing profile...\n";
$email = $mapping['Email Address'];
if ($email) {
    $existingPageId = find_directory_page_by_email($email);
    if ($existingPageId) {
        echo "   ⚠️  Profile already exists for this email!\n";
        echo "   Page ID: $existingPageId\n";
        echo "   Update existing profile? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (trim($line) === 'y') {
            $targetPageId = $existingPageId;
            echo "   ✅ Will update existing profile\n";
        } else {
            echo "   ❌ Aborted\n";
            exit(0);
        }
    } else {
        $targetPageId = null;
        echo "   ✅ No existing profile found\n";
    }
} else {
    $targetPageId = null;
    echo "   ⚠️  No email found in EOI entry\n";
}

// Step 4: Create or update profile
echo "\n📝 Step 4: Creating/updating therapist profile...\n";

$vicProperties = [];

// Title (required)
if (!empty($mapping['Fullname'])) {
    $vicProperties['First Name'] = [
        'title' => [
            ['text' => ['content' => $mapping['Fullname']]]
        ]
    ];
}

// Email
if (!empty($mapping['Email Address'])) {
    $vicProperties['Email Address'] = ['email' => $mapping['Email Address']];
}

// Professional info
if (!empty($mapping['Profession'])) {
    $vicProperties['Profession'] = ['select' => ['name' => $mapping['Profession']]];
}

if (!empty($mapping['Organisation'])) {
    $vicProperties['Fullname'] = [
        'rich_text' => [
            ['text' => ['content' => $mapping['Organisation']]]
        ]
    ];
}

if (!empty($mapping['Specialisations'])) {
    $vicProperties['Specialisations'] = [
        'rich_text' => [
            ['text' => ['content' => $mapping['Specialisations']]]
        ]
    ];
}

// Profile creation metadata
$vicProperties['Profile Creation Source'] = ['select' => ['name' => 'EOI Submission']];
$vicProperties['Original EOI Entry'] = [
    'relation' => [
        ['id' => $eoiPageId]
    ]
];

try {
    if ($targetPageId) {
        // Update existing profile
        notion_request('PATCH', "https://api.notion.com/v1/pages/$targetPageId", [
            'properties' => $vicProperties
        ]);
        echo "   ✅ Profile updated successfully\n";
        $newProfileId = $targetPageId;
    } else {
        // Create new profile
        $newProfile = notion_request('POST', 'https://api.notion.com/v1/pages', [
            'parent' => ['database_id' => NOTION_DB_USER_TESTING],
            'properties' => $vicProperties
        ]);
        $newProfileId = $newProfile['id'];
        echo "   ✅ Profile created successfully\n";
        echo "   Page ID: $newProfileId\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error creating profile: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 5: Update EOI entry with relation and status
echo "\n🔗 Step 5: Updating EOI entry with relation...\n";
try {
    notion_request('PATCH', "https://api.notion.com/v1/pages/$eoiPageId", [
        'properties' => [
            'Profile Creation Status' => ['select' => ['name' => 'Created']],
            'Profile Creation Date' => ['date' => ['start' => date('c')]],
            'Profile Created By' => [
                'rich_text' => [
                    ['text' => ['content' => $adminName]]
                ]
            ],
            'Related Therapist Profile' => [
                'relation' => [
                    ['id' => $newProfileId]
                ]
            ],
        ]
    ]);
    echo "   ✅ EOI entry updated successfully\n";
} catch (Exception $e) {
    echo "   ⚠️  Warning: Could not update EOI entry: " . $e->getMessage() . "\n";
}

echo "\n✅ Profile creation complete!\n\n";
echo "📋 Summary:\n";
echo "   EOI Entry: $eoiPageId\n";
echo "   Therapist Profile: $newProfileId\n";
echo "   Created by: $adminName\n";
echo "   Date: " . date('Y-m-d H:i:s') . "\n\n";

