<?php
/**
 * Track Sandbox Visit
 * Updates therapist directory when user visits sandbox demo
 */

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/directory-helpers.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    echo json_encode(['ok' => true]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || empty($data['email'])) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Email is required']);
        exit;
    }
    
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => 'Invalid email address']);
        exit;
    }
    
    // Find therapist in directory
    $directoryPageId = find_directory_page_by_email($email);
    
    if (!$directoryPageId) {
        // Therapist not found - this is OK, they might not be in directory yet
        error_log('[sandbox-visit] Therapist not found in directory: ' . $email);
        echo json_encode([
            'ok' => true,
            'message' => 'Visit logged (therapist not in directory)',
            'directory_updated' => false
        ]);
        exit;
    }
    
    // Update therapist directory with sandbox visit
    $properties = [];
    
    // Update "Latest Sandbox Visit" date if property exists
    $properties['Latest Sandbox Visit'] = [
        'date' => [
            'start' => date('c')
        ]
    ];
    
    // Update "Research Status" if property exists
    // Try to get current status first
    try {
        $existingPage = notion_request('GET', 'https://api.notion.com/v1/pages/' . $directoryPageId);
        $currentStatus = $existingPage['properties']['Research Status']['select']['name'] ?? null;
        
        // Set status based on current state
        if ($currentStatus === 'Survey Completed' || $currentStatus === 'Feedback Submitted') {
            $newStatus = 'Sandbox Visited';
        } else {
            $newStatus = 'Sandbox Visited';
        }
        
        $properties['Research Status'] = [
            'select' => [
                'name' => $newStatus
            ]
        ];
    } catch (Exception $e) {
        // Property might not exist, skip it
        error_log('[sandbox-visit] Could not update Research Status: ' . $e->getMessage());
    }
    
    // Update "Research Source Notes" with visit info
    try {
        $existingPage = notion_request('GET', 'https://api.notion.com/v1/pages/' . $directoryPageId);
        $existingNotes = '';
        
        if (isset($existingPage['properties']['Research Source Notes'])) {
            $prop = $existingPage['properties']['Research Source Notes'];
            if ($prop['type'] === 'rich_text' && !empty($prop['rich_text'])) {
                $existingNotes = implode('', array_map(function($text) {
                    return $text['plain_text'] ?? '';
                }, $prop['rich_text']));
            }
        }
        
        $visitInfo = [];
        $visitInfo[] = "\n--- Sandbox Visit ---";
        $visitInfo[] = "Date: " . date('Y-m-d H:i:s');
        if (!empty($data['session_id'])) {
            $visitInfo[] = "Session ID: " . $data['session_id'];
        }
        if (!empty($data['page_url'])) {
            $visitInfo[] = "Page: " . $data['page_url'];
        }
        
        $newNotes = ($existingNotes ? $existingNotes . "\n" : '') . implode("\n", $visitInfo);
        
        $properties['Research Source Notes'] = [
            'rich_text' => [
                [
                    'text' => [
                        'content' => $newNotes
                    ]
                ]
            ]
        ];
    } catch (Exception $e) {
        // Property might not exist, skip it
        error_log('[sandbox-visit] Could not update Research Source Notes: ' . $e->getMessage());
    }
    
    // Update directory page
    if (!empty($properties)) {
        patch_directory_page($directoryPageId, $properties);
        error_log('[sandbox-visit] Updated therapist directory for: ' . $email);
        
        echo json_encode([
            'ok' => true,
            'message' => 'Sandbox visit tracked',
            'directory_updated' => true,
            'therapist_id' => $directoryPageId
        ]);
    } else {
        echo json_encode([
            'ok' => true,
            'message' => 'Visit logged (no properties to update)',
            'directory_updated' => false
        ]);
    }
    
} catch (Exception $e) {
    error_log('[sandbox-visit] Error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'error' => 'Failed to track visit',
        'message' => $e->getMessage()
    ]);
}

