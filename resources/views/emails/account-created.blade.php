<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Created Successfully — UNCART</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #FAF5FC;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1E293B;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 580px;
            margin: 36px auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 30px -5px rgba(27, 14, 36, 0.08);
        }
        .header {
            background-color: #1B0E24;
            padding: 34px 40px;
            text-align: center;
        }
        .logo-text {
            color: #ffffff;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            text-decoration: none;
            display: inline-block;
        }
        .logo-cart {
            color: #F05A28;
        }
        .content {
            padding: 36px 40px;
        }
        .badge {
            display: inline-block;
            background: #ECFDF5;
            color: #065F46;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 5px 14px;
            border-radius: 9999px;
            margin-bottom: 16px;
            border: 1px solid #A7F3D0;
        }
        .title {
            font-size: 22px;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 10px 0;
            letter-spacing: -0.5px;
            line-height: 1.25;
        }
        .lead {
            font-size: 14px;
            color: #475569;
            margin: 0 0 24px 0;
            line-height: 1.6;
        }
        .card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 18px;
            padding: 20px 24px;
            margin: 22px 0;
        }
        .card-header {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #E2E8F0;
            font-size: 13px;
        }
        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .detail-label {
            color: #64748B;
            font-weight: 500;
        }
        .detail-value {
            color: #0F172A;
            font-weight: 700;
            text-align: right;
            word-break: break-all;
        }
        .btn-wrapper {
            text-align: center;
            margin: 32px 0 24px 0;
        }
        .btn {
            display: inline-block;
            background: #1B0E24;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 9999px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 14px rgba(27, 14, 36, 0.25);
        }
        .next-steps {
            background: #FAF5FC;
            border: 1px solid #EDE4F2;
            border-radius: 16px;
            padding: 18px 22px;
            margin: 24px 0;
        }
        .next-steps h4 {
            margin: 0 0 10px 0;
            font-size: 13px;
            font-weight: 700;
            color: #1B0E24;
        }
        .next-steps ul {
            margin: 0;
            padding-left: 20px;
            font-size: 12px;
            color: #475569;
        }
        .next-steps li {
            margin-bottom: 6px;
        }
        .security-note {
            font-size: 12px;
            color: #64748B;
            line-height: 1.5;
            background: #FFFBEB;
            border: 1px solid #FDE68A;
            border-radius: 14px;
            padding: 14px 18px;
            margin: 20px 0;
        }
        .footer {
            background-color: #F8FAFC;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #E2E8F0;
            font-size: 11px;
            color: #94A3B8;
            line-height: 1.5;
        }
        .footer a {
            color: #64748B;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Brand Header -->
        <div class="header">
            <div class="logo-text">
                ✦ UN<span class="logo-cart">CART</span>
            </div>
        </div>

        <!-- Email Body -->
        <div class="content">
            <div class="badge">✓ Account Verified & Active</div>
            
            <h1 class="title">Welcome, {{ $user->name }}!</h1>
            <p class="lead">
                Your UNCART account has been successfully created and verified. You are now officially part of the UNCART ecosystem with full access to our curated commerce platform.
            </p>

            <!-- Account Details Card -->
            <div class="card">
                <div class="card-header">
                    <span>Account Overview</span>
                    <span style="color: #10B981; font-weight: 800;">● Active</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Account Holder</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Registered Email</span>
                    <span class="detail-value" style="font-family: monospace;">{{ $user->email }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Account Membership</span>
                    <span class="detail-value" style="color: #6366F1;">{{ $roleName }}</span>
                </div>

                @if(!empty($user->phone))
                <div class="detail-row">
                    <span class="detail-label">Contact Phone</span>
                    <span class="detail-value">{{ $user->phone }}</span>
                </div>
                @endif

                <div class="detail-row">
                    <span class="detail-label">Member Since</span>
                    <span class="detail-value">
                        {{ $user->created_at ? $user->created_at->format('M d, Y · h:i A') : now()->format('M d, Y · h:i A') }}
                    </span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Email Verification</span>
                    <span class="detail-value" style="color: #059669;">Verified via OTP</span>
                </div>
            </div>

            <!-- Merchant Storefront Details (if Vendor) -->
            @if(!empty($vendor))
            <div class="card" style="background: #FDF4FF; border-color: #F5D0FE;">
                <div class="card-header" style="color: #86198F; border-bottom-color: #F5D0FE;">
                    <span>🏪 Merchant Storefront Details</span>
                    <span style="color: #C026D3; font-weight: 800;">Seller Portal</span>
                </div>

                <div class="detail-row" style="border-bottom-color: #F5D0FE;">
                    <span class="detail-label">Store Brand Name</span>
                    <span class="detail-value">{{ $vendor->shop_name }}</span>
                </div>

                <div class="detail-row" style="border-bottom-color: #F5D0FE;">
                    <span class="detail-label">Public Store URL</span>
                    <span class="detail-value" style="font-family: monospace; font-size: 11px;">
                        <a href="{{ route('vendor.storefront', $vendor->shop_slug) }}" style="color: #9333EA; text-decoration: none;">
                            uncart.com/shop/seller/{{ $vendor->shop_slug }}
                        </a>
                    </span>
                </div>

                <div class="detail-row" style="border-bottom-color: #F5D0FE;">
                    <span class="detail-label">KYC Document Status</span>
                    <span class="detail-value" style="color: #D97706; text-transform: uppercase; font-size: 11px;">
                        {{ $vendor->kyc_status ?? 'Pending Review' }}
                    </span>
                </div>

                <div class="detail-row" style="border-bottom: none;">
                    <span class="detail-label">Commission Rate</span>
                    <span class="detail-value">{{ number_format($vendor->commission_rate, 2) }}%</span>
                </div>
            </div>
            @endif

            <!-- What to do next -->
            <div class="next-steps">
                @if(($user->role ?? '') === 'vendor')
                    <h4>What you can do right now:</h4>
                    <ul>
                        <li>Access your dedicated <strong>Seller Dashboard</strong> to monitor orders and revenues.</li>
                        <li>Upload your product catalog with images directly from your device.</li>
                        <li>Review your storefront branding, dispatch address, and bank payout settings.</li>
                    </ul>
                @else
                    <h4>Get the most out of your UNCART account:</h4>
                    <ul>
                        <li>Save your shipping and delivery addresses in your <strong>Address Book</strong>.</li>
                        <li>Create a curated <strong>Wishlist</strong> of your favorite products.</li>
                        <li>Enjoy real-time parcel dispatch tracking and official tax invoices on every order.</li>
                    </ul>
                @endif
            </div>

            <!-- Call to Action -->
            <div class="btn-wrapper">
                <a href="{{ $actionUrl }}" class="btn">
                    {{ $actionText ?? 'Get Started' }} &rarr;
                </a>
            </div>

            <!-- Security Advisory -->
            <div class="security-note">
                <strong style="color: #92400E;">🔒 Security Tip:</strong> 
                UNCART representatives will never ask you for your account password. For maximum protection, consider turning on Two-Factor Authentication (2FA) in your Account Settings.
            </div>

            <p style="font-size: 12px; color: #64748B; margin-top: 24px;">
                If you did not initiate this registration or suspect unauthorized use of your email, please reach out immediately to our support team at <a href="mailto:support@uncart.com" style="color: #6366F1;">support@uncart.com</a>.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 8px 0; font-weight: 600; color: #475569;">
                UNCART Multi-Vendor E-Commerce Platform
            </p>
            <p style="margin: 0 0 12px 0;">
                Designed for discerning creators and buyers. All rights reserved.
            </p>
            <p style="margin: 0;">
                <a href="{{ route('home') }}">Storefront</a> &nbsp;•&nbsp; 
                <a href="{{ route('static.privacy') }}">Privacy Policy</a> &nbsp;•&nbsp; 
                <a href="{{ route('static.terms') }}">Terms of Service</a> &nbsp;•&nbsp; 
                <a href="{{ route('static.contact') }}">Help Center</a>
            </p>
        </div>
    </div>
</body>
</html>
