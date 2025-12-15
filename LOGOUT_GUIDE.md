# How to Reset Session / Logout

## The Problem
Once you log in, the system redirects you to `index.php` and won't let you go back to the login form. This is because the app checks if you're already logged in and automatically redirects you.

## The Solution
We've added **Logout** functionality so you can easily reset your session and test the login flow again.

---

## How to Logout

### Option 1: Click Logout Button on Dashboard (index.php)
1. Once logged in, go to `index.php`
2. Look for the **"Logout"** link in the top-right navigation menu (in red)
3. Click it
4. You'll be automatically logged out and redirected to `admin-login.php`
5. Now you can test the login flow again!

### Option 2: Click Logout Button During Setup (admin-setup.php)
1. If you're on the setup page (`admin-setup.php`)
2. Look for the **red logout icon button** in the top-right corner of the setup card
3. Click it
4. You'll be logged out and redirected to `admin-login.php`

### Option 3: Direct URL (For Testing)
1. Visit any page with `?logout=1` parameter:
   - `admin-login.php?logout=1`
   - `admin-setup.php?logout=1`
   - `index.php?logout=1`
2. Your session will be cleared and you'll be redirected to login

---

## Login Flow Test

### First Time Login (Setup Required)
1. Click "Logout" link on `index.php`
2. Go to `admin-login.php`
3. Enter credentials:
   - **Email**: `jbemployee50@gmail.com`
   - **Password**: `admin123`
4. Get MFA code from email or log file
5. Enter MFA code
6. Complete 3-stage setup (Password → MFA Setup → Verify)
7. Get **welcome notification** on `index.php` ✅

### Subsequent Logins (No Setup)
1. Click "Logout" link on `index.php`
2. Go to `admin-login.php` again
3. Enter same credentials
4. Get MFA code and enter it
5. **Skip setup** and go directly to `index.php` with no welcome notification ✅

---

## Testing Checklist

- [x] **First Login**: Redirects to setup → Welcome notification on index.php
- [x] **Second Login**: Redirects directly to index.php (no setup, no notification)
- [x] **Logout**: Can access login page again
- [x] **Session Cleared**: All session variables removed on logout
- [x] **Multiple Tests**: Can login → logout → login again repeatedly

---

## What Happens When You Logout

When you click logout, the system:
1. ✅ Destroys the entire PHP session (`session_destroy()`)
2. ✅ Clears all session variables (`$_SESSION = array()`)
3. ✅ Redirects you to `admin-login.php`
4. ✅ Clears all authentication data
5. ✅ Resets the welcome notification flag (`$_SESSION['first_login']`)

Now you can test the login flow from the beginning!

---

## Quick Reference

| Action | Result |
|--------|--------|
| Log in (first time) | → Setup page → Welcome notification on index.php |
| Log in (second+ times) | → index.php (no setup needed) |
| Click "Logout" | → admin-login.php (session cleared) |
| Visit `?logout=1` | → admin-login.php (session cleared) |
| Not logged in & visit index.php | → Redirects to admin-login.php |

---

**Now you can easily test the entire authentication system from login to logout!** 🚀
