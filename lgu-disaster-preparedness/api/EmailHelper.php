<?php
/**
 * Email Helper Class for Sending Authentication Codes
 */

class EmailHelper {
    
    /**
     * Send MFA code via email
     * @param string $email Recipient email
     * @param string $code MFA code
     * @return bool Success status
     */
    public static function sendMFACode($email, $code) {
        // For development/demo, log the code to a file
        // In production, use a proper email service
        
        // Log to file for demo purposes
        $log_file = __DIR__ . '/logs/mfa_codes.log';
        if (!is_dir(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $message = "[$timestamp] Email: $email | Code: $code\n";
        file_put_contents($log_file, $message, FILE_APPEND);
        
        // Try to send email if mail() is available
        if (function_exists('mail')) {
            $to = $email;
            $subject = "Your Multi-Factor Authentication Code";
            $body = self::getMFAEmailTemplate($code, $email);
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: noreply@lgu-disaster-preparedness.local\r\n";
            
            return mail($to, $subject, $body, $headers);
        }
        
        return true; // Return true for demo mode
    }
    
    /**
     * Send account setup email with invitation link
     * @param string $email Recipient email
     * @param string $name Employee name
     * @param string $token Invitation token
     * @return bool Success status
     */
    public static function sendSetupInvitation($email, $name, $token) {
        $log_file = __DIR__ . '/logs/invitations.log';
        if (!is_dir(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $invitation_url = "http://localhost/frontend/admin-setup.php?token=" . urlencode($token);
        $message = "[$timestamp] Email: $email | Name: $name | URL: $invitation_url\n";
        file_put_contents($log_file, $message, FILE_APPEND);
        
        if (function_exists('mail')) {
            $to = $email;
            $subject = "Complete Your Account Setup - LGU Disaster Preparedness";
            $body = self::getSetupEmailTemplate($name, $invitation_url);
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: noreply@lgu-disaster-preparedness.local\r\n";
            
            return mail($to, $subject, $body, $headers);
        }
        
        return true;
    }
    
    /**
     * Get MFA email template
     */
    private static function getMFAEmailTemplate($code, $email) {
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3a7675; padding-bottom: 20px; }
        .header h1 { color: #3a7675; margin: 0; }
        .code-box { background-color: #f0f5f5; border: 2px solid #3a7675; border-radius: 6px; padding: 20px; text-align: center; margin: 20px 0; }
        .code-box .code { font-size: 32px; font-weight: bold; color: #3a7675; letter-spacing: 3px; font-family: 'Courier New', monospace; }
        .info { color: #666; font-size: 14px; line-height: 1.6; margin: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; color: #856404; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔐 Multi-Factor Authentication</h1>
        </div>
        
        <p class='info'>Hello,</p>
        
        <p class='info'>Your authentication code for the LGU Disaster Preparedness Management System is:</p>
        
        <div class='code-box'>
            <div class='code'>$code</div>
        </div>
        
        <p class='info'><strong>This code will expire in 10 minutes.</strong></p>
        
        <div class='warning'>
            <strong>Security Notice:</strong> If you did not request this code, please contact your administrator immediately. Do not share this code with anyone.
        </div>
        
        <p class='info'>
            <strong>Email:</strong> $email<br>
            <strong>Requested at:</strong> " . date('Y-m-d H:i:s') . "
        </p>
        
        <p class='info'>
            Regards,<br>
            LGU Disaster Preparedness Team
        </p>
        
        <div class='footer'>
            <p>&copy; 2025 Local Government Unit Disaster Preparedness. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
        ";
    }
    
    /**
     * Get setup invitation email template
     */
    private static function getSetupEmailTemplate($name, $invitation_url) {
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3a7675; padding-bottom: 20px; }
        .header h1 { color: #3a7675; margin: 0; }
        .button { display: inline-block; background-color: #3a7675; color: white; padding: 12px 30px; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        .info { color: #666; font-size: 14px; line-height: 1.6; margin: 20px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; }
        .warning { background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 20px 0; color: #721c24; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Welcome to the Employee Portal! 👋</h1>
        </div>
        
        <p class='info'>Hello <strong>$name</strong>,</p>
        
        <p class='info'>You have been invited to complete your account setup for the LGU Disaster Preparedness Management System.</p>
        
        <p style='text-align: center;'>
            <a href='$invitation_url' class='button'>Complete Setup Now</a>
        </p>
        
        <p class='info'><strong>Or copy and paste this link in your browser:</strong></p>
        <p class='info' style='word-break: break-all;'><code>$invitation_url</code></p>
        
        <div class='warning'>
            <strong>⚠️ Important:</strong> This invitation link will expire in 72 hours. If you don't complete your setup within this time, you'll need to request a new invitation.
        </div>
        
        <p class='info'>
            <strong>During setup, you will:</strong><br>
            ✓ Create a secure password<br>
            ✓ Set up two-factor authentication (2FA)<br>
            ✓ Verify your identity<br>
        </p>
        
        <p class='info'>
            If you did not expect this email or have any questions, please contact your administrator.
        </p>
        
        <p class='info'>
            Regards,<br>
            LGU Disaster Preparedness Team
        </p>
        
        <div class='footer'>
            <p>&copy; 2025 Local Government Unit Disaster Preparedness. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
        ";
    }
}
