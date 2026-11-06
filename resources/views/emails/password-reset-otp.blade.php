<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset OTP — UNCART</title>
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
            max-width: 540px;
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
            margin: 0 0 18px;
        }
        .otp-box {
            background: #FAF5FC;
            border: 2px dashed #D0A9E4;
            border-radius: 20px;
            padding: 24px 20px;
            text-align: center;
            margin: 28px 0;
        }
        .otp-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #6B2A88;
            font-weight: 800;
            margin-bottom: 10px;
        }
        .otp-digits {
            font-family: 'JetBrains Mono', 'Courier New', Courier, monospace;
            font-size: 40px;
            font-weight: 900;
            letter-spacing: 10px;
            color: #1B0E24;
            user-select: all;
            margin: 4px 0;
        }
        .otp-sub {
            font-size: 11px;
            color: #8E44AD;
            margin-top: 8px;
        }
        .expiry-alert {
            background: #FFFDF5;
            border: 1px solid #FDEEC4;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 12px;
            color: #92400E;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .security-note {
            border-top: 1px solid #F1F5F9;
            padding-top: 20px;
            font-size: 12px;
            color: #64748B;
            line-height: 1.5;
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
            <div class="badge">Two-Factor Recovery</div>
            <h1>Your Password Reset OTP</h1>
            <p>Hello <strong>{{ $userName }}</strong>,</p>
            <p>We received a request to reset the password for your UNCART account (<code>{{ $userEmail }}</code>). Use the verification code below to authorize the password update:</p>

            <div class="otp-box">
                <div class="otp-label">One-Time Verification Code</div>
                <div class="otp-digits">{{ $otp }}</div>
                <div class="otp-sub">Enter this 6-digit code on the reset page</div>
            </div>

            <div class="expiry-alert">
                ⏳ This passcode is valid for <strong>{{ $expiresIn }} minutes</strong>. For your security, it will expire automatically after that time.
            </div>

            <div class="security-note">
                🔒 <strong>Security Warning:</strong> Never share this code with anyone. UNCART staff will never call or message you asking for your verification code. If you did not initiate this request, your account is safe and you may disregard this email.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} UNCART Modern Commerce. All rights reserved.<br>
            Secure cryptographic password verification.
        </div>
    </div>
</body>
</html>
