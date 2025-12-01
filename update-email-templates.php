<?php
/**
 * Script to update all email templates with new design system
 * Run this to see the new templates in action
 */

require_once __DIR__ . '/email-template-base.php';

// Test the new email template
$content = '<h1 style="color: #0F1E4B;">Test Email Template</h1><p>This uses the new design system with logo and brand colors.</p>';
echo getEmailTemplate($content);
?>



