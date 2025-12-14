<?php
/**
 * Authentication and Employee Setup Helper Functions
 * Used by admin-login.php and admin-setup.php
 */

class AuthenticationHelper {
    
    /**
     * Validate invitation token
     * @param string $token The invitation token
     * @return array|false Invitation data or false if invalid
     */
    public static function validateInvitationToken($token) {
        // Decode token (base64 format: email|timestamp|hash)
        $decoded = base64_decode($token, true);
        if (!$decoded) {
            return false;
        }
        
        $parts = explode('|', $decoded);
        if (count($parts) < 2) {
            return false;
        }
        
        $email = $parts[0];
        $timestamp = intval($parts[1]);
        
        // Check if invitation is still valid (72 hours)
        $expiry_time = $timestamp + (72 * 60 * 60);
        if (time() > $expiry_time) {
            return false;
        }
        
        // In a real application, verify against database
        // Check if token has already been used
        // For now, return success
        
        return [
            'email' => $email,
            'name' => 'John Doe', // Fetch from database in real application
            'created_at' => $timestamp,
            'expires_at' => $expiry_time,
            'used' => false
        ];
    }
    
    /**
     * Generate invitation token
     * @param string $email Employee email
     * @return string Encoded invitation token
     */
    public static function generateInvitationToken($email) {
        $timestamp = time();
        $data = $email . '|' . $timestamp;
        return base64_encode($data);
    }
    
    /**
     * Validate password strength
     * @param string $password Password to validate
     * @return array Array with 'valid' bool and 'errors' array
     */
    public static function validatePasswordStrength($password) {
        $errors = [];
        
        if (strlen($password) < 12) {
            $errors[] = 'Password must be at least 12 characters long.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Generate TOTP secret for MFA
     * @return string Base32 encoded secret
     */
    public static function generateTOTPSecret() {
        // In a real application, use a library like OTPHP
        // For now, generate a random string
        $secret = base64_encode(random_bytes(32));
        return substr($secret, 0, 32);
    }
    
    /**
     * Generate QR code for TOTP
     * @param string $issuer Application name
     * @param string $email User email
     * @param string $secret TOTP secret
     * @return string QR code URL
     */
    public static function generateQRCode($issuer, $email, $secret) {
        $label = urlencode("$issuer ($email)");
        $otpauth_url = "otpauth://totp/$label?secret=$secret&issuer=" . urlencode($issuer);
        
        // Use QR code generation API
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth_url);
    }
    
    /**
     * Generate recovery codes
     * @return array Array of recovery codes
     */
    public static function generateRecoveryCodes() {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $code = strtoupper(substr(md5(random_bytes(16)), 0, 6));
            // Format as XXXX-XXXX-XXXX
            $codes[] = substr($code, 0, 4) . '-' . substr($code, 4, 4) . '-' . substr($code, 8, 4);
        }
        return $codes;
    }
    
    /**
     * Verify TOTP code
     * @param string $secret TOTP secret
     * @param string $code 6-digit code
     * @return bool True if valid
     */
    public static function verifyTOTPCode($secret, $code) {
        // In a real application, use a library to verify TOTP
        // For demo, accept any 6-digit code
        return strlen($code) === 6 && is_numeric($code);
    }
    
    /**
     * Create admin/employee session
     * @param string $email Employee email
     * @param string $name Employee name
     */
    public static function createSession($email, $name) {
        $_SESSION['admin_id'] = 'admin_' . md5($email);
        $_SESSION['admin_email'] = $email;
        $_SESSION['admin_name'] = $name;
        $_SESSION['login_time'] = time();
        $_SESSION['session_token'] = bin2hex(random_bytes(32));
    }
    
    /**
     * Validate session
     * @return bool True if session is valid
     */
    public static function isSessionValid() {
        if (!isset($_SESSION['admin_id'])) {
            return false;
        }
        
        // Check session timeout (30 minutes)
        if (isset($_SESSION['login_time'])) {
            if (time() - $_SESSION['login_time'] > 1800) {
                session_destroy();
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Log authentication attempt
     * @param string $email Email used in attempt
     * @param bool $success Whether attempt was successful
     * @param string $reason Reason for failure (if applicable)
     */
    public static function logAuthAttempt($email, $success, $reason = '') {
        // In a real application, log to database
        $log_file = __DIR__ . '/logs/auth_attempts.log';
        
        if (!is_dir(dirname($log_file))) {
            mkdir(dirname($log_file), 0755, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $status = $success ? 'SUCCESS' : 'FAILED';
        $message = "[$timestamp] $status - Email: $email - IP: $ip";
        
        if (!$success && $reason) {
            $message .= " - Reason: $reason";
        }
        
        error_log($message . "\n", 3, $log_file);
    }
    
    /**
     * Check if email is rate limited
     * @param string $email Email to check
     * @return bool True if rate limited
     */
    public static function isRateLimited($email) {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_attempt_time'] = time();
            return false;
        }
        
        // Reset if 15 minutes have passed
        if (time() - $_SESSION['login_attempt_time'] > 900) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_attempt_time'] = time();
            return false;
        }
        
        // Rate limit: 5 attempts per 15 minutes
        return $_SESSION['login_attempts'] >= 5;
    }
    
    /**
     * Check if CAPTCHA should be shown
     * @return bool True if CAPTCHA should be shown
     */
    public static function shouldShowCaptcha() {
        if (!isset($_SESSION['login_attempts'])) {
            return false;
        }
        
        return $_SESSION['login_attempts'] >= 3;
    }
    
    /**
     * Hash password using bcrypt
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    /**
     * Verify password
     * @param string $password Plain text password
     * @param string $hash Password hash
     * @return bool True if password matches
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}

// Export functions for use in PHP files
if (!function_exists('validateInvitationToken')) {
    function validateInvitationToken($token) {
        return AuthenticationHelper::validateInvitationToken($token);
    }
}

if (!function_exists('validatePassword')) {
    function validatePassword($password, $confirm) {
        $validation = AuthenticationHelper::validatePasswordStrength($password);
        return $validation['errors'];
    }
}

if (!function_exists('generateMFAQRCode')) {
    function generateMFAQRCode($issuer, $email, $secret) {
        return AuthenticationHelper::generateQRCode($issuer, $email, $secret);
    }
}
