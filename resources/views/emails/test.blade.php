<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
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
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 32px 24px;
            line-height: 1.6;
        }
        .badge {
            display: inline-block;
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            border-radius: 9999px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .info-card {
            background-color: #f8fafc;
            border-left: 4px solid #4f46e5;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
            font-size: 14px;
        }
        .info-card strong {
            color: #1e293b;
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
            <p>SMTP &amp; Queue Verification</p>
        </div>
        <div class="content">
            <div class="badge">SMTP SUCCESS</div>
            <h2 style="margin-top: 0; color: #1e293b; font-size: 20px;">{{ $title }}</h2>
            <p>{{ $bodyText }}</p>
            
            <div class="info-card">
                <p style="margin: 0 0 6px 0;"><strong>Recipient:</strong> bijon.ofc2021@gmail.com</p>
                <p style="margin: 0 0 6px 0;"><strong>Queue:</strong> default</p>
                <p style="margin: 0 0 6px 0;"><strong>Mailer:</strong> Brevo SMTP (smtp-relay.brevo.com)</p>
                <p style="margin: 0;"><strong>Dispatched At:</strong> {{ $sentAt }}</p>
            </div>

            <p style="color: #64748b; font-size: 13px;">
                If you received this message, your background mail queue and SMTP transport configuration are working perfectly.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Money Manage. All rights reserved.
        </div>
    </div>
</body>
</html>
