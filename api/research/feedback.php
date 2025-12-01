<?php
/**
 * Global Feedback API Endpoint
 * Saves feedback to "User feedback" Notion database
 * Links to therapist directory if email/tracking ID found
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['ok' => true]);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

// Load config
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/directory-helpers.php';

// Get JSON payload
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
    exit;
}

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

// Determine source based on page section
$sourceName = 'Website';
if (!empty($data['section'])) {
    $section = strtolower($data['section']);
    if ($section === 'sandbox') {
        $sourceName = 'Sandbox';
    } else if ($section === 'survey') {
        $sourceName = 'Survey';
    } else if ($section === 'documentation') {
        $sourceName = 'Documentation';
    } else if ($section === 'legal') {
        $sourceName = 'Legal';
    } else if ($section === 'home') {
        $sourceName = 'Home';
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
        
        // Rating
        $properties['Rating'] = [
            'number' => $rating
        ];
        
        // Feedback (rich text) - Include all context
        $properties['Feedback'] = [
            'rich_text' => [
                [
                    'text' => [
                        'content' => $feedbackText
                    ]
                ]
            ]
        ];
        
        // Audience Type - Use source name
        $properties['Audience Type'] = [
            'select' => [
                'name' => $sourceName
            ]
        ];
        
        // Submission Date
        $createdAt = !empty($data['created_at']) ? $data['created_at'] : date('c');
        $properties['Submission Date'] = [
            'date' => [
                'start' => $createdAt
            ]
        ];
        
        // Submission Status
        $properties['Submission Status'] = [
            'select' => [
                'name' => 'New'
            ]
        ];
        
        // Page URL
        if (!empty($data['page_url'])) {
            $properties['Page URL'] = [
                'url' => $data['page_url']
            ];
        }
        
        // Tracking ID (if available) - for linking multiple feedback entries from same user
        if (!empty($data['tracking_id'])) {
            $properties['Tracking ID'] = [
                'rich_text' => [
                    [
                        'text' => [
                            'content' => $data['tracking_id']
                        ]
                    ]
                ]
            ];
        }
        
        // Session ID (if available)
        if (!empty($data['session_id'])) {
            $properties['Session ID'] = [
                'rich_text' => [
                    [
                        'text' => [
                            'content' => $data['session_id']
                        ]
                    ]
                ]
            ];
        }
        
        // Create page in User Feedback database
        $feedbackRecord = notion_request('POST', 'https://api.notion.com/v1/pages', [
            'parent' => [
                'database_id' => $userFeedbackDbId
            ],
            'properties' => array_filter($properties, function($value) {
                // Remove empty properties
                if (is_array($value)) {
                    if (isset($value['rich_text']) && empty($value['rich_text'])) return false;
                    if (isset($value['multi_select']) && empty($value['multi_select'])) return false;
                    if (isset($value['url']) && empty($value['url'])) return false;
                }
                return true;
            })
        ]);
        
        error_log('[user-feedback] Successfully saved to User Feedback database: ' . ($feedbackRecord['id'] ?? 'no ID'));
        
    } catch (Exception $e) {
        error_log('[user-feedback] Failed to save to User Feedback DB: ' . $e->getMessage());
        // Continue to try saving to directory if available
    }
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

