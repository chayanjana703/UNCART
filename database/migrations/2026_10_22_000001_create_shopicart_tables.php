<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add e-commerce fields to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('role')->default('customer')->after('password'); // customer, vendor, admin
            $table->string('avatar')->nullable()->after('role');
            $table->text('bio')->nullable()->after('avatar');
            $table->boolean('two_factor_enabled')->default(false)->after('bio');
            $table->string('two_factor_secret')->nullable()->after('two_factor_enabled');
            $table->boolean('is_blocked')->default(false)->after('two_factor_secret');
            $table->json('preferences')->nullable()->after('is_blocked');
        });

        // 1. Addresses
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('type')->default('shipping'); // shipping, billing
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('postal_code');
            $table->string('country')->default('United States');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 2. Vendors
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('shop_name');
            $table->string('shop_slug')->unique();
            $table->text('shop_description')->nullable();
            $table->string('shop_logo')->nullable();
            $table->string('shop_banner')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('kyc_status')->default('verified'); // pending, verified, rejected
            $table->string('kyc_document')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->string('status')->default('active'); // active, suspended, pending
            $table->text('return_policy')->nullable();
            $table->text('shipping_policy')->nullable();
            $table->decimal('total_payouts', 12, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(10);
            $table->string('sku')->unique();
            $table->json('specifications')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('approved'); // approved, pending, rejected
            $table->decimal('rating_avg', 3, 2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->integer('sales_count')->default(0);
            $table->timestamps();
        });

        // 5. Product Reviews
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating')->default(5);
            $table->string('title')->nullable();
            $table->text('comment');
            $table->json('images')->nullable();
            $table->integer('helpful_votes')->default(0);
            $table->integer('unhelpful_votes')->default(0);
            $table->text('vendor_reply')->nullable();
            $table->timestamp('vendor_replied_at')->nullable();
            $table->string('status')->default('approved'); // approved, pending, flagged
            $table->timestamps();
        });

        // 6. Review Votes
        Schema::create('review_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('product_reviews')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('vote_type'); // helpful, unhelpful
            $table->timestamps();
            $table->unique(['review_id', 'user_id']);
        });

        // 7. Cart Items
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->json('options')->nullable();
            $table->boolean('is_saved_for_later')->default(false);
            $table->timestamps();
        });

        // 8. Wishlists
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        // 9. Coupons
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('discount_type')->default('percent'); // percent, fixed
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_spend', 10, 2)->default(0.00);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->integer('usage_limit')->default(100);
            $table->integer('used_count')->default(0);
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 10. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('shipping_amount', 10, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->string('coupon_code')->nullable();
            $table->string('shipping_method')->default('Standard');
            $table->json('shipping_address');
            $table->json('billing_address');
            $table->string('payment_method')->default('card'); // card, upi, wallet, cod
            $table->string('payment_status')->default('paid'); // paid, pending, failed, refunded
            $table->string('transaction_id')->nullable();
            $table->string('order_status')->default('placed'); // placed, confirmed, processing, packed, shipped, out_for_delivery, delivered, cancelled, returned
            $table->string('tracking_number')->nullable();
            $table->string('carrier_name')->nullable();
            $table->text('notes')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->text('return_reason')->nullable();
            $table->string('return_status')->default('none'); // none, requested, approved, rejected, refunded
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        // 11. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->json('options')->nullable();
            $table->string('vendor_status')->default('pending'); // pending, processing, packed, shipped, delivered, cancelled
            $table->timestamps();
        });

        // 12. Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('order'); // order, promotion, system, vendor
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // 13. Customer Inquiries
        Schema::create('customer_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->text('message');
            $table->text('reply')->nullable();
            $table->string('status')->default('open'); // open, replied, closed
            $table->timestamps();
        });

        // 14. Vendor Payouts
        Schema::create('vendor_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, approved, rejected, paid
            $table->string('reference_note')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // 15. Promotional Banners
        Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('badge')->nullable();
            $table->string('image');
            $table->string('link')->nullable();
            $table->string('button_text')->default('Shop Now');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 16. Activity Logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        // 17. Store Settings
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('promotional_banners');
        Schema::dropIfExists('vendor_payouts');
        Schema::dropIfExists('customer_inquiries');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('review_votes');
        Schema::dropIfExists('product_reviews');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('addresses');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'role',
                'avatar',
                'bio',
                'two_factor_enabled',
                'two_factor_secret',
                'is_blocked',
                'preferences'
            ]);
        });
    }
};
