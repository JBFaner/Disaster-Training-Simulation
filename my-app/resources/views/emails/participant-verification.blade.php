<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #334155;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background-color: #3b82f6;
            border-radius: 12px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }
        h1 {
            color: #1e293b;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .subtitle {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }
        .content {
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            margin: 20px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .link-box {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            word-break: break-all;
            font-size: 12px;
            color: #475569;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin: 20px 0;
            font-size: 13px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">LGU</div>
            <h1>Verify Your Email</h1>
            <p class="subtitle">Disaster Preparedness Training & Simulation</p>
        </div>

        <div class="content">
            <p>Hello {{ $name }},</p>
            
            <p>Thank you for registering as a participant! To complete your registration, please verify your email address using the verification code below:</p>

            <div style="text-align: center; margin: 30px 0;">
                <div style="display: inline-block; background-color: #f1f5f9; border: 2px solid #3b82f6; border-radius: 8px; padding: 20px 40px;">
                    <p style="margin: 0; font-size: 12px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Verification Code</p>
                    <p style="margin: 0; font-size: 32px; font-weight: bold; color: #3b82f6; font-family: monospace; letter-spacing: 4px;">{{ $verificationCode }}</p>
                </div>
            </div>

            <p>Enter this 6-digit code on the verification page to complete your registration.</p>

            <div class="warning">
                <strong>⚠️ Important:</strong> This verification code will expire in 15 minutes. If you didn't create an account, please ignore this email.
            </div>

            <p>If you need to access the verification page again, you can return to the registration page and continue from there.</p>
        </div>

        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Disaster Preparedness Training & Simulation System</p>
        </div>
    </div>
</body>
</html>

