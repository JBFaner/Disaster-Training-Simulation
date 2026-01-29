# MFA Verification Code - Where to Get It

## ✅ Problem Fixed!

The system now **sends you a verification code via email** during setup. No more confusion about where to get the code!

---

## How The Updated Setup Works

### **Stage 1: Set Password** ✅
- Enter and confirm your password
- Meet all password requirements (12+ chars, uppercase, lowercase, number, special char)

### **Stage 2: MFA Setup** ✅
- Choose authenticator app method
- System generates and **sends verification code to your email**
- You proceed to Stage 3

### **Stage 3: Verify Code** ✅
- Check your **email inbox** for the 6-digit code
- Enter the code you received
- Complete setup!

---

## Where to Find Your Verification Code

### **Option 1: Check Your Email** (Primary)
1. Open your email inbox
2. Look for email from the system
3. Find the **6-digit code** in the email
4. Enter it in the verification form

### **Option 2: Check the Log File** (For Local Testing)
If email is not configured on your local server:

1. Open file: `frontend/api/logs/mfa_codes.log`
2. Find the most recent entry
3. Copy the **6-digit code** from the log
4. Enter it in the verification form

**File location**: 
```
c:\xampp\htdocs\LGUCapstone\lgu-disaster-preparedness\frontend\api\logs\mfa_codes.log
```

---

## Complete Testing Flow

```
1. Login with email & password
   ↓
2. Enter MFA code from LOGIN email
   ↓
3. Go to Setup Page
   ↓
4. Stage 1: Set Password → Continue
   ↓
5. Stage 2: MFA Setup → System sends code to email → Continue
   ↓
6. Stage 3: Enter code from SETUP email → Complete Setup
   ↓
7. Welcome notification on index.php ✅
```

---

## Important Notes

✅ **During Login**: Code sent via email (or check log file)
✅ **During Setup**: Code sent via email (or check log file)
✅ **Code Format**: 6-digit random number (000000-999999)
✅ **Code Validity**: Code is session-based (cleared on logout)
✅ **Email Logs**: All codes logged to `/api/logs/mfa_codes.log` for development

---

## Troubleshooting

### Code not received?
1. **Check spam folder** in your email
2. **Check the log file**: `/frontend/api/logs/mfa_codes.log`
3. Make sure file exists and is writable

### Code says "Invalid"?
1. Make sure you copied it correctly (no spaces)
2. Make sure it's exactly 6 digits
3. Try again - a new code can be generated on next attempt

### Setup stuck on verification?
1. Logout completely using the logout button
2. Log in again from scratch
3. A new verification code will be sent

---

## FAQ

**Q: Do I need to download an authenticator app?**
> No! We now send the code via email. The authenticator app setup is now optional for future use.

**Q: What if I don't have email configured?**
> Check the `/api/logs/mfa_codes.log` file - all codes are logged there for development/testing.

**Q: Can I reuse the same code?**
> No, the code is valid only during that specific setup/login session. On logout, the code is cleared.

**Q: Can I skip MFA verification?**
> No, MFA is required for security. You must complete it to finish setup.

---

**Now you can complete the setup easily!** Check your email (or log file) for the verification code. 🚀
