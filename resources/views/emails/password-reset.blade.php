<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password — UNCART</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #FAF5FC;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1E293B;
            line-height: 1.6;
        }
        .container {
            max-width: 560px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #1B0E24;
            padding: 32px 40px;
            text-align: center;
        }
        .logo-sparkle {
            color: #F59E0B;
            font-size: 24px;
            vertical-align: middle;
        }
        .logo-text {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-left: 6px;
            vertical-align: middle;
        }
        .logo-cart {
            color: #F59E0B;
        }
        .content {
            padding: 40px;
        }
        .badge {
            display: inline-block;
            background: #FAF5FC;
            color: #6B2A88;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 9999px;
            border: 1px solid #E5CEF1;
            margin-bottom: 16px;
        }
        h1 {
            font-size: 24px;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 16px;
            letter-spacing: -0.5px;
        }
        p {
            font-size: 14px;
            color: #475569;
            margin: 0 0 20px;
        }
        .button-wrapper {
            text-align: center;
            margin: 32px 0;
        }
        .button {
            display: inline-block;
            background: #1B0E24;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 14px 36px;
            border-radius: 9999px;
            box-shadow: 0 4px 12px rgba(27, 14, 36, 0.2);
        }
        .expiry-notice {
            background: #FFFDF5;
            border: 1px solid #FDEEC4;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 12px;
            color: #92400E;
            margin-bottom: 24px;
        }
        .fallback {
            border-top: 1px solid #F1F5F9;
            padding-top: 24px;
            font-size: 12px;
            color: #64748B;
            word-break: break-all;
        }
        .fallback a {
            color: #6B2A88;
            text-decoration: underline;
        }
        .footer {
            background-color: #F8FAFC;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #E2E8F0;
            font-size: 12px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Brand Header -->
        <div class="header">
            <span class="logo-sparkle">✦</span>
            <span class="logo-text">UN<span class="logo-cart">CART</span></span>
        </div>

        <!-- Body Content -->
        <div class="content">
            <div class="badge">Security Verification</div>
            <h1>Password Reset Request</h1>
            <p>Hello <strong>{{ $userName }}</strong>,</p>
            <p>We received a request to reset the password for your UNCART account (<code>{{ $userEmail }}</code>). Click the button below to choose a new, secure password:</p>

            <div class="button-wrapper">
                <a href="{{ $resetUrl }}" class="button" target="_blank">Reset Password &rarr;</a>
            </div>

            <div class="expiry-notice">
                ⏳ This password reset link is valid for <strong>{{ $expiresIn }} minutes</strong>. If you did not request this change, please ignore this email; your account remains completely secure.
            </div>

            <div class="fallback">
                If the button above doesn't work, copy and paste this secure link directly into your browser:<br>
                <a href="{{ $resetUrl }}">{{ $resetUrl }}</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} UNCART Modern Commerce. All rights reserved.<br>
            Protected by cryptographic session validation.
        </div>
    </div>
</body>
</html>
