<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f6fa;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            padding: 30px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 32px 24px;
            line-height: 1.6;
        }
        .btn-reset {
            display: inline-block;
            background-color: #4f46e5;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-weight: 600;
            margin: 20px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Money Manage</h1>
            <p style="margin: 6px 0 0 0; opacity: 0.9; font-size: 14px;">Password Reset Request</p>
        </div>
        <div class="content">
            <h2 style="margin-top: 0; color: #1e293b; font-size: 18px;">Hello {{ $userName }},</h2>
            <p>You recently requested to reset your password for your Money Manage account. Click the button below to proceed:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="btn-reset" target="_blank">Reset Password</a>
            </div>

            <p style="color: #64748b; font-size: 13px;">
                This password reset link will expire in 60 minutes. If you did not request a password reset, please ignore this email.
            </p>
            <p style="word-break: break-all; color: #94a3b8; font-size: 12px;">
                Button not working? Copy and paste this link into your browser:<br>
                <a href="{{ $resetUrl }}" style="color: #4f46e5;">{{ $resetUrl }}</a>
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Money Manage. All rights reserved.
        </div>
    </div>
</body>
</html>
