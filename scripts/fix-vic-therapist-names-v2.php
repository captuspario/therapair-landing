<?php
/**
 * Fix VIC Therapist Database Name Properties (Version 2)
 * 
 * Goal:
 * - "First Name" (title property) = first name only (including double names like "Anne Louise")
 * - "Fullname" = full name (first + last) - MUST include last name
 * - "Last Name" = last name only
 * 
 * Issues to fix:
 * 1. First Name contains full name (e.g., "Anne Louise Thorbecke" should be "Anne Louise")
 * 2. Fullname only contains first name, missing last name (should be "Anne Louise Thorbecke")
 */

require_once __DIR__ . '/../config.php';

$notionToken = defined('NOTION_TOKEN') ? NOTION_TOKEN : '';
$vicDbId = defined('NOTION_DB_USER_TESTING') ? NOTION_DB_USER_TESTING : '28c5c25944da80a48d85fd43119f4ec1';

if (empty($notionToken)) {
    die("❌ Error: NOTION_TOKEN not configured\n");
}

echo "🔍 Reviewing VIC Therapist Database (Version 2)\n";
echo "=" . str_repeat("=", 60) . "\n";
echo "Database ID: $vicDbId\n\n";

// Fetch all therapists (with pagination to get all)
$allTherapists = [];
$hasMore = true;
$startCursor = null;

while ($hasMore) {
    $url = "https://api.notion.com/v1/databases/$vicDbId/query";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $notionToken,
        'Content-Type: application/json',
        'Notion-Version: 2022-06-28'
    ]);
    
    $requestBody = ['page_size' => 100];
    if ($startCursor) {
        $requestBody['start_cursor'] = $startCursor;
    }
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        $error = json_decode($response, true);
        die("❌ Error fetching database: " . ($error['message'] ?? "HTTP $httpCode") . "\n");
    }

    $data = json_decode($response, true);
    $therapists = $data['results'] ?? [];
    $allTherapists = array_merge($allTherapists, $therapists);
    
    $hasMore = $data['has_more'] ?? false;
    $startCursor = $data['next_cursor'] ?? null;
}

echo "📊 Found " . count($allTherapists) . " therapist entries\n\n";

// Analyze current state
$needsFixing = [];
$correct = [];
$issues = [];

foreach ($allTherapists as $therapist) {
    $pageId = $therapist['id'];
    $props = $therapist['properties'] ?? [];
    
    // Get current values
    $firstName = '';
    $fullname = '';
    $lastName = '';
    
    // First Name (title property)
    if (isset($props['First Name']['title']) && !empty($props['First Name']['title'])) {
        $firstName = trim($props['First Name']['title'][0]['text']['content'] ?? '');
    }
    
    // Fullname (rich_text)
    if (isset($props['Fullname']['rich_text']) && !empty($props['Fullname']['rich_text'])) {
        $fullname = trim($props['Fullname']['rich_text'][0]['text']['content'] ?? '');
    }
    
    // Last Name (rich_text)
    if (isset($props['Last Name']['rich_text']) && !empty($props['Last Name']['rich_text'])) {
        $lastName = trim($props['Last Name']['rich_text'][0]['text']['content'] ?? '');
    }
    
    // Parse the current First Name to see if it contains last name
    $firstNameWords = explode(' ', trim($firstName));
    $hasMultipleWords = count($firstNameWords) > 1;
    
    // Check if First Name likely contains last name (heuristic: if it has 3+ words, likely includes last name)
    $likelyHasLastName = count($firstNameWords) >= 3;
    
    // Check if Fullname is missing last name
    $fullnameWords = explode(' ', trim($fullname));
    $fullnameMissingLastName = false;
    
    if (!empty($fullname) && !empty($lastName)) {
        // Check if fullname ends with last name
        $fullnameEndsWithLastName = strtolower(substr($fullname, -strlen($lastName))) === strtolower($lastName);
        if (!$fullnameEndsWithLastName) {
            $fullnameMissingLastName = true;
        }
    } elseif (!empty($fullname) && empty($lastName)) {
        // If we have fullname but no last name, check if fullname might contain last name
        // If fullname has 2+ words and matches firstName, it's likely missing last name
        if (count($fullnameWords) >= 2 && strtolower($fullname) === strtolower($firstName)) {
            $fullnameMissingLastName = true;
        }
    }
    
    // Determine what needs fixing
    $needsFix = false;
    $fixReason = [];
    
    // Check if first name ends with the same word as last name (e.g., "Anne Robertson" with last name "Robertson")
    if (!empty($firstName) && !empty($lastName)) {
        $firstNameWords = explode(' ', trim($firstName));
        $lastWordOfFirstName = end($firstNameWords);
        if (strtolower($lastWordOfFirstName) === strtolower($lastName)) {
            $needsFix = true;
            $fixReason[] = "First Name contains last name (ends with: $lastName)";
        }
    }
    
    if ($likelyHasLastName) {
        // First Name contains full name (3+ words = likely includes last name)
        $needsFix = true;
        $fixReason[] = "First Name contains full name (3+ words)";
    } elseif ($hasMultipleWords && !empty($lastName)) {
        // First Name has multiple words but we have a last name - check if first name matches fullname
        // This might be a double first name like "Anne Louise"
        $firstNameMatchesFullname = strtolower($firstName) === strtolower($fullname);
        if ($firstNameMatchesFullname && !empty($lastName)) {
            // First name matches fullname, but we have a last name - fullname should include last name
            $needsFix = true;
            $fixReason[] = "Fullname missing last name (should be: $firstName $lastName)";
        }
    }
    
    if ($fullnameMissingLastName && !empty($lastName)) {
        $needsFix = true;
        $fixReason[] = "Fullname missing last name";
    }
    
    if ($needsFix) {
        $needsFixing[] = [
            'page_id' => $pageId,
            'current_first_name' => $firstName,
            'current_fullname' => $fullname,
            'current_last_name' => $lastName,
            'reason' => implode(', ', $fixReason),
        ];
    } elseif (!empty($firstName) && !empty($lastName) && !empty($fullname)) {
        // Verify it's correct: fullname should be first + last
        $expectedFullname = trim($firstName . ' ' . $lastName);
        if (strtolower($fullname) === strtolower($expectedFullname)) {
            $correct[] = $pageId;
        } else {
            // Fullname doesn't match expected
            $needsFixing[] = [
                'page_id' => $pageId,
                'current_first_name' => $firstName,
                'current_fullname' => $fullname,
                'current_last_name' => $lastName,
                'reason' => "Fullname doesn't match expected (expected: $expectedFullname, got: $fullname)",
            ];
        }
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

if (count($needsFixing) > 0) {
    echo "🔍 Entries needing fixes:\n";
    foreach (array_slice($needsFixing, 0, 10) as $entry) {
        echo "   - {$entry['current_first_name']} | {$entry['current_fullname']} | {$entry['current_last_name']} - {$entry['reason']}\n";
    }
    if (count($needsFixing) > 10) {
        echo "   ... and " . (count($needsFixing) - 10) . " more\n";
    }
    echo "\n";
}

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
    $currentFullname = $entry['current_fullname'];
    $currentLastName = $entry['current_last_name'];
    
    // Strategy:
    // 1. If First Name has 3+ words, it likely contains last name - parse it
    // 2. If First Name has 2 words and we have a last name, it's likely a double first name
    // 3. Fullname should always be first + last
    
    $firstNameWords = explode(' ', trim($currentFirstName));
    $newFirstName = '';
    $newLastName = '';
    $newFullname = '';
    
    // Strategy: Use Last Name as the source of truth if available
    // If we have a last name, use it to determine first name from fullname or first name field
    
    if (!empty($currentLastName)) {
        // We have a last name - use it as source of truth
        $newLastName = trim($currentLastName);
        
        // Check if fullname already contains the last name
        if (!empty($currentFullname)) {
            $fullnameLower = strtolower($currentFullname);
            $lastNameLower = strtolower($newLastName);
            
            // Check if fullname ends with last name
            if (substr($fullnameLower, -strlen($lastNameLower)) === $lastNameLower) {
                // Fullname already has last name - extract first name from it
                $fullnameWords = explode(' ', trim($currentFullname));
                if (count($fullnameWords) > 1) {
                    // Remove last name from fullname to get first name
                    $lastWord = array_pop($fullnameWords);
                    if (strtolower($lastWord) === $lastNameLower) {
                        $newFirstName = trim(implode(' ', $fullnameWords));
                        $newFullname = trim($currentFullname);
                    } else {
                        // Last word doesn't match - might be a compound last name
                        $newFirstName = trim(implode(' ', $fullnameWords));
                        $newFullname = trim($currentFullname);
                    }
                } else {
                    // Fullname is just one word - use first name field
                    $newFirstName = trim($currentFirstName);
                    $newFullname = trim($newFirstName . ' ' . $newLastName);
                }
            } else {
                // Fullname doesn't end with last name - build it from first name + last name
                $newFirstName = trim($currentFirstName);
                $newFullname = trim($newFirstName . ' ' . $newLastName);
            }
        } else {
            // No fullname - use first name field
            $firstNameWords = explode(' ', trim($currentFirstName));
            
            // Check if first name field contains the last name
            $lastWord = end($firstNameWords);
            if (strtolower($lastWord) === strtolower($newLastName)) {
                // First name field contains last name - remove it
                array_pop($firstNameWords);
                $newFirstName = trim(implode(' ', $firstNameWords));
            } else {
                // First name field doesn't contain last name - use as is
                $newFirstName = trim($currentFirstName);
            }
            
            $newFullname = trim($newFirstName . ' ' . $newLastName);
        }
    } else {
        // No last name stored - try to extract from first name or fullname
        if (count($firstNameWords) >= 3) {
            // First Name contains full name (e.g., "Anne Louise Thorbecke")
            // Last word is likely the last name
            $newLastName = array_pop($firstNameWords);
            $newFirstName = trim(implode(' ', $firstNameWords));
            $newFullname = trim($newFirstName . ' ' . $newLastName);
        } elseif (count($firstNameWords) == 2) {
            // Two words - could be double first name or first + last
            // If fullname has more words, use it to determine
            if (!empty($currentFullname)) {
                $fullnameWords = explode(' ', trim($currentFullname));
                if (count($fullnameWords) > 2) {
                    // Fullname has more words - last word is likely last name
                    $newLastName = array_pop($fullnameWords);
                    $newFirstName = trim(implode(' ', $fullnameWords));
                    $newFullname = trim($currentFullname);
                } else {
                    // Fullname matches first name - assume double first name, no last name
                    $newFirstName = trim($currentFirstName);
                    $newFullname = trim($currentFullname ?: $currentFirstName);
                }
            } else {
                // No fullname - assume second word is last name
                $newLastName = array_pop($firstNameWords);
                $newFirstName = trim($firstNameWords[0]);
                $newFullname = trim($newFirstName . ' ' . $newLastName);
            }
        } else {
            // Single word first name
            $newFirstName = trim($currentFirstName);
            if (!empty($currentFullname)) {
                $fullnameWords = explode(' ', trim($currentFullname));
                if (count($fullnameWords) > 1) {
                    $newLastName = array_pop($fullnameWords);
                    $newFirstName = trim(implode(' ', $fullnameWords));
                    $newFullname = trim($currentFullname);
                } else {
                    $newFullname = $newFirstName;
                }
            } else {
                $newFullname = $newFirstName;
            }
        }
    }
    
    // Final validation and fixes
    
    // Check if first name contains the last name (e.g., "Anne Robertson" with last name "Robertson")
    if (!empty($newFirstName) && !empty($newLastName)) {
        $firstNameWords = explode(' ', trim($newFirstName));
        $lastWord = end($firstNameWords);
        if (strtolower($lastWord) === strtolower($newLastName)) {
            // First name contains last name - remove it
            array_pop($firstNameWords);
            $newFirstName = trim(implode(' ', $firstNameWords));
        }
    }
    
    // Ensure fullname is first + last
    if (!empty($newFirstName) && !empty($newLastName)) {
        $expectedFullname = trim($newFirstName . ' ' . $newLastName);
        $newFullname = $expectedFullname;
    } elseif (!empty($newFirstName) && !empty($newFullname)) {
        // If we have fullname but no last name, try to extract last name from fullname
        $fullnameWords = explode(' ', trim($newFullname));
        if (count($fullnameWords) > 1) {
            $possibleLastName = array_pop($fullnameWords);
            $possibleFirstName = trim(implode(' ', $fullnameWords));
            if (strtolower($possibleFirstName) === strtolower($newFirstName)) {
                $newLastName = $possibleLastName;
                $newFullname = trim($newFirstName . ' ' . $newLastName);
            }
        }
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

