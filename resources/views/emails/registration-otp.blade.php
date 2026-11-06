<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Verification OTP — UNCART</title>
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
        .logo-text {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            vertical-align: middle;
        }
        .logo-cart {
            color: #F05A28;
        }
        .content {
            padding: 40px;
        }
        .badge {
            display: inline-block;
            background: #FEF3C7;
            color: #92400E;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 9999px;
            margin-bottom: 16px;
        }
        .title {
            font-size: 20px;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 12px 0;
            letter-spacing: -0.5px;
        }
        .lead {
            font-size: 14px;
            color: #475569;
            margin: 0 0 24px 0;
        }
        .otp-container {
            background: #F8FAFC;
            border: 2px dashed #CBD5E1;
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            margin: 28px 0;
        }
        .otp-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
        }
        .otp-code {
            font-family: 'SF Mono', SFMono-Regular, ui-monospace, Menlo, Monaco, Consolas, monospace;
            font-size: 36px;
            font-weight: 800;
            color: #1B0E24;
            letter-spacing: 8px;
            margin: 4px 0;
        }
        .expiry-text {
            font-size: 11px;
            color: #94A3B8;
            margin-top: 8px;
        }
        .instructions {
            background-color: #FAF5FC;
            border-left: 3px solid #6B2A88;
            padding: 14px 16px;
            border-radius: 0 12px 12px 0;
            font-size: 12px;
            color: #4A1D6E;
            margin-bottom: 24px;
        }
        .footer {
            background: #F8FAFC;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #E2E8F0;
            font-size: 11px;
            color: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo-text">UN<span class="logo-cart">CART</span></span>
        </div>

        <div class="content">
            <span class="badge">Account Verification</span>
            <h1 class="title">Verify Your Registration</h1>
            <p class="lead">Hello {{ $userName }}, welcome to UNCART! You are creating a new <strong>{{ $roleTitle ?? 'Account' }}</strong>. Please use the verification code below to confirm your email and activate your account.</p>

            <div class="otp-container">
                <div class="otp-label">6-Digit Verification Code</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="expiry-text">Valid for the next {{ $expiresIn ?? 15 }} minutes</div>
            </div>

            <div class="instructions">
                <strong>Security Notice:</strong> Never share this verification code with anyone. UNCART staff will never ask for your one-time passcode.
            </div>

            <p style="font-size: 12px; color: #64748B; margin: 0;">If you did not initiate this registration request on UNCART, you can safely ignore this email.</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} UNCART Precision Commerce Platform Inc.<br>
            Automated security dispatch &bull; Do not reply to this email
        </div>
    </div>
</body>
</html>
