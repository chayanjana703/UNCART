<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminEmailTemplateController extends Controller
{
    private function getTemplates()
    {
        return [
            'order_confirmation' => [
                'id' => 'order_confirmation',
                'name' => 'Order Confirmation & Receipt',
                'description' => 'Sent automatically to customer immediately after successful order payment.',
                'subject' => 'Your NovaShop Order #{order_number} is Confirmed!',
                'tags' => ['{customer_name}', '{order_number}', '{order_total}', '{shipping_address}', '{tracking_url}'],
                'body' => '<p>Hi {customer_name},</p><p>Thank you for shopping with NovaShop! Your order <strong>#{order_number}</strong> has been received and is being prepared with care by our makers.</p><p>Total Amount Paid: <strong>{order_total}</strong></p><p>Shipping to:<br>{shipping_address}</p><p><a href="{tracking_url}">Track Your Order Live</a></p>',
            ],
            'order_shipped' => [
                'id' => 'order_shipped',
                'name' => 'Order Dispatched & Tracking Link',
                'description' => 'Dispatched when the vendor or warehouse updates order status to Shipped.',
                'subject' => 'Great news! Your package #{order_number} has shipped',
                'tags' => ['{customer_name}', '{order_number}', '{carrier_name}', '{tracking_number}', '{tracking_url}'],
                'body' => '<p>Hi {customer_name},</p><p>Your order <strong>#{order_number}</strong> is officially on its way!</p><p>Carrier: <strong>{carrier_name}</strong><br>Tracking Code: <strong>{tracking_number}</strong></p><p><a href="{tracking_url}">View Shipment Tracking</a></p>',
            ],
            'welcome_user' => [
                'id' => 'welcome_user',
                'name' => 'Welcome to NovaShop / Email Verification',
                'description' => 'Welcome onboarding message sent to new customers with promo coupon.',
                'subject' => 'Welcome to NovaShop — Here is your $10 Gift Code',
                'tags' => ['{customer_name}', '{coupon_code}', '{shop_url}'],
                'body' => '<p>Welcome {customer_name},</p><p>We are delighted to have you. Enjoy 10% off your first precision hardware or lifestyle order with code <strong>{coupon_code}</strong> at checkout.</p><p><a href="{shop_url}">Start Exploring Collections</a></p>',
            ],
            'vendor_kyc_approved' => [
                'id' => 'vendor_kyc_approved',
                'name' => 'Vendor KYC Verification Approved',
                'description' => 'Notifies newly registered sellers when their compliance documents are verified.',
                'subject' => 'Your NovaShop Merchant Account is Fully Verified!',
                'tags' => ['{merchant_name}', '{shop_name}', '{dashboard_url}'],
                'body' => '<p>Congratulations {merchant_name},</p><p>Your store <strong>{shop_name}</strong> has passed our regulatory KYC verification. You can now publish unlimited products and receive weekly payouts.</p><p><a href="{dashboard_url}">Open Seller Dashboard</a></p>',
            ],
            'password_reset' => [
                'id' => 'password_reset',
                'name' => 'Password Reset Security Alert',
                'description' => 'Secure one-time authentication link dispatched when a user requests password reset.',
                'subject' => 'NovaShop Password Reset Request',
                'tags' => ['{customer_name}', '{reset_url}', '{expiry_minutes}'],
                'body' => '<p>Hello {customer_name},</p><p>We received a request to reset your password. Click the link below to set a new password. This link expires in {expiry_minutes} minutes.</p><p><a href="{reset_url}">Reset My Password</a></p><p>If you did not request this, please ignore this email.</p>',
            ],
        ];
    }

    public function index()
    {
        $templates = $this->getTemplates();
        return view('admin.email-templates.index', compact('templates'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        return back()->with('success', "Email template '{$id}' updated successfully.");
    }

    public function sendTest(Request $request, $id)
    {
        $email = $request->input('test_email', 'admin@shopicart.com');
        return back()->with('success', "Simulated test email for template '{$id}' sent to {$email}.");
    }
}
