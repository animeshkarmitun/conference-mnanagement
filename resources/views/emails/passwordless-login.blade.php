<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Conference Dashboard Access</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .content {
            padding: 30px 20px;
        }
        .welcome-message {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .conference-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        .login-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s ease;
        }
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .security-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .expiration-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #0c5460;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 20px 0;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .header h1 {
                font-size: 24px;
            }
            .content {
                padding: 20px 15px;
            }
            .login-button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Welcome to Your Conference Dashboard</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="welcome-message">
                Hello <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>,
            </div>

            <p>You have been granted access to your conference dashboard! Click the button below to securely log in without needing a password.</p>

            @if($conference)
            <div class="conference-info">
                <h3 style="margin-top: 0; color: #667eea;">📅 Conference Details</h3>
                <p><strong>Conference:</strong> {{ $conference->name }}</p>
                @if($conference->start_date)
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($conference->start_date)->format('F j, Y') }}</p>
                @endif
                @if($conference->venue)
                <p><strong>Venue:</strong> {{ $conference->venue->name ?? 'TBA' }}</p>
                @endif
            </div>
            @endif

            <!-- Login Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $loginUrl }}" class="login-button">
                    🚀 Access My Dashboard
                </a>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <h4 style="margin-top: 0;">🔒 Security Notice</h4>
                <ul style="margin-bottom: 0;">
                    <li>This link is unique to you and should not be shared</li>
                    <li>It will expire automatically for your security</li>
                    <li>If you didn't request this access, please ignore this email</li>
                </ul>
            </div>

            <!-- Expiration Info -->
            <div class="expiration-info">
                <h4 style="margin-top: 0;">⏰ Link Expiration</h4>
                <p style="margin-bottom: 0;">
                    This login link will expire on <strong>{{ $expiresAt->format('F j, Y \a\t g:i A') }}</strong> 
                    ({{ $expiresAt->diffForHumans() }}).
                </p>
            </div>

            <div class="divider"></div>

            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace;">
                {{ $loginUrl }}
            </p>

            <p>Once logged in, you'll be able to:</p>
            <ul>
                <li>View your conference schedule</li>
                <li>Access conference materials</li>
                <li>Update your profile information</li>
                <li>Connect with other participants</li>
                <li>And much more!</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>
            <p>
                <a href="mailto:support@conference.com">📧 support@conference.com</a> | 
                <a href="tel:+1234567890">📞 +1 (234) 567-890</a>
            </p>
            <div class="divider"></div>
            <p style="font-size: 12px; color: #adb5bd;">
                This email was sent to {{ $user->email }}. If you believe you received this email in error, please contact our support team.
            </p>
        </div>
    </div>
</body>
</html>
