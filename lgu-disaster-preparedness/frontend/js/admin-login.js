/* Admin Login JavaScript with SweetAlert2 */

/**
 * Toggle password visibility
 */
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
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
                // Could auto-submit here
                console.log('MFA code complete: ' + this.value);
            }
        });
    }

    // Prevent form submission if account is locked
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email');
            if (emailInput.disabled) {
                e.preventDefault();
                Swal.fire({
                    title: 'Account Locked',
                    text: 'Too many failed login attempts. Please try again in 15 minutes.',
                    icon: 'error',
                    confirmButtonColor: '#3a7675'
                });
            }
        });
    }

    // Add enter key support for MFA
    if (mfaInput) {
        mfaInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.length === 6) {
                this.form.submit();
            }
        });
    }

    // Demo credentials hint (for development only)
    const emailInput = document.getElementById('email');
    if (emailInput) {
        // Uncomment for development: emailInput.value = 'jbemployee50@gmail.com';
    }
});

/**
 * Simulate CAPTCHA verification (replace with actual reCAPTCHA)
 */
function verifyCaptcha(token) {
    console.log('CAPTCHA verified with token:', token);
    document.querySelector('input[name="captcha_token"]').value = token;
}

/**
 * Handle "Remember this device" functionality
 */
function handleRememberDevice() {
    const rememberCheckbox = document.getElementById('remember');
    if (rememberCheckbox && rememberCheckbox.checked) {
        // Store a device token in localStorage for future convenience
        const deviceToken = generateDeviceToken();
        localStorage.setItem('device_token', deviceToken);
        localStorage.setItem('remembered_email', document.getElementById('email').value);
        console.log('Device remembered');
    }
}

/**
 * Generate a unique device token
 */
function generateDeviceToken() {
    return 'dev_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
}

/**
 * Pre-fill email if device was remembered
 */
document.addEventListener('DOMContentLoaded', function() {
    const deviceToken = localStorage.getItem('device_token');
    const rememberedEmail = localStorage.getItem('remembered_email');
    
    if (deviceToken && rememberedEmail) {
        const emailInput = document.getElementById('email');
        const rememberCheckbox = document.getElementById('remember');
        
        if (emailInput) {
            emailInput.value = rememberedEmail;
            emailInput.focus();
        }
        
        if (rememberCheckbox) {
            rememberCheckbox.checked = true;
        }
    }
});

/**
 * Add client-side validation
 */
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            // Basic validation
            if (!email || !password) {
                e.preventDefault();
                Swal.fire({
                    title: 'Missing Fields',
                    text: 'Please fill in all fields',
                    icon: 'warning',
                    confirmButtonColor: '#3a7675'
                });
                return false;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                Swal.fire({
                    title: 'Invalid Email',
                    text: 'Please enter a valid email address',
                    icon: 'error',
                    confirmButtonColor: '#3a7675'
                });
                return false;
            }
            
            // Password length check
            if (password.length < 6) {
                e.preventDefault();
                Swal.fire({
                    title: 'Invalid Password',
                    text: 'Password must be at least 6 characters',
                    icon: 'error',
                    confirmButtonColor: '#3a7675'
                });
                return false;
            }
        });
    }
});

/**
 * Show error message helper
 */
function showErrorMessage(message) {
    // Create or update error message element
    let errorDiv = document.querySelector('.error-message');
    
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        const form = document.getElementById('loginForm') || document.getElementById('mfaForm');
        form.insertBefore(errorDiv, form.firstChild);
    }
    
    errorDiv.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
        <span>${message}</span>
    `;
    
    // Scroll to error
    errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Add loading state to submit button
 */
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.login-form, #mfaForm');
    
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Please wait...';
                submitBtn.style.opacity = '0.6';
            }
        });
    });
});

// Session timeout warning (optional)
let sessionTimeout = null;

function startSessionTimeout() {
    // Warn user after 25 minutes of inactivity, logout after 30 minutes
    sessionTimeout = setTimeout(() => {
        Swal.fire({
            title: 'Session Expiring',
            text: 'Your session will expire in 5 minutes. Do you want to continue?',
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
    // Only start timeout if user is on MFA page
    if (document.querySelector('.mfa-header')) {
        startSessionTimeout();
    }
});
