<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Vendor;

class PlatformRoutesTest extends TestCase
{
    public function test_public_pages_load_successfully()
    {
        $publicUrls = [
            '/',
            '/shop',
            '/cart',
            '/compare',
            '/login',
            '/register',
            '/about',
            '/contact',
            '/faq',
            '/terms',
            '/privacy',
            '/become-a-seller',
        ];

        foreach ($publicUrls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }

        $vendor = Vendor::first();
        if ($vendor) {
            $this->get('/shop/seller/' . $vendor->shop_slug)->assertStatus(200);
        }

        $product = Product::first();
        if ($product) {
            $this->get('/product/' . $product->slug)->assertStatus(200);
        }

        // Test Phase A Live Search & Cart Drawer APIs
        $this->get('/cart/drawer-data')->assertStatus(200)->assertJsonStructure(['items', 'count', 'subtotal']);
        $this->get('/products/live-search?q=pro')->assertStatus(200)->assertJsonStructure(['results', 'total']);
    }

    public function test_quick_login_redirects()
    {
        $this->get('/quick-login/customer')->assertRedirect('/');
        $this->get('/quick-login/vendor')->assertRedirect('/vendor/dashboard');
        $this->get('/quick-login/admin')->assertRedirect('/admin/dashboard');
    }

    public function test_customer_portal_pages()
    {
        $customer = User::where('role', 'customer')->first();
        $this->assertNotNull($customer);

        $customerUrls = [
            '/orders',
            '/wishlist',
            '/profile',
            '/notifications',
            '/inquiries',
        ];

        foreach ($customerUrls as $url) {
            $response = $this->actingAs($customer)->get($url);
            $response->assertStatus(200);
        }

        $order = Order::where('user_id', $customer->id)->first();
        if ($order) {
            $this->actingAs($customer)->get('/orders/' . $order->order_number)->assertStatus(200);
            $this->actingAs($customer)->get('/orders/' . $order->order_number . '/track')->assertStatus(200);
            $this->actingAs($customer)->get('/orders/' . $order->order_number . '/invoice')->assertStatus(200);
        }

        // Test Cart With Items
        $product = Product::first();
        if ($product) {
            $this->actingAs($customer)->post('/cart/add', [
                'product_id' => $product->id,
                'quantity' => 2,
            ]);
            $cartResponse = $this->actingAs($customer)->get('/cart');
            $cartResponse->assertStatus(200);
            $cartResponse->assertSee($product->name);

            // Test Coupon Application & Safe Redirect
            $couponPost = $this->actingAs($customer)->post('/cart/coupon/apply', [
                'code' => 'WELCOME10',
            ]);
            $couponPost->assertRedirect(route('cart.index'));

            // Test Coupon Application via JSON (Cart Drawer)
            $couponJson = $this->actingAs($customer)->postJson('/cart/coupon/apply', [
                'code' => 'WELCOME10',
            ]);
            $couponJson->assertStatus(200)->assertJson(['success' => true]);

            // Test Coupon Removal
            $couponRemove = $this->actingAs($customer)->post('/cart/coupon/remove');
            $couponRemove->assertRedirect(route('cart.index'));

            $checkoutResponse = $this->actingAs($customer)->get('/checkout');
            $checkoutResponse->assertStatus(200);
        }
    }

    public function test_vendor_portal_pages()
    {
        $vendorUser = User::where('role', 'vendor')->first();
        $this->assertNotNull($vendorUser);

        $vendorUrls = [
            '/vendor/dashboard',
            '/vendor/products',
            '/vendor/products/create',
            '/vendor/orders',
            '/vendor/analytics',
            '/vendor/inquiries',
            '/vendor/reviews',
            '/vendor/shop',
        ];

        foreach ($vendorUrls as $url) {
            $response = $this->actingAs($vendorUser)->get($url);
            $response->assertStatus(200);
            $response->assertDontSee('@extends');
        }
    }

    public function test_admin_console_pages()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $adminUrls = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/vendors',
            '/admin/products',
            '/admin/categories',
            '/admin/orders',
            '/admin/reviews',
            '/admin/coupons',
            '/admin/payments',
            '/admin/settings',
            '/admin/reports',
            '/admin/activity-logs',
            '/admin/activity-logs/export',
            '/admin/email-templates',
            '/admin/disputes',
        ];

        foreach ($adminUrls as $url) {
            $response = $this->actingAs($admin)->get($url);
            $response->assertStatus(200);
        }

        $vendor = Vendor::first();
        if ($vendor) {
            $this->actingAs($admin)->get('/admin/vendors/' . $vendor->id)->assertStatus(200);
        }

        $order = Order::first();
        if ($order) {
            $this->actingAs($admin)->get('/admin/orders/' . $order->id)->assertStatus(200);
        }
    }
}
