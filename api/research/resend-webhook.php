<?php
/**
 * Resend Webhook Endpoint
 * 
 * This file is an alias/redirect to email-event.php
 * It exists to match the webhook URL configured in Resend dashboard:
 * https://therapair.com.au/api/research/resend-webhook.php
 * 
 * The actual webhook handler is in email-event.php
 */

// Redirect to the actual webhook handler
require_once __DIR__ . '/email-event.php';

