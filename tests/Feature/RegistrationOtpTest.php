<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RegistrationOtpNotification;
use App\Notifications\AccountCreatedNotification;

class RegistrationOtpTest extends TestCase
{
    use DatabaseTransactions;

    public function test_customer_registration_initiates_otp_verification()
    {
        Notification::fake();

        $response = $this->post(route('register.submit'), [
            'name' => 'Alice Customer',
            'email' => 'alice@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'phone' => '+1555123456',
            'role' => 'customer',
        ]);

        $response->assertRedirect(route('register.otp.show'));
        $this->assertTrue(session()->has('pending_registration'));
        $this->assertEquals('alice@example.com', session('pending_registration.email'));

        Notification::assertSentOnDemand(RegistrationOtpNotification::class, function ($notification, $channels, $notifiable) {
            return $notifiable->routes['mail'] === 'alice@example.com' && strlen($notification->otp) === 6;
        });

        // Ensure user is not yet created in database prior to OTP confirmation
        $this->assertDatabaseMissing('users', ['email' => 'alice@example.com']);
    }

    public function test_customer_submitting_invalid_otp_fails()
    {
        $this->post(route('register.submit'), [
            'name' => 'Bob Customer',
            'email' => 'bob@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'role' => 'customer',
        ]);

        $response = $this->post(route('register.otp.verify'), [
            'otp' => '000000',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'bob@example.com']);
    }

    public function test_customer_submitting_valid_otp_completes_registration_and_logs_in()
    {
        Notification::fake();

        $this->post(route('register.submit'), [
            'name' => 'Charlie Customer',
            'email' => 'charlie@example.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'phone' => '+1555987654',
            'role' => 'customer',
        ]);

        // Ensure the verification view does not display demo passcode on the card
        $otpShowResponse = $this->get(route('register.otp.show'));
        $otpShowResponse->assertDontSee('Demo Passcode Preview');

        $capturedOtp = null;
        Notification::assertSentOnDemand(RegistrationOtpNotification::class, function ($notification) use (&$capturedOtp) {
            $capturedOtp = $notification->otp;
            return true;
        });

        $this->assertNotEmpty($capturedOtp);

        $response = $this->post(route('register.otp.verify'), [
            'otp' => $capturedOtp,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', 'charlie@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Charlie Customer', $user->name);
        $this->assertEquals('customer', $user->role);
        $this->assertNotNull($user->email_verified_at);

        // Assert AccountCreatedNotification was dispatched to the new user
        Notification::assertSentTo($user, AccountCreatedNotification::class, function ($notification) use ($user) {
            return $notification->user->id === $user->id;
        });
    }

    public function test_vendor_can_upload_shop_logo_from_device_in_shop_settings()
    {
        Storage::fake('public');

        $vendorUser = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'shop_name' => 'Artisan Acoustics',
            'shop_slug' => 'artisan-acoustics',
            'shop_description' => 'Precision audio gear',
            'phone' => '+15554321',
            'email' => 'seller@artisan.com',
            'address' => '100 Studio Way',
            'kyc_status' => 'pending',
            'status' => 'active',
            'commission_rate' => 8.50,
        ]);

        $file = UploadedFile::fake()->image('custom_logo.png', 400, 400);

        $response = $this->actingAs($vendorUser)->put(route('vendor.shop.update'), [
            'shop_name' => 'Artisan Acoustics Studio',
            'email' => 'seller@artisan.com',
            'phone' => '+15554321',
            'address' => '100 Studio Way',
            'shop_description' => 'Updated precision acoustic engineering',
            'shop_logo_file' => $file,
        ]);

        $response->assertSessionHas('success');
        $vendor->refresh();

        $this->assertStringStartsWith('/storage/shops/', $vendor->shop_logo);
        $storedFilename = basename($vendor->shop_logo);
        Storage::disk('public')->assertExists('shops/' . $storedFilename);
    }

    public function test_vendor_can_submit_device_kyc_document()
    {
        Storage::fake('public');

        $vendorUser = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'shop_name' => 'Nordic Woods',
            'shop_slug' => 'nordic-woods',
            'shop_description' => 'Handcrafted furniture',
            'phone' => '+15558888',
            'email' => 'info@nordic.com',
            'address' => '200 Forest Rd',
            'kyc_status' => 'pending',
            'status' => 'active',
            'commission_rate' => 8.50,
        ]);

        $pdf = UploadedFile::fake()->create('business_license.pdf', 500, 'application/pdf');

        $response = $this->actingAs($vendorUser)->post(route('vendor.shop.kyc'), [
            'tax_id' => 'US-EIN-992211',
            'kyc_file' => $pdf,
        ]);

        $response->assertSessionHas('success');
        $vendor->refresh();

        $this->assertStringContainsString('/storage/kyc/', $vendor->kyc_document);
        $this->assertStringContainsString('US-EIN-992211', $vendor->kyc_document);
    }

    public function test_vendor_registration_with_device_logo_requires_otp_verification()
    {
        Storage::fake('public');
        Notification::fake();

        $logoFile = UploadedFile::fake()->image('brand_badge.jpg', 300, 300);

        $response = $this->post(route('vendor.become.submit'), [
            'name' => 'Eleanor Merchant',
            'email' => 'eleanor@merchant.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'shop_name' => 'Eleanor Crafts',
            'shop_description' => 'Handmade leather and stationery',
            'business_email' => 'eleanor@merchant.com',
            'business_phone' => '+15553334444',
            'address' => '33 Leather Lane',
            'bank_name' => 'Chase Bank',
            'account_number' => '1234567890',
            'ifsc_code' => 'CHAS0001',
            'tax_id' => 'EIN-990088',
            'shop_logo_file' => $logoFile,
        ]);

        $response->assertRedirect(route('vendor.become.otp.show'));
        $this->assertTrue(session()->has('pending_vendor_registration'));
        
        $pending = session('pending_vendor_registration');
        $this->assertStringStartsWith('/storage/shops/', $pending['shop_logo']);

        // Ensure the verification view does not display demo passcode on the card
        $otpShowResponse = $this->get(route('vendor.become.otp.show'));
        $otpShowResponse->assertDontSee('Demo Passcode Preview');

        // Capture OTP from dispatched notification
        $capturedOtp = null;
        Notification::assertSentOnDemand(RegistrationOtpNotification::class, function ($notification) use (&$capturedOtp) {
            $capturedOtp = $notification->otp;
            return true;
        });

        $this->assertNotEmpty($capturedOtp);

        $verifyResponse = $this->post(route('vendor.become.otp.verify'), [
            'otp' => $capturedOtp,
        ]);

        $verifyResponse->assertRedirect(route('vendor.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'eleanor@merchant.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('vendor', $user->role);

        $vendor = Vendor::where('user_id', $user->id)->first();
        $this->assertNotNull($vendor);
        $this->assertEquals('Eleanor Crafts', $vendor->shop_name);
        $this->assertStringStartsWith('/storage/shops/', $vendor->shop_logo);

        // Assert AccountCreatedNotification was dispatched with vendor profile
        Notification::assertSentTo($user, AccountCreatedNotification::class, function ($notification) use ($vendor) {
            return $notification->vendor && $notification->vendor->id === $vendor->id;
        });
    }

    public function test_account_created_email_renders_correctly_with_details()
    {
        $user = User::factory()->create([
            'name' => 'Seraphina Vance',
            'email' => 'seraphina@example.com',
            'phone' => '+15550001111',
            'role' => 'customer',
        ]);

        $notification = new AccountCreatedNotification($user);
        $mailMessage = $notification->toMail($user);

        $this->assertEquals("Welcome to UNCART — Your Account Has Been Created!", $mailMessage->subject);

        $renderedHtml = view($mailMessage->view, $mailMessage->viewData)->render();
        $this->assertStringContainsString('Seraphina Vance', $renderedHtml);
        $this->assertStringContainsString('seraphina@example.com', $renderedHtml);
        $this->assertStringContainsString('+15550001111', $renderedHtml);
        $this->assertStringContainsString('Account Verified & Active', $renderedHtml);
    }
}
