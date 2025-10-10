<?php
/**
 * Notion Database Sync Handler
 * Sends form submissions to Notion database
 */

function syncToNotion($formData, $audience) {
    // Load configuration
    if (file_exists(__DIR__ . '/config.php')) {
        require_once __DIR__ . '/config.php';
        $notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
        $notionDatabaseId = defined('NOTION_DATABASE_ID') ? NOTION_DATABASE_ID : '';
    } else {
        return ['success' => false, 'error' => 'Configuration file not found'];
    }

    if (empty($notionToken) || empty($notionDatabaseId)) {
        return ['success' => false, 'error' => 'Notion credentials not configured'];
    }

    // Build Notion page properties based on audience type
    $properties = buildNotionProperties($formData, $audience);

    // Prepare Notion API request
    $notionData = [
        'parent' => ['database_id' => $notionDatabaseId],
        'properties' => $properties
    ];

    // Call Notion API
    $ch = curl_init('https://api.notion.com/v1/pages');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notionData));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        return ['success' => true, 'response' => json_decode($response, true)];
    } else {
        return [
            'success' => false,
            'error' => 'Notion API error',
            'http_code' => $httpCode,
            'response' => $response
        ];
    }
}

function buildNotionProperties($data, $audience) {
    $properties = [];

    // Core properties (all entries)
    $properties['Name'] = [
        'title' => [
            ['text' => ['content' => generateNameForNotion($data, $audience)]]
        ]
    ];

    $properties['Email'] = [
        'email' => $data['email'] ?? ''
    ];

    $properties['Audience Type'] = [
        'select' => ['name' => ucfirst($audience)]
    ];

    $properties['Submission Date'] = [
        'date' => ['start' => date('c')]
    ];

    $properties['Status'] = [
        'select' => ['name' => 'New']
    ];

    // Email Preferences (default to all checked)
    $defaultPreferences = [
        'Product Updates',
        'Launch News',
        'Research & Feedback'
    ];
    
    // Add audience-specific preference
    if ($audience === 'therapist') {
        $defaultPreferences[] = 'Therapist Opportunities';
    } elseif ($audience === 'organization') {
        $defaultPreferences[] = 'Partnership News';
    } elseif ($audience === 'other') {
        $defaultPreferences[] = 'Investment Updates';
    }

    $properties['Email Preferences'] = [
        'multi_select' => array_map(function($pref) {
            return ['name' => $pref];
        }, $defaultPreferences)
    ];

    // Audience-specific properties
    switch ($audience) {
        case 'individual':
            if (!empty($data['therapy_interests'])) {
                $interests = explode(', ', $data['therapy_interests']);
                $properties['Therapy Interests'] = [
                    'multi_select' => array_map(function($interest) {
                        return ['name' => $interest];
                    }, $interests)
                ];
            }

            if (!empty($data['additional_thoughts'])) {
                $properties['Additional Thoughts'] = [
                    'rich_text' => [
                        ['text' => ['content' => substr($data['additional_thoughts'], 0, 2000)]]
                    ]
                ];
            }

            $properties['Interest Level'] = [
                'select' => ['name' => 'High']
            ];
            break;

        case 'therapist':
            if (!empty($data['professional_title'])) {
                $properties['Professional Title'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['professional_title']]]
                    ]
                ];
            }

            if (!empty($data['organization'])) {
                $properties['Organisation'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['organization']]]
                    ]
                ];
            }

            if (!empty($data['specializations'])) {
                $properties['Specialisations'] = [
                    'rich_text' => [
                        ['text' => ['content' => substr($data['specializations'], 0, 2000)]]
                    ]
                ];
            }

            $properties['Verification Status'] = [
                'select' => ['name' => 'Pending']
            ];

            $properties['Onboarding Stage'] = [
                'select' => ['name' => 'Interest']
            ];
            break;

        case 'organization':
            if (!empty($data['contact_name'])) {
                $properties['Contact Name'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['contact_name']]]
                    ]
                ];
            }

            if (!empty($data['position'])) {
                $properties['Position'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['position']]]
                    ]
                ];
            }

            if (!empty($data['organization_name'])) {
                $properties['Organisation Name'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['organization_name']]]
                    ]
                ];
            }

            if (!empty($data['partnership_interest'])) {
                $properties['Partnership Interest'] = [
                    'rich_text' => [
                        ['text' => ['content' => substr($data['partnership_interest'], 0, 2000)]]
                    ]
                ];
            }

            $properties['Partnership Type'] = [
                'select' => ['name' => 'Collaboration']
            ];
            break;

        case 'other':
            if (!empty($data['name'])) {
                $properties['Name'] = [
                    'title' => [
                        ['text' => ['content' => $data['name']]]
                    ]
                ];
            }

            if (!empty($data['support_interest'])) {
                $properties['Support Interest'] = [
                    'rich_text' => [
                        ['text' => ['content' => substr($data['support_interest'], 0, 2000)]]
                    ]
                ];
            }

            $properties['Support Type'] = [
                'select' => ['name' => 'Advocate']
            ];

            $properties['Engagement Level'] = [
                'select' => ['name' => 'High']
            ];
            break;
    }

    return $properties;
}

function generateNameForNotion($data, $audience) {
    switch ($audience) {
        case 'individual':
            return 'Individual Submission';
        case 'therapist':
            return 'Therapist Application';
        case 'organization':
            return !empty($data['organization_name']) 
                ? $data['organization_name'] 
                : 'Organisation Partnership';
        case 'other':
            return !empty($data['name']) 
                ? $data['name'] 
                : 'Supporter';
        default:
            return 'Form Submission';
    }
}

?>

