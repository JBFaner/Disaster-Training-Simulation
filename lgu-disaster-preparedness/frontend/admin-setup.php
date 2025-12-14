<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    // Destroy all session data
    session_destroy();
    // Clear all session variables
    $_SESSION = array();
    // Redirect to login
    header("Location: admin-login.php");
    exit;
}

// Check if user is authenticated and on first login
$is_first_time_setup = isset($_SESSION['first_login']) && $_SESSION['first_login'] === true;
$has_invitation_link = isset($_GET['token']) && !empty($_GET['token']);

// Redirect if not first time setup and no invitation
if (!$is_first_time_setup && !$has_invitation_link) {
    header("Location: admin-login.php");
    exit;
}

// Validate invitation token (if applicable)
$invitation_data = null;
$error_message = '';
$success_message = '';

if ($has_invitation_link) {
    $token = isset($_GET['token']) ? trim($_GET['token']) : '';
    
    // In a real application, validate this against a database
    // For demo purposes, we'll use a simple validation
    $invitation_data = validateInvitationToken($token);
    
    if (!$invitation_data) {
        $error_message = 'Invalid or expired invitation link. Please contact your administrator.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'setup_password') {
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
        
        // Validation
        $validation_errors = validatePassword($password, $confirm_password);
        
        if (empty($validation_errors)) {
            if ($password !== $confirm_password) {
                $error_message = 'Passwords do not match.';
            } else {
                // Store password temporarily for later
                $_SESSION['temp_password'] = $password;
                $_SESSION['setup_stage'] = 'mfa';
                // Redirect to same page to show MFA setup
                header("Location: admin-setup.php" . ($has_invitation_link ? "?token=" . $_GET['token'] : ""));
                exit;
            }
        } else {
            $error_message = implode('<br>', $validation_errors);
        }
    } elseif ($action === 'setup_mfa') {
        $mfa_method = isset($_POST['mfa_method']) ? $_POST['mfa_method'] : 'authenticator';
        
        if ($mfa_method === 'authenticator') {
            // Generate and send MFA verification code via email
            $mfa_code = sprintf('%06d', rand(0, 999999));
            $_SESSION['setup_mfa_code'] = $mfa_code;
            $_SESSION['setup_stage'] = 'mfa_verify';
            
            // Send verification code via email
            require_once __DIR__ . '/../api/EmailHelper.php';
            $email = $_SESSION['admin_email'] ?? $invitation_data['email'] ?? '';
            if (!empty($email)) {
                EmailHelper::sendMFACode($email, $mfa_code);
            }
            
            header("Location: admin-setup.php" . ($has_invitation_link ? "?token=" . $_GET['token'] : ""));
            exit;
        }
    } elseif ($action === 'verify_mfa') {
        $mfa_code = isset($_POST['mfa_code']) ? $_POST['mfa_code'] : '';
        
        // Verify MFA code against the one sent via email
        if (isset($_SESSION['setup_mfa_code']) && $_SESSION['setup_mfa_code'] === $mfa_code) {
            // Setup complete - activate account
            $_SESSION['setup_complete'] = true;
            
            // In a real application, update the database here
            // to mark the account as active
            
            unset($_SESSION['temp_password']);
            unset($_SESSION['setup_stage']);
            unset($_SESSION['setup_mfa_code']);
            unset($_SESSION['first_login']);
            
            $_SESSION['setup_success'] = true;
            header("Location: index.php");
            exit;
        } else {
            $error_message = 'Invalid MFA code. Please check your email for the correct code.';
        }
    }
}

// Determine current setup stage
$setup_stage = isset($_SESSION['setup_stage']) ? $_SESSION['setup_stage'] : 'password';
if ($has_invitation_link && !isset($_SESSION['setup_stage'])) {
    $setup_stage = 'password';
}

// Generate QR code for MFA (using a library like phpqrcode would be ideal)
$qr_code_url = generateMFAQRCode('LGUDP', $_SESSION['admin_email'] ?? 'employee@lgu.gov', 'secret_key_here');

/**
 * Validate invitation token
 */
function validateInvitationToken($token) {
    // In a real application, check this against the database
    // For demo, we'll create mock data
    
    // Mock token format: base64 encoded email and timestamp
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
    
    // Return invitation data
    return [
        'email' => $email,
        'name' => 'John Doe', // In real app, fetch from database
        'created_at' => $timestamp,
        'expires_at' => $expiry_time
    ];
}

/**
 * Validate password
 */
function validatePassword($password, $confirm) {
    $errors = [];
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } else {
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
            $errors[] = 'Password must contain at least one special character (!@#$%^&*).';
        }
    }
    
    if (empty($confirm)) {
        $errors[] = 'Please confirm your password.';
    }
    
    return $errors;
}

/**
 * Generate MFA QR code URL (using qr-server.com API for demo)
 */
function generateMFAQRCode($issuer, $email, $secret) {
    $label = urlencode("$issuer ($email)");
    $otpauth_url = "otpauth://totp/$label?secret=$secret&issuer=" . urlencode($issuer);
    
    // Use QR code API
    return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($otpauth_url);
}

// Get employee email
$employee_email = $_SESSION['admin_email'] ?? ($invitation_data['email'] ?? '');
$employee_name = $_SESSION['admin_name'] ?? ($invitation_data['name'] ?? 'Employee');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Setup - Disaster Preparedness</title>
    <link rel="stylesheet" href="css/admin-setup.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body>
    <div class="setup-container">
        <div class="setup-card">
            <!-- Header -->
            <div class="setup-header">
                <img src="images/logo.svg" alt="LGU Logo" class="setup-logo">
                <h1>Complete Your Setup</h1>
                <p class="subtitle">Welcome to the Employee Portal</p>
                <button onclick="confirmLogout()" class="logout-link" type="button" title="Logout">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h5M17 16l4-4m0 0l-4-4m4 4H9"></path>
                    </svg>
                </button>
            </div>

            <!-- Progress Indicator -->
            <div class="progress-container">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo ($setup_stage === 'password' ? 33 : ($setup_stage === 'mfa' || $setup_stage === 'mfa_verify' ? 66 : 100)) . '%'; ?>"></div>
                </div>
                <div class="progress-labels">
                    <div class="progress-label <?php echo ($setup_stage === 'password' ? 'active' : 'done'); ?>">
                        <span class="progress-step">1</span>
                        <span class="progress-text">Password</span>
                    </div>
                    <div class="progress-label <?php echo ($setup_stage === 'mfa' || $setup_stage === 'mfa_verify' ? 'active' : ($setup_stage !== 'password' ? 'done' : '')); ?>">
                        <span class="progress-step">2</span>
                        <span class="progress-text">MFA Setup</span>
                    </div>
                    <div class="progress-label <?php echo ($setup_stage === 'mfa_verify' && isset($_POST['verify_mfa']) ? 'done' : ''); ?>">
                        <span class="progress-step">3</span>
                        <span class="progress-text">Complete</span>
                    </div>
                </div>
            </div>

            <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                </svg>
                <span><?php echo $error_message; ?></span>
            </div>
            <?php endif; ?>

            <!-- Employee Information Display -->
            <div class="employee-info">
                <h3>Employee Information</h3>
                <div class="info-grid">
                    <div class="info-field">
                        <label>Full Name</label>
                        <p class="info-value"><?php echo htmlspecialchars($employee_name); ?></p>
                    </div>
                    <div class="info-field">
                        <label>Company Email</label>
                        <p class="info-value"><?php echo htmlspecialchars($employee_email); ?></p>
                    </div>
                </div>
            </div>

            <!-- Stage 1: Password Setup -->
            <?php if ($setup_stage === 'password'): ?>
            <form method="POST" class="setup-form" id="passwordForm">
                <input type="hidden" name="action" value="setup_password">
                
                <h2>Create Your Password</h2>
                <p class="stage-subtitle">Choose a strong password that meets all the requirements below</p>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Enter a strong password"
                        required
                    >
                    <div class="password-requirements">
                        <h4>Password Requirements</h4>
                        <ul>
                            <li class="requirement" data-requirement="length">
                                <span class="requirement-check">✓</span>
                                At least 12 characters
                            </li>
                            <li class="requirement" data-requirement="uppercase">
                                <span class="requirement-check">✓</span>
                                One uppercase letter (A-Z)
                            </li>
                            <li class="requirement" data-requirement="lowercase">
                                <span class="requirement-check">✓</span>
                                One lowercase letter (a-z)
                            </li>
                            <li class="requirement" data-requirement="number">
                                <span class="requirement-check">✓</span>
                                One number (0-9)
                            </li>
                            <li class="requirement" data-requirement="special">
                                <span class="requirement-check">✓</span>
                                One special character (!@#$%^&*)
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-input" 
                        placeholder="Re-enter your password"
                        required
                    >
                    <span class="password-match-indicator" id="matchIndicator"></span>
                </div>

                <button type="submit" class="btn-next">Continue to MFA Setup</button>
            </form>

            <!-- Stage 2: MFA Setup -->
            <?php elseif ($setup_stage === 'mfa'): ?>
            <form method="POST" class="setup-form" id="mfaSetupForm">
                <input type="hidden" name="action" value="setup_mfa">
                
                <h2>Set Up Multi-Factor Authentication</h2>
                <p class="stage-subtitle">Secure your account with an authenticator app</p>

                <div class="mfa-method">
                    <div class="mfa-option selected" onclick="selectMFAMethod(this, 'authenticator')">
                        <input type="radio" name="mfa_method" value="authenticator" checked>
                        <div class="mfa-option-content">
                            <h3>Authenticator App</h3>
                            <p>Use an app like Google Authenticator, Microsoft Authenticator, or Authy</p>
                        </div>
                    </div>
                </div>

                <div class="mfa-instructions">
                    <h3>Setup Instructions</h3>
                    <ol>
                        <li>Download an authenticator app (Google Authenticator, Microsoft Authenticator, or Authy)</li>
                        <li>Open the app and select "Add" or "+" to add a new account</li>
                        <li>Choose "Scan QR code" or "Enter a setup key"</li>
                        <li>Scan the QR code below or enter the setup key</li>
                        <li>Click "Continue" to verify your setup</li>
                    </ol>
                </div>

                <div class="qr-code-container">
                    <h4>Scan This QR Code</h4>
                    <img src="<?php echo $qr_code_url; ?>" alt="MFA QR Code" class="qr-code">
                    <p class="qr-note">Can't scan? Enter this setup key manually:</p>
                    <code class="setup-key">JBQQ-3FIQ-ROBA-5UQA-7FGW</code>
                    <button type="button" class="btn-copy" onclick="copySetupKey()">Copy Setup Key</button>
                </div>

                <button type="submit" class="btn-next">Continue to Verification</button>
            </form>

            <!-- Stage 3: MFA Verification -->
            <?php elseif ($setup_stage === 'mfa_verify'): ?>
            <form method="POST" class="setup-form" id="mfaVerifyForm">
                <input type="hidden" name="action" value="verify_mfa">
                
                <h2>Verify Your Setup</h2>
                <p class="stage-subtitle">Enter the 6-digit verification code sent to your email</p>

                <div class="form-group">
                    <label for="mfa_code">Verification Code</label>
                    <input 
                        type="text" 
                        id="mfa_code" 
                        name="mfa_code" 
                        class="form-input mfa-code-input" 
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        required
                        autofocus
                    >
                    <p class="mfa-code-hint">Check your email inbox for the 6-digit code we just sent you</p>
                </div>

                <div class="recovery-codes-note">
                    <strong>🔑 Where to Find Your Code</strong>
                    <p><strong>📧 Check your email:</strong> We just sent a 6-digit verification code to your registered email address.</p>
                    <p><strong>💾 If using local server:</strong> Check the code in <code>/api/logs/mfa_codes.log</code> file.</p>
                    <p>Enter the code above to complete your setup.</p>
                </div>

                <div class="recovery-codes-note" style="margin-top: 20px;">
                    <strong>Save Your Recovery Codes</strong>
                    <p>If you lose access to your email, you can use these recovery codes to regain access:</p>
                    <div class="recovery-codes">
                        <code>2B4K-9HJF-3DL2</code>
                        <code>7QWE-5RNM-6XC8</code>
                        <code>4DFG-2HJK-8PQW</code>
                        <code>9YUI-3CVB-7NML</code>
                    </div>
                    <button type="button" class="btn-copy" onclick="copyRecoveryCodes()">Copy Recovery Codes</button>
                </div>

                <button type="submit" class="btn-complete">Complete Setup</button>
            </form>
            <?php endif; ?>

            <!-- Footer -->
            <div class="setup-footer">
                <p>Need help? Contact your administrator or <a href="#">view support documentation</a></p>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
    </div>

    <script src="js/admin-setup.js"></script>
    
    <script>
    function confirmLogout() {
        Swal.fire({
            title: 'Confirm Logout',
            text: 'Are you sure you want to logout? You will need to log in again to access the system.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c33',
            cancelButtonColor: '#999',
            confirmButtonText: 'Yes, Logout',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'admin-setup.php?logout=1';
            }
        });
    }
    </script>
</html>
