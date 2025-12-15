<?php
session_start();
require_once __DIR__ . '/../api/EmailHelper.php';

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

// Initialize variables
$error_message = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Simple validation for demo
    if (empty($email) || empty($password)) {
        $error_message = 'Email and password are required.';
    } else if ($email === 'jbemployee50@gmail.com' && $password === 'admin123') {
        // Test credentials match - generate and send MFA code
        $mfa_code = sprintf('%06d', rand(0, 999999));
        $_SESSION['mfa_code'] = $mfa_code;
        $_SESSION['temp_email'] = $email;
        $_SESSION['mfa_pending'] = true;
        
        // Send MFA code via email
        EmailHelper::sendMFACode($email, $mfa_code);
        
        header("Location: admin-login.php?mfa=1");
        exit;
    } else {
        // Track failed attempts
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
            $_SESSION['login_attempt_time'] = time();
        }
        
        $_SESSION['login_attempts']++;
        
        // Rate limiting: 5 attempts per 15 minutes
        if ($_SESSION['login_attempts'] >= 5) {
            $time_diff = time() - $_SESSION['login_attempt_time'];
            if ($time_diff < 900) { // 15 minutes
                $error_message = 'Too many login attempts. Please try again in 15 minutes.';
                $_SESSION['account_locked'] = true;
            } else {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['login_attempt_time'] = time();
                $error_message = 'Invalid email or password.';
            }
        } else {
            $error_message = 'Invalid email or password.';
            
            // Show CAPTCHA after 3 failed attempts
            if ($_SESSION['login_attempts'] >= 3) {
                $_SESSION['show_captcha'] = true;
            }
        }
    }
}

// Handle MFA submission
$show_mfa = isset($_GET['mfa']) && $_GET['mfa'] == 1;
if ($show_mfa && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mfa_code'])) {
    $mfa_code = $_POST['mfa_code'];
    
    // Verify MFA code
    if (isset($_SESSION['mfa_code']) && $_SESSION['mfa_code'] === $mfa_code) {
        // Set session for authenticated user
        $_SESSION['admin_id'] = 'admin_' . md5($_SESSION['temp_email']);
        $_SESSION['admin_email'] = $_SESSION['temp_email'];
        $_SESSION['admin_name'] = 'Employee'; // Default name, can be updated in setup
        $_SESSION['first_login'] = true;
        $_SESSION['mfa_pending'] = false;
        
        unset($_SESSION['temp_email']);
        unset($_SESSION['mfa_code']);
        unset($_SESSION['login_attempts']);
        
        // Redirect to setup if first time
        header("Location: admin-setup.php");
        exit;
    } else {
        $error_message = 'Invalid MFA code. Please try again.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Portal Login - Disaster Preparedness</title>
    <link rel="stylesheet" href="css/admin-login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Logo/Header -->
            <div class="login-header">
                <div class="logo">
                    <img src="images/logo.svg" alt="LGU Logo" class="logo-img">
                </div>
                <h1>Employee Portal</h1>
                <p class="subtitle">Disaster Preparedness Management System</p>
            </div>

            <!-- Employee Only Notice -->
            <div class="employee-notice">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Employee access only. Authorized personnel sign-in required.</span>
            </div>

            <?php if (!$show_mfa): ?>
            <!-- Login Form -->
            <form method="POST" class="login-form" id="loginForm">
                <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
                <?php endif; ?>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email">Company Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="employee@company.com"
                        value="<?php echo htmlspecialchars($email); ?>"
                        required
                        <?php echo isset($_SESSION['account_locked']) ? 'disabled' : ''; ?>
                    >
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="••••••••"
                            required
                            <?php echo isset($_SESSION['account_locked']) ? 'disabled' : ''; ?>
                        >
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Device Checkbox -->
                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember" class="checkbox-input">
                    <label for="remember" class="checkbox-label">Remember this device</label>
                </div>

                <!-- CAPTCHA Section (shown after 3 failed attempts) -->
                <?php if (isset($_SESSION['show_captcha']) && $_SESSION['show_captcha']): ?>
                <div class="captcha-section">
                    <label>Verify you're human</label>
                    <div class="captcha-box">
                        <div class="g-recaptcha" data-sitekey="your-site-key-here">
                            <p style="font-size: 12px; color: #666; margin: 0;">
                                reCAPTCHA protection active
                                <br><small>This site is protected by reCAPTCHA and the Google<br>
                                <a href="https://policies.google.com/privacy">Privacy Policy</a> and
                                <a href="https://policies.google.com/terms">Terms of Service</a> apply.</small>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <button type="submit" class="btn-signin" <?php echo isset($_SESSION['account_locked']) ? 'disabled' : ''; ?>>
                    Sign In
                </button>
            </form>

            <?php else: ?>
            <!-- MFA Form -->
            <form method="POST" class="login-form" id="mfaForm">
                <div class="mfa-header">
                    <h2>Multi-Factor Authentication</h2>
                    <p>Enter the 6-digit code from your authenticator app</p>
                </div>

                <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="mfa_code">Authentication Code</label>
                    <input 
                        type="text" 
                        id="mfa_code" 
                        name="mfa_code" 
                        class="form-input mfa-input" 
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        required
                        autofocus
                    >
                </div>

                <button type="submit" class="btn-signin">Verify Code</button>

                <div class="mfa-help">
                    <p><strong>Don't have your authenticator app?</strong></p>
                    <p>If you don't have access to your authenticator app, contact your administrator.</p>
                </div>
            </form>
            <?php endif; ?>

            <!-- Footer -->
            <div class="login-footer">
                <p>&copy; 2025 Local Government Unit Disaster Preparedness. All rights reserved.</p>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
    </div>

    <script src="js/admin-login.js"></script>
</body>
</html>
