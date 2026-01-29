# Employee/Admin Login System - Implementation Guide

## Overview

This is a secure employee portal login system with two-factor authentication (MFA), designed for the LGU Disaster Preparedness Management System.

## Files Created

### Frontend Files

1. **admin-login.php** - Employee portal login page
   - Email and password authentication
   - Password show/hide toggle
   - "Remember this device" functionality
   - Rate limiting (5 attempts per 15 minutes)
   - CAPTCHA triggering after 3 failed attempts
   - MFA challenge after successful password entry
   - Inline error messages

2. **admin-setup.php** - First-time setup/account activation
   - Accessible via secure invitation link
   - Three-stage setup process:
     - Stage 1: Password creation with strength requirements
     - Stage 2: MFA setup (Authenticator app)
     - Stage 3: MFA code verification
   - Progress indicator
   - Recovery codes generation
   - Pre-filled employee information

3. **css/admin-login.css** - Login page styling
   - Modern gradient design
   - Responsive layout
   - Dark mode support
   - Accessibility features

4. **css/admin-setup.css** - Setup page styling
   - Multi-stage form styling
   - Progress indicator
   - QR code display area
   - Recovery codes display
   - Responsive design

5. **js/admin-login.js** - Login page functionality
   - Password visibility toggle
   - MFA code formatting
   - Device memory functionality
   - Client-side validation
   - Form submission handling

6. **js/admin-setup.js** - Setup page functionality
   - Real-time password validation
   - Password requirement checking
   - MFA input formatting
   - Copy-to-clipboard for setup keys and recovery codes
   - Session timeout handling

### Backend Files

7. **api/AuthenticationHelper.php** - Helper class for authentication
   - Password validation
   - TOTP/MFA code generation and verification
   - QR code generation
   - Recovery code generation
   - Session management
   - Rate limiting checks
   - Authentication logging

## Test Credentials

**Test Login Credentials:**
- Email: `jbemployee50@gmail.com`
- Password: `admin123`

**First-Time Login Flow:**
1. Login with test credentials
2. Enter any 6-digit code for MFA verification
3. Redirects to admin-setup.php for first-time setup

## Security Features

### Login Page

1. **Rate Limiting**
   - Maximum 5 failed attempts per 15 minutes
   - Account temporary lock after limit reached
   - Countdown timer (demo shows message)

2. **CAPTCHA**
   - Triggered after 3 failed login attempts
   - Prevents automated attacks
   - Uses reCAPTCHA API (configure in production)

3. **Multi-Factor Authentication**
   - Required after successful password entry
   - Authenticator app integration
   - 6-digit time-based code

4. **Session Management**
   - Secure session handling
   - Session timeout after 30 minutes
   - Device memory option

5. **Password Features**
   - Show/hide toggle
   - Client-side validation
   - Server-side verification

### Setup Page

1. **Invitation Link Validation**
   - One-time use (implement in database)
   - Time-limited expiration (72 hours)
   - Secure token generation

2. **Password Requirements**
   - Minimum 12 characters
   - Uppercase letter required
   - Lowercase letter required
   - Number required
   - Special character required
   - Real-time validation with visual feedback

3. **MFA Setup**
   - QR code for authenticator app
   - Manual setup key option
   - Recovery codes (8 codes)
   - Code verification before account activation

4. **Account Activation**
   - Account inactive until setup complete
   - Secure verification process
   - Audit logging

## Database Schema (Recommended)

```sql
-- Employees table
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255),
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Employee invitations table
CREATE TABLE employee_invitations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- MFA settings table
CREATE TABLE mfa_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL UNIQUE,
    totp_secret VARCHAR(255),
    recovery_codes JSON,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- Authentication logs table
CREATE TABLE auth_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255),
    status ENUM('SUCCESS', 'FAILED'),
    reason VARCHAR(255),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email_time (email, created_at)
);
```

## Configuration Steps

### 1. Include Authentication Helper

In your PHP files, include the helper:

```php
require_once __DIR__ . '/../api/AuthenticationHelper.php';
```

### 2. Configure reCAPTCHA (Production)

1. Go to [Google reCAPTCHA Console](https://www.google.com/recaptcha/admin)
2. Create a new site for your domain
3. Add your site key to `admin-login.php`
4. Verify tokens on the server

### 3. Setup MFA (Authenticator Apps)

Supported apps:
- Google Authenticator
- Microsoft Authenticator
- Authy
- 1Password
- LastPass

### 4. Email Integration (Optional)

Extend `AuthenticationHelper.php` to send invitation emails:

```php
public static function sendInvitationEmail($email, $name, $token) {
    $invitation_url = "https://yourdomain.com/admin-setup.php?token=" . urlencode($token);
    // Send email with invitation link
}
```

## Usage Examples

### Check Session

```php
if (!AuthenticationHelper::isSessionValid()) {
    header("Location: admin-login.php");
    exit;
}
```

### Validate Password

```php
$result = AuthenticationHelper::validatePasswordStrength($password);
if (!$result['valid']) {
    // Show errors
    print_r($result['errors']);
}
```

### Create Employee Invitation

```php
$token = AuthenticationHelper::generateInvitationToken('employee@company.com');
// Store token in database
// Send invitation link with token
```

### Log Authentication

```php
AuthenticationHelper::logAuthAttempt('user@email.com', true);
// or
AuthenticationHelper::logAuthAttempt('user@email.com', false, 'Invalid password');
```

## Customization

### Colors and Branding

Edit CSS files to change colors:
- Login page: `css/admin-login.css`
- Setup page: `css/admin-setup.css`

Default color scheme:
- Primary: Purple/Pink gradient (#667eea to #764ba2)
- Success: Green (#10b981)
- Error: Red (#c33)

### Session Timeout

Edit timeout duration (currently 30 minutes):

```javascript
// In js/admin-login.js or js/admin-setup.js
sessionTimeout = setTimeout(() => {
    // Default: 25 * 60 * 1000 = 25 minutes
    // Change the multiplier as needed
}, 25 * 60 * 1000);
```

### Password Requirements

Modify in `api/AuthenticationHelper.php`:

```php
public static function validatePasswordStrength($password) {
    // Edit regex patterns and length requirements
}
```

## Production Checklist

- [ ] Configure database tables
- [ ] Setup reCAPTCHA keys
- [ ] Implement email sending for invitations
- [ ] Replace test credentials
- [ ] Setup HTTPS/SSL certificate
- [ ] Configure session storage (Redis recommended)
- [ ] Implement IP whitelisting (optional)
- [ ] Setup audit logging
- [ ] Configure backup/recovery procedures
- [ ] Test all security features
- [ ] Load testing for rate limiting
- [ ] Set up monitoring and alerts

## Troubleshooting

### MFA Not Working

1. Check server time synchronization (TOTP is time-based)
2. Verify AuthenticationHelper.php is in api/ folder
3. Check browser console for JavaScript errors

### Session Issues

1. Verify sessions folder has write permissions
2. Check PHP session settings in php.ini
3. Clear browser cookies and try again

### Password Validation Failing

1. Review `validatePasswordStrength()` in AuthenticationHelper.php
2. Check regex patterns for special characters
3. Ensure all requirements are met

## Support

For issues or questions, contact your system administrator or refer to the documentation in the api/AuthenticationHelper.php file.

## License

This code is part of the LGU Disaster Preparedness Management System and is proprietary.
