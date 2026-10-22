<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\StaticPageController;

// Vendor Controllers
use App\Http\Controllers\Vendor\VendorDashboardController;
use App\Http\Controllers\Vendor\VendorProductController;
use App\Http\Controllers\Vendor\VendorOrderController;
use App\Http\Controllers\Vendor\VendorAnalyticsController;
use App\Http\Controllers\Vendor\VendorShopController;
use App\Http\Controllers\Vendor\VendorCommunicationController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVendorController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\AdminReportController;

/*
|--------------------------------------------------------------------------
| Web Routes - SHOPICART (Complete Online Shopping Platform)
|--------------------------------------------------------------------------
*/

// 1. Public Storefront Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/compare', [ProductController::class, 'compare'])->name('products.compare');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/save-for-later/{id}', [CartController::class, 'saveForLater'])->name('cart.save-for-later');
Route::post('/cart/move-to-cart/{id}', [CartController::class, 'moveToCart'])->name('cart.move-to-cart');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/checkout/confirmation/{orderNumber}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

// Static Pages
Route::get('/about', [StaticPageController::class, 'about'])->name('static.about');
Route::get('/privacy', [StaticPageController::class, 'privacy'])->name('static.privacy');
Route::get('/terms', [StaticPageController::class, 'terms'])->name('static.terms');
Route::get('/faq', [StaticPageController::class, 'faq'])->name('static.faq');
Route::get('/contact', [StaticPageController::class, 'contact'])->name('static.contact');
Route::post('/contact/submit', [StaticPageController::class, 'submitContact'])->name('static.contact.submit');

// 2. Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/two-factor', [AuthController::class, 'show2FA'])->name('2fa.show');
Route::post('/two-factor', [AuthController::class, 'verify2FA'])->name('2fa.verify');

// Instant 1-Click Role Switcher (For Evaluation & Demos)
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');

// 3. Authenticated Customer Routes
Route::middleware('auth')->group(function () {
    // Orders & Tracking
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{orderNumber}/track', [OrderController::class, 'track'])->name('orders.track');
    Route::get('/orders/{orderNumber}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('/orders/{orderNumber}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{orderNumber}/return', [OrderController::class, 'requestReturn'])->name('orders.return');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Reviews & Voting
    Route::post('/reviews/store', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{id}/vote', [ReviewController::class, 'vote'])->name('reviews.vote');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // User Profile & Addresses
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::post('/profile/2fa', [ProfileController::class, 'toggle2FA'])->name('profile.2fa');
    Route::post('/profile/preferences', [ProfileController::class, 'preferences'])->name('profile.preferences');
    Route::post('/profile/address/store', [ProfileController::class, 'storeAddress'])->name('profile.address.store');
    Route::delete('/profile/address/{id}', [ProfileController::class, 'deleteAddress'])->name('profile.address.delete');
    Route::post('/profile/address/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.address.default');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Inquiries
    Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::post('/inquiries/store', [InquiryController::class, 'store'])->name('inquiries.store');
});

// 4. Vendor / Seller Portal Routes
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'role:vendor,admin'])->group(function () {
    Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::get('/products', [VendorProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [VendorProductController::class, 'create'])->name('products.create');
    Route::post('/products/store', [VendorProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [VendorProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}/update', [VendorProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [VendorProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{id}/toggle', [VendorProductController::class, 'toggleStatus'])->name('products.toggle');
    Route::post('/products/bulk-upload', [VendorProductController::class, 'bulkUpload'])->name('products.bulk');
    Route::get('/products/template/download', [VendorProductController::class, 'downloadTemplate'])->name('products.template');

    // Orders
    Route::get('/orders', [VendorOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [VendorOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [VendorOrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('/orders/{id}/shipping-label', [VendorOrderController::class, 'shippingLabel'])->name('orders.shipping-label');

    // Analytics
    Route::get('/analytics', [VendorAnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [VendorAnalyticsController::class, 'exportCsv'])->name('analytics.export');

    // Shop Settings & KYC
    Route::get('/shop', [VendorShopController::class, 'edit'])->name('shop.edit');
    Route::post('/shop/update', [VendorShopController::class, 'update'])->name('shop.update');
    Route::post('/shop/kyc', [VendorShopController::class, 'submitKyc'])->name('shop.kyc');
    Route::post('/shop/payout', [VendorShopController::class, 'requestPayout'])->name('shop.payout');

    // Communication
    Route::get('/inquiries', [VendorCommunicationController::class, 'inquiries'])->name('inquiries');
    Route::post('/inquiries/{id}/reply', [VendorCommunicationController::class, 'replyInquiry'])->name('inquiries.reply');
    Route::get('/reviews', [VendorCommunicationController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{id}/reply', [VendorCommunicationController::class, 'replyReview'])->name('reviews.reply');
});

// 5. Administrator Portal Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/store', [AdminUserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}/update', [AdminUserController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/toggle-block', [AdminUserController::class, 'toggleBlock'])->name('users.toggle-block');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/export', [AdminUserController::class, 'exportCsv'])->name('users.export');

    // Vendor Management
    Route::get('/vendors', [AdminVendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/{id}', [AdminVendorController::class, 'show'])->name('vendors.show');
    Route::post('/vendors/{id}/status', [AdminVendorController::class, 'updateStatus'])->name('vendors.status');
    Route::post('/vendors/payout/{id}/approve', [AdminVendorController::class, 'approvePayout'])->name('vendors.payout.approve');

    // Product Oversight
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::post('/products/{id}/status', [AdminProductController::class, 'updateStatus'])->name('products.status');
    Route::post('/products/{id}/featured', [AdminProductController::class, 'toggleFeatured'])->name('products.featured');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Category Management
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories/store', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}/update', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{id}/refund', [AdminOrderController::class, 'processRefund'])->name('orders.refund');
    Route::get('/orders/export', [AdminOrderController::class, 'exportCsv'])->name('orders.export');

    // Reviews Management
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    // Coupons & Banners
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons/store', [AdminCouponController::class, 'storeCoupon'])->name('coupons.store');
    Route::post('/coupons/{id}/toggle', [AdminCouponController::class, 'toggleCoupon'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [AdminCouponController::class, 'destroyCoupon'])->name('coupons.destroy');
    Route::post('/coupons/banner/store', [AdminCouponController::class, 'storeBanner'])->name('coupons.banner.store');
    Route::delete('/coupons/banner/{id}', [AdminCouponController::class, 'destroyBanner'])->name('coupons.banner.destroy');

    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/toggle/{method}', [AdminPaymentController::class, 'toggleMethod'])->name('payments.toggle');

    // Settings
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/update', [AdminSettingsController::class, 'update'])->name('settings.update');

    // Reports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-sales', [AdminReportController::class, 'exportSales'])->name('reports.export-sales');
});
