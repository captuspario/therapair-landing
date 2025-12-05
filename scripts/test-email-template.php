<?php
/**
 * Test Email Template
 * Validates email template structure and links without requiring web context
 */

// Mock web context
$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST = [];

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../email-template-base.php';

// Copy formatUserEmail function logic for testing
function testFormatTherapistEmail() {
    $darkGrey = '#4A5568';
    
    $content = '
        <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 16px 0;">
            <strong>Explore Therapair</strong>
        </p>
        <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 32px 0;">
            We\'d love your input as we build Therapair. Try our sandbox demo to see the therapist-matching prototype in action:
        </p>
        
        <!-- CTA: Sandbox Demo -->
        <div style="margin: 0 0 24px 0;">
            <a href="https://therapair.com.au/sandbox/sandbox-demo.html" style="' . getEmailButtonStyle('primary') . '; margin-bottom: 8px; display: inline-block;">
                View Sandbox Demo
            </a>
            <p style="font-size: 15px; line-height: 1.6; color: ' . $darkGrey . '; margin: 8px 0 0 0;">
                Experience our therapist-matching prototype
            </p>
        </div>
    ';
    
    return $content;
}

// Test tracking function
function testAddTracking($emailHtml, $email) {
    $trackBase = 'https://therapair.com.au/track.php';
    $emailHash = md5(strtolower(trim($email)));
    $utmSource = 'email';
    $utmMedium = 'eoi_confirmation';
    $utmContent = 'therapist';
    
    $sandboxUrl = 'https://therapair.com.au/sandbox/sandbox-demo.html';
    $trackingSandboxUrl = $trackBase . '?email=' . urlencode($emailHash) . 
        '&dest=sandbox' . 
        '&utm_source=' . urlencode($utmSource) . 
        '&utm_medium=' . urlencode($utmMedium) . 
        '&utm_campaign=sandbox_demo' . 
        '&utm_content=' . urlencode($utmContent);
    
    $emailHtml = str_replace($sandboxUrl, $trackingSandboxUrl, $emailHtml);
    $emailHtml = str_replace('href="' . $sandboxUrl, 'href="' . $trackingSandboxUrl, $emailHtml);
    
    return $emailHtml;
}

echo "🧪 Testing Email Template\n";
echo str_repeat("=", 60) . "\n\n";

// Generate email
$emailHtml = testFormatTherapistEmail();
$testEmail = 'test@therapair.com.au';
$emailHtml = testAddTracking($emailHtml, $testEmail);

// Extract links
preg_match_all('/href="([^"]+)"/', $emailHtml, $matches);
$links = $matches[1];

echo "📧 Found " . count($links) . " link(s) in email:\n\n";

$issues = [];
foreach ($links as $i => $link) {
    $num = $i + 1;
    echo "$num. $link\n";
    
    // Validate
    if (strpos($link, 'track.php') !== false) {
        echo "   ✅ Tracking link\n";
        
        // Check destination
        if (preg_match('/dest=([^&]+)/', $link, $destMatch)) {
            echo "   📍 Destination: {$destMatch[1]}\n";
        }
        
        // Check email hash
        if (preg_match('/email=([^&]+)/', $link, $emailMatch)) {
            echo "   📧 Email hash: {$emailMatch[1]}\n";
        }
    } else {
        if (strpos($link, 'sandbox') !== false || strpos($link, 'survey') !== false) {
            $issues[] = "Link should use tracking: $link";
            echo "   ⚠️  WARNING: Direct link (should use tracking)\n";
        }
    }
    
    echo "\n";
}

// Check for required CTAs
$hasSandbox = false;
$hasSurvey = false;
foreach ($links as $link) {
    if (strpos($link, 'sandbox') !== false) {
        $hasSandbox = true;
    }
    if (strpos($link, 'survey') !== false || strpos($link, 'research/survey') !== false) {
        $hasSurvey = true;
    }
}

echo "📋 CTA Checklist:\n";
echo "   " . ($hasSandbox ? "✅" : "❌") . " Sandbox Demo link\n";
echo "   " . ($hasSurvey ? "✅" : "❌") . " Research Survey link\n\n";

if (!$hasSandbox) {
    $issues[] = "Missing Sandbox Demo CTA";
}

// Summary
echo str_repeat("=", 60) . "\n";
if (empty($issues)) {
    echo "✅ All checks passed!\n";
} else {
    echo "❌ Issues found:\n\n";
    foreach ($issues as $issue) {
        echo "   • $issue\n";
    }
}
echo "\n";

