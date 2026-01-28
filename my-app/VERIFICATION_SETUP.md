# Email & SMS Verification Setup Guide

## Email Configuration

### For Development (Log Mode)
By default, emails are logged to `storage/logs/laravel.log`. No configuration needed.

### For Production (SMTP)
Add these to your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # or your SMTP server
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Disaster Preparedness Training"
```

### Other Email Providers

**Mailgun:**
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-mailgun-secret
```

**Postmark:**
```env
MAIL_MAILER=postmark
POSTMARK_API_KEY=your-postmark-key
```

**AWS SES:**
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
```

## SMS Configuration

### For Development (Log Mode)
By default, SMS messages are logged to `storage/logs/laravel.log`. No configuration needed.

### Twilio Setup
1. Sign up at [Twilio.com](https://www.twilio.com)
2. Get your Account SID, Auth Token, and Phone Number
3. Add to `.env`:

```env
SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=your_account_sid
TWILIO_AUTH_TOKEN=your_auth_token
TWILIO_FROM_NUMBER=+1234567890  # Your Twilio phone number
```

### Vonage (Nexmo) Setup
1. Sign up at [Vonage.com](https://www.vonage.com)
2. Get your API Key and API Secret
3. Add to `.env`:

```env
SMS_PROVIDER=vonage
VONAGE_API_KEY=your_api_key
VONAGE_API_SECRET=your_api_secret
VONAGE_FROM_NUMBER=YourBrandName  # or phone number
```

## Testing

### Test Email Verification
1. Register with an email address
2. Check your email inbox (or logs if in log mode)
3. Click the verification link

### Test SMS Verification
1. Register with a phone number
2. Check your phone for the OTP code (or logs if in log mode)
3. Enter the 6-digit code on the verification page

## Troubleshooting

### Emails not sending?
- Check `storage/logs/laravel.log` for errors
- Verify `.env` mail configuration
- Test SMTP credentials with a mail client
- Check spam folder

### SMS not sending?
- Check `storage/logs/laravel.log` for errors
- Verify `.env` SMS provider credentials
- Ensure phone number format is correct (include country code, e.g., +1234567890)
- Check Twilio/Vonage account balance

### Both fallback to log mode?
- If credentials are missing or invalid, the system automatically falls back to logging
- Check logs for detailed error messages

