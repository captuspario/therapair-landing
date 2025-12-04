<?php
/**
 * Global Feedback API Endpoint
 * Saves feedback to "User feedback" Notion database
 * Links to therapist directory if email/tracking ID found
 */

// Log all requests for debugging
error_log('[user-feedback] Request received: ' . $_SERVER['REQUEST_METHOD'] . ' from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
error_log('[user-feedback] Request headers: ' . json_encode(getallheaders()));

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['ok' => true]);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('[user-feedback] Invalid method: ' . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

error_log('[user-feedback] POST request received, processing...');

// Load config
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/directory-helpers.php';

// Get JSON payload
$input = file_get_contents('php://input');
$inputLength = strlen($input);
error_log('[user-feedback] Raw input received (length: ' . $inputLength . '): ' . substr($input, 0, 500));

if (empty($input)) {
    error_log('[user-feedback] ERROR: Empty input received');
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Empty request body']);
    exit;
}

$data = json_decode($input, true);

if (!$data) {
    $jsonError = json_last_error_msg();
    $jsonErrorCode = json_last_error();
    error_log('[user-feedback] JSON decode error: ' . $jsonError . ' (code: ' . $jsonErrorCode . ')');
    error_log('[user-feedback] Input that failed: ' . substr($input, 0, 200));
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON: ' . $jsonError]);
    exit;
}

error_log('[user-feedback] Data received: ' . json_encode([
    'rating' => $data['rating'] ?? null,
    'has_comment' => !empty($data['comment']),
    'has_tags' => !empty($data['tags']),
    'section' => $data['section'] ?? null,
    'page_url' => $data['page_url'] ?? null
]));

// Validate required fields
if (!isset($data['rating']) || !is_numeric($data['rating'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Rating is required']);
    exit;
}

$rating = (int) $data['rating'];
if ($rating < 1 || $rating > 6) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Rating must be between 1 and 6']);
    exit;
}

// Try to find therapist by email from UTM parameters or session
$therapistEmail = null;
$directoryPageId = null;

// Check for email in UTM parameters or session data
if (!empty($data['page_url'])) {
    $urlParts = parse_url($data['page_url']);
    if (!empty($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
        // Try to extract email from token if available
        if (!empty($queryParams['token'])) {
            // Token contains therapist info - we'd need to decode it
            // For now, we'll save feedback to the user research feedback database
        }
    }
}

// Get database IDs
$directoryDatabaseId = (string) config_value('THERAPIST_DIRECTORY_DATABASE_ID', '');
$userFeedbackDbId = (string) config_value('NOTION_DB_SANDBOX', ''); // User feedback database (renamed from Sandbox Feedback)

// If we have a directory database, try to find therapist by email
if ($directoryDatabaseId && !empty($data['therapist_email'])) {
    $therapistEmail = filter_var($data['therapist_email'], FILTER_VALIDATE_EMAIL);
    if ($therapistEmail) {
        $directoryPageId = find_directory_page_by_email($therapistEmail);
    }
}

// Prepare feedback text with all context
$feedbackParts = [];

// Rating
$feedbackParts[] = sprintf('Rating: %d/6', $rating);

// Comment
if (!empty($data['comment'])) {
    $feedbackParts[] = "\nComment: " . trim($data['comment']);
}

// Tags
if (!empty($data['tags']) && is_array($data['tags'])) {
    $feedbackParts[] = "\nTags: " . implode(', ', $data['tags']);
}

// Page/Part tracking
$pagePartInfo = [];
if (!empty($data['current_step'])) {
    $pagePartInfo[] = 'Step: ' . $data['current_step'];
    if (!empty($data['total_steps'])) {
        $pagePartInfo[] = 'of ' . $data['total_steps'];
    }
}
if (!empty($data['question_id'])) {
    $pagePartInfo[] = 'Question ID: ' . $data['question_id'];
}
if (!empty($data['question_text'])) {
    $pagePartInfo[] = 'Question: ' . substr($data['question_text'], 0, 100);
}
if (!empty($data['section'])) {
    $pagePartInfo[] = 'Section: ' . $data['section'];
}
if (!empty($data['scroll_percent'])) {
    $pagePartInfo[] = 'Scroll: ' . $data['scroll_percent'] . '%';
}

if (!empty($pagePartInfo)) {
    $feedbackParts[] = "\n\nFeedback Location: " . implode(' | ', $pagePartInfo);
}

// Page context
if (!empty($data['page_url'])) {
    $feedbackParts[] = "\nPage URL: " . $data['page_url'];
}
if (!empty($data['page_title'])) {
    $feedbackParts[] = "\nPage Title: " . $data['page_title'];
}

$feedbackText = implode('', $feedbackParts);

// Determine source based on page section (match sandbox endpoint logic exactly)
// Use 'source' from data if available, otherwise derive from section, default to 'Sandbox'
$sourceName = !empty($data['source']) ? ucfirst($data['source']) : 'Sandbox';
if (empty($data['source']) && !empty($data['section'])) {
    $section = strtolower($data['section']);
    if ($section === 'sandbox') {
        $sourceName = 'Sandbox';
    } else if ($section === 'survey') {
        $sourceName = 'Survey';
    } else {
        // For homepage and other pages, default to 'Sandbox' (matches working sandbox endpoint)
        $sourceName = 'Sandbox';
    }
}

// Save to User Feedback database (NOTION_DB_SANDBOX - renamed to "User feedback")
if ($userFeedbackDbId) {
    try {
        $properties = [];
        
        // Title - Include page/section info
        $titleParts = [sprintf('Feedback - %d⭐', $rating)];
        if (!empty($data['section'])) {
            $titleParts[] = ucfirst($data['section']);
        }
        if (!empty($data['page_path'])) {
            $pathParts = explode('/', trim($data['page_path'], '/'));
            $pageName = end($pathParts) ?: 'home';
            $titleParts[] = $pageName;
        }
        $titleParts[] = date('Y-m-d H:i:s');
        
        $properties['Name'] = [
            'title' => [
                [
                    'text' => [
                        'content' => implode(' - ', $titleParts)
                    ]
                ]
            ]
        ];
        
        // Rating - Try to include, but will fail gracefully if property doesn't exist
        $properties['Rating'] = [
            'number' => $rating
        ];
        
        // Feedback (rich text) - Include all context
        // This is the main property - should exist in most databases
        $properties['Feedback'] = [
            'rich_text' => [
                [
                    'text' => [
                        'content' => $feedbackText
                    ]
                ]
            ]
        ];
        
        // Submission Date
        $createdAt = !empty($data['created_at']) ? $data['created_at'] : date('c');
        $properties['Submission Date'] = [
            'date' => [
                'start' => $createdAt
            ]
        ];
        
        // Submission Status (default to "New") - Try to set, but make optional
        // Commented out - uncomment if your database has this property
        // $properties['Submission Status'] = [
        //     'select' => [
        //         'name' => 'New'
        //     ]
        // ];
        
        // Audience Type - Match sandbox endpoint exactly
        $properties['Audience Type'] = [
            'select' => [
                'name' => $sourceName
            ]
        ];
        
        // Page URL - Only add if property exists in Notion database
        // Note: This property may not exist in all Notion databases
        // Page URL is already included in the Feedback rich_text field above
        // Uncomment below if "Page URL" property exists in your Notion database:
        // if (!empty($data['page_url'])) {
        //     $properties['Page URL'] = [
        //         'url' => $data['page_url']
        //     ];
        // }
        
        // Tracking ID and Session ID - These properties don't exist in the Notion database
        // Include tracking info in the Feedback text instead
        // if (!empty($data['tracking_id'])) {
        //     $properties['Tracking ID'] = [
        //         'rich_text' => [
        //             [
        //                 'text' => [
        //                     'content' => $data['tracking_id']
        //                 ]
        //             ]
        //         ]
        //     ];
        // }
        // 
        // if (!empty($data['session_id'])) {
        //     $properties['Session ID'] = [
        //         'rich_text' => [
        //             [
        //                 'text' => [
        //                     'content' => $data['session_id']
        //                 ]
        //             ]
        //         ]
        //     ];
        // }
        
        // Add tracking info to Feedback text if available
        if (!empty($data['tracking_id']) || !empty($data['session_id'])) {
            $trackingInfo = [];
            if (!empty($data['tracking_id'])) {
                $trackingInfo[] = 'Tracking ID: ' . $data['tracking_id'];
            }
            if (!empty($data['session_id'])) {
                $trackingInfo[] = 'Session ID: ' . $data['session_id'];
            }
            if (!empty($trackingInfo)) {
                $properties['Feedback']['rich_text'][0]['text']['content'] .= "\n\n" . implode("\n", $trackingInfo);
            }
        }
        
        // Filter out empty properties before sending to Notion
        $filteredProperties = array_filter($properties, function($value) {
            // Remove empty properties
            if (is_array($value)) {
                if (isset($value['rich_text']) && empty($value['rich_text'])) return false;
                if (isset($value['multi_select']) && empty($value['multi_select'])) return false;
                if (isset($value['url']) && empty($value['url'])) return false;
                if (isset($value['select']) && empty($value['select']['name'])) return false;
            }
            return true;
        });
        
        error_log('[user-feedback] Attempting to save with properties: ' . json_encode(array_keys($filteredProperties)));
        
        // Log to file for easier debugging
        $logFile = __DIR__ . '/../../feedback-debug.log';
        $logEntry = date('Y-m-d H:i:s') . " - Properties: " . json_encode(array_keys($filteredProperties)) . "\n";
        $logEntry .= date('Y-m-d H:i:s') . " - Full payload: " . json_encode([
            'parent' => ['database_id' => $userFeedbackDbId],
            'properties' => $filteredProperties
        ], JSON_PRETTY_PRINT) . "\n\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        // Create page in User Feedback database
        $feedbackRecord = notion_request('POST', 'https://api.notion.com/v1/pages', [
            'parent' => [
                'database_id' => $userFeedbackDbId
            ],
            'properties' => $filteredProperties
        ]);
        
        $feedbackRecordId = $feedbackRecord['id'] ?? null;
        error_log('[user-feedback] Successfully saved to User Feedback database: ' . ($feedbackRecordId ?? 'no ID'));
        
        // Set relation to VIC Therapist DB if we found the therapist (link when available)
        if ($feedbackRecordId && $directoryPageId) {
            try {
                // Update Feedback DB entry with relation to therapist
                notion_request('PATCH', 'https://api.notion.com/v1/pages/' . $feedbackRecordId, [
                    'properties' => [
                        'Related Therapist' => [
                            'relation' => [
                                ['id' => $directoryPageId],
                            ],
                        ],
                    ],
                ]);
                
                // Update VIC Therapist DB entry with relation to feedback
                patch_directory_page($directoryPageId, [
                    'Related Feedback' => [
                        'relation' => [
                            ['id' => $feedbackRecordId],
                        ],
                    ],
                ]);
            } catch (Exception $e) {
                // Silently fail - relations are optional, feedback can be anonymous
                error_log('[user-feedback] Failed to set relation: ' . $e->getMessage());
            }
        }
        
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        $errorDetails = [
            'message' => $errorMessage,
            'properties_attempted' => array_keys($properties),
            'database_id' => $userFeedbackDbId,
            'data_received' => [
                'rating' => $rating,
                'section' => $data['section'] ?? null,
                'page_url' => $data['page_url'] ?? null,
                'page_path' => $data['page_path'] ?? null
            ]
        ];
        
        // Log to both error_log and file
        error_log('[user-feedback] Failed to save to User Feedback DB: ' . $errorMessage);
        error_log('[user-feedback] Error details: ' . json_encode($errorDetails, JSON_PRETTY_PRINT));
        
        $logFile = __DIR__ . '/../../feedback-debug.log';
        $logEntry = date('Y-m-d H:i:s') . " - ERROR: " . $errorMessage . "\n";
        $logEntry .= date('Y-m-d H:i:s') . " - Error details: " . json_encode($errorDetails, JSON_PRETTY_PRINT) . "\n\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
        
        // Extract the actual Notion error message
        $displayError = 'Failed to save feedback to database';
        if (strpos($errorMessage, 'Notion API error') !== false) {
            $displayError = str_replace('Notion API error: ', '', $errorMessage);
        }
        
        // Return error to client with more details for debugging
        http_response_code(500);
        $response = [
            'ok' => false,
            'error' => $displayError,
            'message' => $displayError
        ];
        
        // Include full error in debug mode
        if (strpos($errorMessage, 'Notion API error') !== false) {
            $response['debug'] = [
                'notion_error' => $errorMessage,
                'properties_attempted' => array_keys($properties)
            ];
        }
        
        echo json_encode($response);
        exit;
    }
} else {
    // No database ID configured
    error_log('[user-feedback] No User Feedback database ID configured');
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Feedback database not configured'
    ]);
    exit;
}

// Also update VIC Therapists directory if we found the therapist
if ($directoryPageId) {
    try {
        $sandboxFeedbackProperty = (string) config_value('NOTION_DIRECTORY_SANDBOX_FEEDBACK_PROPERTY', 'Sandbox Feedback');
        
        // Get existing feedback or create new
        $existingPage = notion_request('GET', 'https://api.notion.com/v1/pages/' . $directoryPageId);
        $existingFeedback = '';
        
        if (isset($existingPage['properties'][$sandboxFeedbackProperty])) {
            $prop = $existingPage['properties'][$sandboxFeedbackProperty];
            if ($prop['type'] === 'rich_text' && !empty($prop['rich_text'])) {
                $existingFeedback = implode('', array_map(function($text) {
                    return $text['plain_text'] ?? '';
                }, $prop['rich_text']));
            }
        }
        
        // Append new feedback with tracking ID if available
        $trackingInfo = '';
        if (!empty($data['tracking_id'])) {
            $trackingInfo = "\nTracking ID: " . $data['tracking_id'];
        }
        if (!empty($data['session_id'])) {
            $trackingInfo .= "\nSession ID: " . $data['session_id'];
        }
        
        $newFeedbackEntry = "\n\n---\n" . date('Y-m-d H:i:s') . " - User Feedback (" . $sourceName . ")\n" . $feedbackText . $trackingInfo;
        $updatedFeedback = ($existingFeedback ? $existingFeedback : '') . $newFeedbackEntry;
        
        $properties = [];
        $properties[$sandboxFeedbackProperty] = [
            'rich_text' => [
                [
                    'text' => [
                        'content' => $updatedFeedback
                    ]
                ]
            ]
        ];
        
        patch_directory_page($directoryPageId, $properties);
        
    } catch (Exception $e) {
        error_log('[survey-feedback] Failed to update directory: ' . $e->getMessage());
        // Don't fail the request if directory update fails
    }
}

// Return success
echo json_encode([
    'ok' => true,
    'message' => 'Feedback saved successfully'
]);

