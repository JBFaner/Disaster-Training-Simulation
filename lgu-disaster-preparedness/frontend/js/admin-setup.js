/* Admin Setup JavaScript with SweetAlert2 */

/**
 * Real-time password validation
 */
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            validatePasswordRequirements(this.value);
        });
    }
    
    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            checkPasswordMatch();
        });
    }
});

/**
 * Validate password requirements in real-time
 */
function validatePasswordRequirements(password) {
    const requirements = {
        length: password.length >= 12,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // Update requirement indicators
    Object.keys(requirements).forEach(req => {
        const element = document.querySelector(`.requirement[data-requirement="${req}"]`);
        if (element) {
            if (requirements[req]) {
                element.classList.add('met');
            } else {
                element.classList.remove('met');
            }
        }
    });
    
    // Check if all requirements are met
    const allMet = Object.values(requirements).every(val => val === true);
    
    // Update confirm password validation
    const confirmInput = document.getElementById('confirm_password');
    if (confirmInput && confirmInput.value) {
        checkPasswordMatch();
    }
    
    return allMet;
}

/**
 * Check if passwords match
 */
function checkPasswordMatch() {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const indicator = document.getElementById('matchIndicator');
    
    if (!passwordInput || !confirmInput || !indicator) return;
    
    if (confirmInput.value === '') {
        indicator.className = 'password-match-indicator';
        indicator.textContent = '';
    } else if (passwordInput.value === confirmInput.value) {
        indicator.className = 'password-match-indicator match';
        indicator.textContent = '✓ Passwords match';
    } else {
        indicator.className = 'password-match-indicator no-match';
        indicator.textContent = '✗ Passwords do not match';
    }
}

/**
 * Select MFA method
 */
function selectMFAMethod(element, method) {
    // Remove selected class from all options
    document.querySelectorAll('.mfa-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    element.closest('.mfa-option').classList.add('selected');
    
    // Update radio button
    const radio = element.closest('.mfa-option').querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
    }
}

/**
 * Copy setup key to clipboard
 */
function copySetupKey() {
    const setupKey = document.querySelector('.setup-key');
    if (setupKey) {
        const text = setupKey.textContent;
        navigator.clipboard.writeText(text).then(() => {
            // Show success with SweetAlert2
            Swal.fire({
                title: 'Copied!',
                text: 'Setup key copied to clipboard',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }
}

/**
 * Copy recovery codes to clipboard
 */
function copyRecoveryCodes() {
    const codesContainer = document.querySelector('.recovery-codes');
    if (codesContainer) {
        const codes = Array.from(codesContainer.querySelectorAll('code'))
            .map(code => code.textContent)
            .join('\n');
        
        navigator.clipboard.writeText(codes).then(() => {
            // Show success with SweetAlert2
            Swal.fire({
                title: 'Copied!',
                text: 'Recovery codes copied to clipboard',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }
}

/**
 * Format MFA input to accept only numbers
 */
document.addEventListener('DOMContentLoaded', function() {
    const mfaInput = document.getElementById('mfa_code');
    if (mfaInput) {
        mfaInput.addEventListener('input', function(e) {
            // Only allow numbers
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits entered
            if (this.value.length === 6) {
                console.log('MFA code complete: ' + this.value);
            }
        });
        
        // Handle Enter key
        mfaInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.length === 6) {
                this.form.submit();
            }
        });
    }
});

/**
 * Add client-side form validation
 */
document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.getElementById('passwordForm');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validate password meets all requirements
            const requirements = {
                length: password.length >= 12,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };
            
            const allMet = Object.values(requirements).every(val => val === true);
            
            if (!allMet) {
                e.preventDefault();
                Swal.fire({
                    title: 'Password Requirements Not Met',
                    text: 'Please ensure your password meets all requirements',
                    icon: 'warning',
                    confirmButtonColor: '#3a7675'
                });
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    title: 'Passwords Do Not Match',
                    text: 'Please verify your passwords match',
                    icon: 'error',
                    confirmButtonColor: '#3a7675'
                });
                return false;
            }
        });
    }
});

/**
 * Handle form submission with loading state
 */
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.setup-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Processing...';
                
                // Re-enable after 2 seconds if not navigated away
                setTimeout(() => {
                    if (submitBtn.textContent === 'Processing...') {
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                        submitBtn.textContent = originalText;
                    }
                }, 2000);
            }
        });
    });
});

/**
 * Initialize MFA method selection (select first option by default)
 */
document.addEventListener('DOMContentLoaded', function() {
    const mfaOptions = document.querySelectorAll('.mfa-option');
    if (mfaOptions.length > 0) {
        mfaOptions[0].classList.add('selected');
    }
});

/**
 * Password strength indicator
 */
function getPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 12) strength++;
    if (password.length >= 16) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
    
    return {
        score: strength,
        level: strength <= 2 ? 'weak' : strength <= 4 ? 'fair' : 'strong'
    };
}

// Session timeout warning
let sessionTimeout = null;

function startSessionTimeout() {
    // Warn user after 25 minutes, logout after 30 minutes
    sessionTimeout = setTimeout(() => {
        Swal.fire({
            title: 'Session Expiring',
            text: 'Your session will expire in 5 minutes due to inactivity. Do you want to continue?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3a7675',
            cancelButtonColor: '#999',
            confirmButtonText: 'Yes, Continue',
            cancelButtonText: 'Logout'
        }).then((result) => {
            if (result.isConfirmed) {
                startSessionTimeout();
            } else {
                window.location.href = 'admin-login.php';
            }
        });
    }, 25 * 60 * 1000);
}

document.addEventListener('DOMContentLoaded', function() {
    // Start session timeout on setup page
    startSessionTimeout();
});

