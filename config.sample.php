<?php
/**
 * Therapair Landing Page configuration (sample).
 *
 * Copy this file to config.php and replace placeholder values with your production secrets.
 */

// Notion API credentials
define('NOTION_TOKEN', 'secret_xxx'); // Internal integration token

// Database IDs
define('NOTION_DB_USER_TESTING', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // User Testing Group
define('NOTION_DB_SANDBOX', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');      // Sandbox Feedback
define('NOTION_DB_SURVEY', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');       // Survey Feedback
define('NOTION_DB_EOI', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');          // Expression of Interest

// Legacy / Optional
define('THERAPIST_RESEARCH_DATABASE_ID', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'); // Research responses DB
define('THERAPIST_DIRECTORY_DATABASE_ID', 'yyyyyyyyyyyyyyyyyyyyyyyyyyyyyyyy'); // Existing VIC therapist directory (optional)

// Research token secret (use a long, random string). This should match whatever
// service is generating signed invite links.
define('RESEARCH_TOKEN_SECRET', 'change-me-to-a-long-random-string');

// Consent defaults shown in the survey UI
define('RESEARCH_CONSENT_VERSION', '2025-11-13');
define('RESEARCH_CONSENT_LINK', '/legal/privacy-policy.html');

// Optional: salt added before hashing IP addresses for audit purposes
define('RESEARCH_IP_SALT', 'add-a-random-salt-value-here');

// Property name overrides (only change if your Notion database uses different labels)
define('NOTION_RESEARCH_TITLE_PROPERTY', 'Respondent ID');
define('NOTION_RESEARCH_THERAPIST_NAME_PROPERTY', 'Therapist Name');
define('NOTION_RESEARCH_THERAPIST_ID_PROPERTY', 'Therapist ID');
define('NOTION_RESEARCH_DIRECTORY_ID_PROPERTY', 'Directory Page ID');
define('NOTION_RESEARCH_THERAPIST_EMAIL_PROPERTY', 'Therapist Email');
define('NOTION_RESEARCH_SCREENS_CLIENTS_PROPERTY', 'Screens Clients');
define('NOTION_RESEARCH_PROFILE_INTENT_PROPERTY', 'Profile Intent');
define('NOTION_RESEARCH_PROFILE_READY_PROPERTY', 'Profile Ready');
define('NOTION_RESEARCH_CONSENT_VERSION_PROPERTY', 'Consent Version');
define('NOTION_RESEARCH_CONSENT_TIMESTAMP_PROPERTY', 'Consent Timestamp');
define('NOTION_RESEARCH_SESSION_ID_PROPERTY', 'Survey Session ID');
define('NOTION_RESEARCH_ENGAGEMENT_SOURCE_PROPERTY', 'Engagement Source');
define('NOTION_RESEARCH_SOURCE_NOTES_PROPERTY', 'Source Notes');
define('NOTION_RESEARCH_IP_HASH_PROPERTY', 'IP Hash');

// Therapist directory property overrides (optional)
define('NOTION_DIRECTORY_RESEARCH_STATUS_PROPERTY', 'Research Status');
define('NOTION_DIRECTORY_LATEST_SURVEY_PROPERTY', 'Latest Survey Date');
define('NOTION_DIRECTORY_PROFILE_INTENT_PROPERTY', 'Profile Intent');
define('NOTION_DIRECTORY_PROFILE_READY_PROPERTY', 'Profile Ready');
define('NOTION_DIRECTORY_FUTURE_CONTACT_PROPERTY', 'Research Follow-up');

