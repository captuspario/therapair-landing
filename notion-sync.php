<?php
/**
 * Notion Database Sync Handler
 * Sends form submissions to Notion database
 */

function syncToNotion($formData, $audience, $targetDatabaseId = null) {
    // Load configuration
    if (file_exists(__DIR__ . '/config.php')) {
        require_once __DIR__ . '/config.php';
        $notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
        // Use passed ID or fallback to default constant if defined
        $notionDatabaseId = $targetDatabaseId ?: (defined('NOTION_DATABASE_ID') ? NOTION_DATABASE_ID : (defined('NOTION_DB_EOI') ? NOTION_DB_EOI : ''));
    } else {
        error_log("Notion sync: Configuration file not found");
        return ['success' => false, 'error' => 'Configuration file not found'];
    }

    // Enhanced logging
    error_log("Notion sync: Token=" . (!empty($notionToken) ? 'SET' : 'EMPTY') . ", DB_ID=" . (!empty($notionDatabaseId) ? $notionDatabaseId : 'EMPTY') . ", Audience=" . $audience);

    if (empty($notionToken)) {
        error_log("Notion sync failed: NOTION_TOKEN is empty");
        return ['success' => false, 'error' => 'Notion token not configured'];
    }
    
    if (empty($notionDatabaseId)) {
        error_log("Notion sync failed: Database ID is empty. TargetDB=" . ($targetDatabaseId ?? 'null') . ", NOTION_DATABASE_ID=" . (defined('NOTION_DATABASE_ID') ? NOTION_DATABASE_ID : 'not defined') . ", NOTION_DB_EOI=" . (defined('NOTION_DB_EOI') ? NOTION_DB_EOI : 'not defined'));
        return ['success' => false, 'error' => 'Notion database ID not configured'];
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
    
    // Log the request payload for debugging (without sensitive data)
    error_log("Notion sync request payload: " . json_encode([
        'database_id' => $notionDatabaseId,
        'audience' => $audience,
        'properties_count' => count($properties),
        'property_names' => array_keys($properties)
    ]));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 201) {
        $responseData = json_decode($response, true);
        error_log("Notion sync: Success! HTTP $httpCode, Page ID: " . ($responseData['id'] ?? 'unknown'));
        return ['success' => true, 'response' => $responseData];
    } else {
        $responseData = json_decode($response, true);
        $errorMessage = isset($responseData['message']) ? $responseData['message'] : 'Unknown error';
        $errorCode = isset($responseData['code']) ? $responseData['code'] : 'unknown';
        
        error_log("Notion sync failed: HTTP $httpCode, Code: $errorCode, Message: $errorMessage");
        error_log("Notion sync response: " . substr($response, 0, 500));
        if ($curlError) {
            error_log("Notion sync cURL error: $curlError");
        }
        
        return [
            'success' => false,
            'error' => 'Notion API error',
            'http_code' => $httpCode,
            'error_code' => $errorCode,
            'error_message' => $errorMessage,
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
        'status' => ['name' => 'New']
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
            if (!empty($data['full_name'])) {
                $properties['Full Name'] = [
                    'rich_text' => [
                        ['text' => ['content' => $data['full_name']]]
                    ]
                ];
            }

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
            return !empty($data['full_name']) 
                ? $data['full_name'] 
                : 'Therapist Application';
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

