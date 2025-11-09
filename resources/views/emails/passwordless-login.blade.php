@php
    $template = $template ?? [];
    $templateVariables = $templateVariables ?? [];
    $loginUrl = $loginUrl ?? ($templateVariables['login_url'] ?? '');
    $expiresAt = $expiresAt ?? null;
    $appName = config('app.name', 'Conference Management System');

    $defaultHeading = $templateVariables['email_heading'] ?? '🎉 Welcome to Your Conference Dashboard';
    $ctaLabel = $templateVariables['email_cta_label'] ?? 'Access My Dashboard';
    $ctaEmoji = $templateVariables['email_cta_emoji'] ?? '🚀';

    $defaultBodySections = [
        '<p>You have been granted access to your conference dashboard! Use the secure button below to sign in without a password.</p>',
    ];

    if (isset($conference) && $conference) {
        $conferenceDetails = '<div style="background-color:#f8f9fa;border-left:4px solid #667eea;padding:15px;margin:20px 0;border-radius:0 5px 5px 0;">';
        $conferenceDetails .= '<h3 style="margin-top:0;color:#667eea;">📅 Conference Details</h3>';
        $conferenceDetails .= '<p><strong>Conference:</strong> ' . e($conference->name) . '</p>';

        if (!empty($conference->start_date)) {
            $conferenceDetails .= '<p><strong>Date:</strong> ' . \Carbon\Carbon::parse($conference->start_date)->format('F j, Y') . '</p>';
        }

        if (optional($conference->venue)->name) {
            $conferenceDetails .= '<p><strong>Venue:</strong> ' . e($conference->venue->name) . '</p>';
        }

        $conferenceDetails .= '</div>';
        $defaultBodySections[] = $conferenceDetails;
    }

    $defaultBodySections[] = '<div style="background-color:#fff3cd;border:1px solid #ffeaa7;border-radius:5px;padding:15px;margin:20px 0;color:#856404;">
        <h4 style="margin-top:0;">🔒 Security Notice</h4>
        <ul style="margin:0;padding-left:20px;">
            <li>This link is unique to you and should not be shared.</li>
            <li>It will expire automatically for your security.</li>
            <li>If you didn\'t request this access, please ignore this email.</li>
        </ul>
    </div>';

    if ($expiresAt) {
        $defaultBodySections[] = '<div style="background-color:#d1ecf1;border:1px solid #bee5eb;border-radius:5px;padding:15px;margin:20px 0;color:#0c5460;">
            <h4 style="margin-top:0;">⏰ Link Expiration</h4>
            <p style="margin:0;">This login link will expire on <strong>' . $expiresAt->timezone(config('app.timezone', 'UTC'))->format('F j, Y \a\t g:i A T') . '</strong>.</p>
        </div>';
    }

    $defaultBodySections[] = '<p>If the button above doesn\'t work, copy and paste this link into your browser:</p>
        <p style="word-break:break-all;background-color:#f8f9fa;padding:10px;border-radius:5px;font-family:monospace;">' . e($loginUrl) . '</p>';

    $defaultBodySections[] = '<p>Once logged in, you can:</p>
        <ul>
            <li>Review your personalised conference schedule</li>
            <li>Access documents and resources</li>
            <li>Update your profile and preferences</li>
            <li>Connect with fellow participants</li>
        </ul>';

    $defaultBody = implode("\n", $defaultBodySections);

    $defaults = [
        'subject' => 'Your Conference Dashboard Access',
        'greeting' => 'Hello ' . e(trim(($templateVariables['full_name'] ?? ($user->first_name ?? '') . ' ' . ($user->last_name ?? '')))) . ',',
        'body' => $defaultBody,
        'closing' => 'Best regards,',
        'signature' => e($templateVariables['signature'] ?? $appName),
    ];

    foreach ($defaults as $key => $value) {
        if (!array_key_exists($key, $template) || $template[$key] === null) {
            $template[$key] = $value;
        }
    }

    $greetingContainsHtml = \Illuminate\Support\Str::contains($template['greeting'], ['<', '>']);
    $bodyContainsHtml = \Illuminate\Support\Str::contains($template['body'], ['<', '>']);
    $closingContainsHtml = \Illuminate\Support\Str::contains($template['closing'], ['<', '>']);
    $signatureContainsHtml = \Illuminate\Support\Str::contains($template['signature'], ['<', '>']);
    $bodyIncludesLoginLink = $loginUrl && \Illuminate\Support\Str::contains($template['body'], $loginUrl);
@endphp

@if(!empty($useCustomLayout))
{!! $template['body'] !!}
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template['subject'] ?? 'Your Conference Dashboard Access' }}</title>
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
        .cta-button {
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
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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
            .cta-button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $defaultHeading }}</h1>
        </div>

        <div class="content">
            <div class="welcome-message">
                {!! $greetingContainsHtml ? $template['greeting'] : e($template['greeting']) !!}
            </div>

            <div>
                {!! $bodyContainsHtml ? $template['body'] : nl2br(e($template['body'])) !!}
            </div>

            @if($loginUrl && !$bodyIncludesLoginLink)
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $loginUrl }}" class="cta-button">
                        {{ $ctaEmoji }} {{ $ctaLabel }}
                    </a>
                </div>
            @endif

            <div style="margin-top: 30px;">
                @if(!empty($template['closing']))
                    <p>{!! $closingContainsHtml ? $template['closing'] : e($template['closing']) !!}</p>
                @endif

                @if(!empty($template['signature']))
                    <p>{!! $signatureContainsHtml ? $template['signature'] : e($template['signature']) !!}</p>
                @endif
            </div>
        </div>

        <div class="footer">
            <p>If you have any questions or need assistance, please don't hesitate to contact us.</p>
            <p>
                <a href="mailto:{{ config('mail.from.address', 'support@example.com') }}">📧 {{ config('mail.from.address', 'support@example.com') }}</a>
            </p>
            <div class="divider"></div>
            <p style="font-size: 12px; color: #adb5bd;">
                This email was sent to {{ $user->email }}. If you believe you received this email in error, please contact our support team.
            </p>
        </div>
    </div>
</body>
</html>
@endif
