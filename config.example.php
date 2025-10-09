<?php
/**
 * Therapair Configuration File - EXAMPLE
 * 
 * SETUP INSTRUCTIONS:
 * 1. Copy this file to config.php
 * 2. Update the values below with your actual credentials
 * 3. config.php is in .gitignore and will not be committed to git
 */

// ============================================
// EMAIL CONFIGURATION
// ============================================
define('ADMIN_EMAIL', 'contact@therapair.com.au');
define('FROM_EMAIL', 'noreply@therapair.com.au');
define('FROM_NAME', 'Therapair');

// ============================================
// OPENAI API CONFIGURATION
// ============================================
// Get your API key from: https://platform.openai.com/api-keys
define('OPENAI_API_KEY', 'sk-proj-YOUR_API_KEY_HERE');

// Enable/disable AI personalization
define('USE_AI_PERSONALIZATION', true);

// AI Model Selection
// 'gpt-4o-mini' - Recommended: Fast, cheap ($0.15/1M input tokens), good quality
// 'gpt-4o' - Best quality but more expensive ($2.50/1M input tokens)
// 'gpt-3.5-turbo' - Cheapest but lower quality
define('AI_MODEL', 'gpt-4o-mini');

// ============================================
// WEBSITE SETTINGS
// ============================================
define('WEBSITE_URL', 'https://therapair.com.au');
define('THANK_YOU_URL', '/thank-you.html');

?>

