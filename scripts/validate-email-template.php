<?php
/**
 * Email Template Validation
 * Run this before deploying to catch broken links and missing CTAs
 * 
 * Usage: php scripts/validate-email-template.php
 */

$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST = [];

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../email-template-base.php';

// Mock the formatUserEmail function for therapist audience
function mockFormatTherapistEmail() {
    $darkGrey = '#4A5568';
    
    return '
        <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 24px 0;">
            <strong>Explore Therapair</strong>
        </p>
        <p style="font-size: 16px; line-height: 1.6; color: ' . $darkGrey . '; margin: 0 0 32px 0;">
            We\'d love your input as we build Therapair. Here are two ways to get involved:
        </p>
        
        <!-- First CTA: Sandbox Demo -->
        <div style="margin: 0 0 32px 0;">
            <a href="https://therapair.com.au/sandbox/sandbox-demo.html" style="' . getEmailButtonStyle('primary') . '; margin-bottom: 8px; display: inline-block;">
                View Sandbox Demo
            </a>
            <p style="font-size: 15px; line-height: 1.6; color: ' . $darkGrey . '; margin: 8px 0 0 0;">
                Experience our therapist-matching prototype
            </p>
        </div>
        
        <!-- Second CTA: Research Survey -->
        <div style="margin: 0 0 24px 0;">
            <a href="https://therapair.com.au/research/survey/index.html" style="' . getEmailButtonStyle('secondary') . '; display: inline-block;">
                Take Research Survey
            </a>
            <p style="font-size: 15px; line-height: 1.6; color: ' . $darkGrey . '; margin: 8px 0 0 0;">
                Help shape Therapair by completing our short user research survey (invitation link will be sent separately)
            </p>
        </div>
    ';
}

// Mock the addTrackingToEmailLinks function
function mockAddTracking($emailHtml, $email, $audience) {
    $trackBase = 'https://therapair.com.au/track.php';
    $emailHash = md5(strtolower(trim($email)));
    $utmSource = 'email';
    $utmMedium = 'eoi_confirmation';
    $utmContent = $audience;
    
    // Track sandbox
    $sandboxUrl = 'https://therapair.com.au/sandbox/sandbox-demo.html';
    $trackingSandboxUrl = $trackBase . '?email=' . urlencode($emailHash) . 
        '&dest=sandbox' . 
        '&utm_source=' . urlencode($utmSource) . 
        '&utm_medium=' . urlencode($utmMedium) . 
        '&utm_campaign=sandbox_demo' . 
        '&utm_content=' . urlencode($utmContent);
    $emailHtml = str_replace($sandboxUrl, $trackingSandboxUrl, $emailHtml);
    $emailHtml = str_replace('href="' . $sandboxUrl, 'href="' . $trackingSandboxUrl, $emailHtml);
    
    // Track survey
    $surveyUrl = 'https://therapair.com.au/research/survey/index.html';
    $trackingSurveyUrl = $trackBase . '?email=' . urlencode($emailHash) . 
        '&dest=survey' . 
        '&utm_source=' . urlencode($utmSource) . 
        '&utm_medium=' . urlencode($utmMedium) . 
        '&utm_campaign=research_survey' . 
        '&utm_content=' . urlencode($utmContent);
    $emailHtml = str_replace($surveyUrl, $trackingSurveyUrl, $emailHtml);
    $emailHtml = str_replace('href="' . $surveyUrl, 'href="' . $trackingSurveyUrl, $emailHtml);
    
    return $emailHtml;
}

echo "🔍 Validating Email Template\n";
echo str_repeat("=", 70) . "\n\n";

$testEmail = 'test@therapair.com.au';
$emailHtml = mockFormatTherapistEmail();
$emailHtml = mockAddTracking($emailHtml, $testEmail, 'therapist');

// Extract all links
preg_match_all('/href="([^"]+)"/', $emailHtml, $matches);
$links = $matches[1];

$errors = [];
$warnings = [];

echo "📧 Found " . count($links) . " link(s):\n\n";

$hasSandbox = false;
$hasSurvey = false;

foreach ($links as $i => $link) {
    $num = $i + 1;
    echo "  $num. " . substr($link, 0, 80) . (strlen($link) > 80 ? '...' : '') . "\n";
    
    // Check if it's a tracking link
    if (strpos($link, 'track.php') !== false) {
        echo "     ✅ Tracking link\n";
        
        // Validate tracking URL structure
        if (!preg_match('/dest=(sandbox|survey|preferences|home)/', $link)) {
            $errors[] = "Link #$num has invalid destination parameter";
        }
        if (!preg_match('/email=[a-f0-9]{32}/', $link)) {
            $errors[] = "Link #$num has invalid email hash";
        }
        
        // Check destination
        if (preg_match('/dest=([^&]+)/', $link, $destMatch)) {
            $dest = $destMatch[1];
            echo "     📍 Destination: $dest\n";
            
            if ($dest === 'sandbox') {
                $hasSandbox = true;
            } elseif ($dest === 'survey') {
                $hasSurvey = true;
            }
        }
    } else {
        // Direct link (should be tracked)
        if (strpos($link, 'sandbox') !== false || strpos($link, 'survey') !== false) {
            $errors[] = "Link #$num is a direct link but should use tracking: $link";
            echo "     ❌ ERROR: Direct link (should use tracking)\n";
        }
    }
    
    // Validate URL format
    if (!filter_var($link, FILTER_VALIDATE_URL) && strpos($link, 'track.php') === false) {
        $errors[] = "Link #$num is not a valid URL: $link";
        echo "     ❌ ERROR: Invalid URL format\n";
    }
    
    echo "\n";
}

// Check for required CTAs
echo "📋 CTA Checklist:\n";
echo "   " . ($hasSandbox ? "✅" : "❌") . " Sandbox Demo link\n";
echo "   " . ($hasSurvey ? "✅" : "❌") . " Research Survey link\n\n";

if (!$hasSandbox) {
    $errors[] = "Missing Sandbox Demo CTA";
}
if (!$hasSurvey) {
    $errors[] = "Missing Research Survey CTA";
}

// Summary
echo str_repeat("=", 70) . "\n";
if (empty($errors) && empty($warnings)) {
    echo "✅ All validation checks passed!\n";
    exit(0);
} else {
    if (!empty($errors)) {
        echo "❌ ERRORS found:\n\n";
        foreach ($errors as $error) {
            echo "   • $error\n";
        }
        echo "\n";
    }
    if (!empty($warnings)) {
        echo "⚠️  WARNINGS:\n\n";
        foreach ($warnings as $warning) {
            echo "   • $warning\n";
        }
        echo "\n";
    }
    echo "💡 Fix these issues before deploying!\n";
    exit(1);
}

