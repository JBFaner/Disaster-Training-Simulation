<?php
session_start();
require_once __DIR__ . '/../api/EmailHelper.php';

// Redirect if already logged in
if (isset($_SESSION['participant_id'])) {
    header("Location: ../part-index.php");
    exit;
}

// Initialize variables
$error_message = '';
$email = '';
$is_signup = isset($_GET['signup']);
$verification_email = '';

// Handle Sign Up
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    
    // Validation
    if (empty($email) || empty($password) || empty($name)) {
        $error_message = 'Name, email, and password are required.';
    } else if ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } else if (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Generate verification code
        $verification_code = sprintf('%06d', rand(0, 999999));
        $_SESSION['signup_data'] = [
            'email' => $email,
            'password' => $password,
            'name' => $name,
            'verification_code' => $verification_code
        ];
        $_SESSION['verification_pending'] = true;
        
        // Send verification code via email
        EmailHelper::sendVerificationCode($email, $verification_code);
        
        $_SESSION['show_verification'] = true;
        $verification_email = $email;
    }
}

// Handle Sign In
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'signin')) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Demo credentials or database check
    if (empty($email) || empty($password)) {
        $error_message = 'Email and password are required.';
    } else if ($email === 'jbfaner8@gmail.com' && $password === 'part123') {
        // Test credentials match - generate MFA code
        $mfa_code = sprintf('%06d', rand(0, 999999));
        $_SESSION['mfa_code'] = $mfa_code;
        $_SESSION['temp_email'] = $email;
        $_SESSION['mfa_pending'] = true;
        
        // Send MFA code via email
        EmailHelper::sendMFACode($email, $mfa_code);
        
        header("Location: login.php?mfa=1");
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
        $_SESSION['participant_id'] = 'part_' . md5($_SESSION['temp_email']);
        $_SESSION['participant_email'] = $_SESSION['temp_email'];
        $_SESSION['participant_name'] = 'Participant'; // Default name
        $_SESSION['login_attempts'] = 0;
        
        unset($_SESSION['temp_email']);
        unset($_SESSION['mfa_code']);
        unset($_SESSION['mfa_pending']);
        
        header("Location: ../part-index.php");
        exit;
    } else {
        $error_message = 'Invalid MFA code. Please try again.';
    }
}

// Handle verification code submission
if (isset($_POST['verify_code'])) {
    $submitted_code = isset($_POST['verification_code']) ? trim($_POST['verification_code']) : '';
    
    if (isset($_SESSION['signup_data']) && $submitted_code === $_SESSION['signup_data']['verification_code']) {
        // Account verified - create session
        $_SESSION['participant_id'] = 'part_' . md5($_SESSION['signup_data']['email']);
        $_SESSION['participant_email'] = $_SESSION['signup_data']['email'];
        $_SESSION['participant_name'] = $_SESSION['signup_data']['name'];
        
        unset($_SESSION['signup_data']);
        unset($_SESSION['verification_pending']);
        unset($_SESSION['show_verification']);
        
        header("Location: ../part-index.php");
        exit;
    } else {
        $error_message = 'Invalid verification code. Please try again.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Disaster Preparedness Training</title>
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">
    <link rel="stylesheet" href="css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
</head>
<body>
    <!-- Background Decorations -->
    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">
                    <img src="images/logo.svg" alt="Logo" class="logo-img">
                </div>
                <h1><?php echo $is_signup || isset($_SESSION['show_verification']) ? 'Create Account' : 'Login'; ?></h1>
                <p class="subtitle">Disaster Preparedness Training & Simulation</p>
            </div>

            <?php if ($error_message): ?>
                <div class="error-message">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <!-- MFA Form -->
            <?php if ($show_mfa): ?>
                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label for="mfa_code">Verification Code</label>
                        <p class="form-hint">Enter the 6-digit code sent to your email</p>
                        <input type="text" id="mfa_code" name="mfa_code" placeholder="000000" maxlength="6" required autofocus inputmode="numeric">
                    </div>
                    <button type="submit" class="btn-login">Verify Code</button>
                    <p class="form-switch">
                        <a href="login.php">Back to Login</a>
                    </p>
                </form>

            <!-- Verification Form -->
            <?php elseif (isset($_SESSION['show_verification'])): ?>
                <form method="POST" class="login-form">
                    <div class="form-group">
                        <label for="verification_code">Verification Code</label>
                        <p class="form-hint">A 6-digit code has been sent to<br><strong><?php echo htmlspecialchars($verification_email); ?></strong></p>
                        <input type="text" id="verification_code" name="verification_code" placeholder="000000" maxlength="6" required autofocus inputmode="numeric">
                    </div>
                    <button type="submit" name="verify_code" value="1" class="btn-login">Verify Email</button>
                    <p class="form-switch">
                        <a href="login.php">Back to Login</a>
                    </p>
                </form>

            <!-- Sign Up Form -->
            <?php elseif ($is_signup): ?>
                <form method="POST" class="login-form">
                    <input type="hidden" name="action" value="signup">
                    
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="signup-email">Email Address</label>
                        <input type="email" id="signup-email" name="email" placeholder="your@email.com" required>
                    </div>

                    <div class="form-group">
                        <label for="signup-password">Password</label>
                        <input type="password" id="signup-password" name="password" placeholder="At least 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required>
                    </div>

                    <button type="submit" class="btn-login">Create Account</button>
                    
                    <p class="form-switch">
                        Already have an account? <a href="login.php">Login here</a>
                    </p>
                </form>

            <!-- Sign In Form (Default) -->
            <?php else: ?>
                <form method="POST" class="login-form">
                    <input type="hidden" name="action" value="signin">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-login">Login</button>

                    <p class="form-switch">
                        Don't have an account? <a href="login.php?signup=1">Sign up here</a>
                    </p>
                </form>

                <div class="demo-credentials">
                    <p><strong>Demo Credentials:</strong></p>
                    <p>Email: jbfaner8@gmail.com</p>
                    <p>Password: part123</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>
</html>
