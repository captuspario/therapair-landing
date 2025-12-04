<?php
/**
 * Therapair Email Template Base
 * Shared template function using design system colors and typography
 */

/**
 * Generate email template with logo and design system styling
 * 
 * @param string $content Main email content HTML
 * @param string $preheader Optional preheader text
 * @return string Complete HTML email
 */
function getEmailTemplate($content, $preheader = '')
{
    // Logo URL (hosted on domain)
    $logoUrl = 'https://therapair.com.au/images/therapair-logo-final.png';
    
    // Design system colors
    $darkNavy = '#0F1E4B';      // Primary / Icon Core
    $midBlue = '#3D578A';       // Icon Middle Layer
    $lightBlue = '#95B1CD';     // Icon Ears / Accents
    $black = '#000000';         // Wordmark / Headings
    $warmBeige = '#F9FAFD';     // Background (updated to match React Email)
    $darkGrey = '#4A5568';      // Body Text / Secondary
    $white = '#FFFFFF';         // Surface
    $headerBg = '#F1F3F5';     // Header background (updated to match React Email)
    
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <!--[if mso]>
    <style type="text/css">
        body, table, td { font-family: "Inter", Arial, sans-serif !important; }
    </style>
    <![endif]-->
    ' . ($preheader ? '<style type="text/css">.preheader { display: none !important; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0; }</style>' : '') . '
</head>
<body style="margin: 0; padding: 0; background-color: ' . $warmBeige . '; font-family: \'Inter\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, \'Helvetica Neue\', Arial, sans-serif;">
    ' . ($preheader ? '<span class="preheader">' . htmlspecialchars($preheader) . '</span>' : '') . '
    
    <!-- Email Container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: ' . $warmBeige . '; padding: 40px 20px;">
        <tr>
            <td align="center">
                <!-- Main Content Card -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="background-color: ' . $white . '; border-radius: 12px; box-shadow: 0 4px 12px rgba(15, 30, 75, 0.08); max-width: 600px; width: 100%;">
                    
                    <!-- Header with Logo -->
                    <tr>
                        <td style="padding: 24px 40px; text-align: left; background-color: ' . $headerBg . '; border-radius: 12px 12px 0 0;">
                            <img src="' . $logoUrl . '" alt="Therapair" style="height: 90px; width: auto; display: block; margin: 0; float: none;" />
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px;">
                            ' . $content . '
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: ' . $warmBeige . '; border-radius: 0 0 12px 12px; border-top: 1px solid rgba(149, 177, 205, 0.2);">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center;">
                                        <p style="margin: 0 0 12px 0; color: ' . $darkGrey . '; font-size: 14px; line-height: 1.6; font-weight: 500;">
                                            <strong style="color: ' . $black . ';">Therapair</strong>
                                        </p>
                                        <p style="margin: 0 0 20px 0; color: ' . $darkGrey . '; font-size: 13px; line-height: 1.5;">
                                            Connecting you with therapists who actually get you.
                                        </p>
                                        
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" style="margin: 0 auto;">
                                            <tr>
                                                <td style="padding: 0 8px;">
                                                    <a href="mailto:contact@therapair.com.au" style="color: ' . $midBlue . '; text-decoration: none; font-size: 13px;">Contact</a>
                                                </td>
                                                <td style="padding: 0 8px; color: ' . $lightBlue . ';">|</td>
                                                <td style="padding: 0 8px;">
                                                    <a href="https://therapair.com.au" style="color: ' . $midBlue . '; text-decoration: none; font-size: 13px;">Website</a>
                                                </td>
                                                <td style="padding: 0 8px; color: ' . $lightBlue . ';">|</td>
                                                <td style="padding: 0 8px;">
                                                    <a href="https://therapair.com.au/email-preferences.html" style="color: ' . $midBlue . '; text-decoration: none; font-size: 13px;">Email Preferences</a>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <p style="margin: 20px 0 0 0; color: ' . $darkGrey . '; font-size: 11px; line-height: 1.5;">
                                            © 2025 Therapair. Made for inclusive mental health.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>';

    return $html;
}

/**
 * Generate button/link styling for emails
 */
function getEmailButtonStyle($variant = 'primary')
{
    $darkNavy = '#0F1E4B';
    $midBlue = '#3D578A';
    $lightBlue = '#95B1CD';
    $white = '#FFFFFF';
    $darkGrey = '#4A5568';
    
    switch ($variant) {
        case 'primary':
            return 'display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, ' . $lightBlue . ', ' . $midBlue . '); color: ' . $white . '; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; line-height: 1.5;';
        case 'secondary':
            return 'display: inline-block; padding: 14px 32px; background-color: ' . $white . '; color: ' . $midBlue . '; text-decoration: none; border: 2px solid ' . $midBlue . '; border-radius: 8px; font-weight: 600; font-size: 16px; line-height: 1.5;';
        case 'text':
            return 'color: ' . $midBlue . '; text-decoration: none; font-size: 16px; font-weight: 500;';
        default:
            return 'display: inline-block; padding: 14px 32px; background-color: ' . $midBlue . '; color: ' . $white . '; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px; line-height: 1.5;';
    }
}

/**
 * Generate content box styling
 */
function getEmailBoxStyle()
{
    $lightBlue = '#95B1CD';
    $warmBeige = '#FAF8F5';
    
    return 'background-color: ' . $warmBeige . '; border-left: 4px solid ' . $lightBlue . '; padding: 20px; margin: 24px 0; border-radius: 4px;';
}

