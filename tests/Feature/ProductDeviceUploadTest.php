<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProductDeviceUploadTest extends TestCase
{
    use DatabaseTransactions;

    public function test_vendor_can_create_product_with_device_uploaded_images()
    {
        Storage::fake('public');

        $vendorUser = User::where('role', 'vendor')->first() ?? User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::firstOrCreate(
            ['user_id' => $vendorUser->id],
            [
                'shop_name' => 'Test Vendor Electronics',
                'shop_slug' => 'test-vendor-electronics',
                'status' => 'active',
                'kyc_status' => 'verified'
            ]
        );
        $category = Category::first() ?? Category::create(['name' => 'Peripherals', 'slug' => 'peripherals']);

        $file1 = UploadedFile::fake()->image('mechanical-keyboard-1.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('mechanical-keyboard-2.png', 800, 600);

        $sku = 'TEST-DEV-' . rand(1000, 9999);
        $response = $this->actingAs($vendorUser)->post(route('vendor.products.store'), [
            'name' => 'Apex Mechanical Keyboard',
            'category_id' => $category->id,
            'sku' => $sku,
            'price' => 149.99,
            'sale_price' => 129.99,
            'stock_quantity' => 15,
            'short_description' => 'Fast switches with RGB',
            'description' => 'High quality mechanical keyboard crafted from aluminum.',
            'image_files' => [$file1, $file2],
            'is_active' => '1',
            'is_featured' => '1',
        ]);

        $response->assertRedirect(route('vendor.products.index'));

        $product = Product::where('sku', $sku)->first();
        $this->assertNotNull($product);
        $this->assertIsArray($product->images);
        $this->assertCount(2, $product->images);

        // Verify stored in fake public storage disk
        Storage::disk('public')->assertExists($product->images[0]);
        Storage::disk('public')->assertExists($product->images[1]);

        // Verify image accessor URLs
        $this->assertStringContainsString('storage/products/', $product->featured_image);
        $this->assertStringContainsString('storage/products/', $product->primary_image);
        $this->assertCount(2, $product->gallery_images);
    }

    public function test_vendor_can_edit_product_with_device_images_and_remove_existing()
    {
        Storage::fake('public');

        $vendorUser = User::where('role', 'vendor')->first() ?? User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::firstOrCreate(
            ['user_id' => $vendorUser->id],
            [
                'shop_name' => 'Test Vendor Electronics',
                'shop_slug' => 'test-vendor-electronics',
                'status' => 'active',
                'kyc_status' => 'verified'
            ]
        );
        $category = Category::first();

        // Create initial product with 2 device pictures
        $initial1 = UploadedFile::fake()->image('old-pic-1.jpg')->store('products', 'public');
        $initial2 = UploadedFile::fake()->image('old-pic-2.jpg')->store('products', 'public');

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Original Audio Headset',
            'slug' => 'orig-headset-' . rand(100, 999),
            'price' => 99.00,
            'stock_quantity' => 10,
            'sku' => 'HDST-' . rand(1000, 9999),
            'description' => 'Great headphones',
            'images' => [$initial1, $initial2],
            'status' => 'approved',
        ]);

        // Keep initial1, remove initial2, and upload a new device file
        $newFile = UploadedFile::fake()->image('new-angle.png');

        $response = $this->actingAs($vendorUser)->put(route('vendor.products.update', $product->id), [
            'name' => 'Original Audio Headset v2',
            'category_id' => $category->id,
            'sku' => $product->sku,
            'price' => 109.00,
            'stock_quantity' => 20,
            'description' => 'Updated great headphones',
            'existing_images' => [$initial1],
            'image_files' => [$newFile],
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('vendor.products.index'));

        $product->refresh();
        $this->assertCount(2, $product->images);
        $this->assertEquals($initial1, $product->images[0]);
        Storage::disk('public')->assertExists($initial1);
        Storage::disk('public')->assertExists($product->images[1]);
        // initial2 was deleted
        Storage::disk('public')->assertMissing($initial2);
    }

    public function test_admin_can_create_and_edit_product_with_device_images()
    {
        Storage::fake('public');

        $adminUser = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $vendor = Vendor::first();
        $category = Category::first();

        $adminFile = UploadedFile::fake()->image('admin-curated-camera.jpg');

        $sku = 'ADM-' . rand(1000, 9999);
        $response = $this->actingAs($adminUser)->post(route('admin.products.store'), [
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Curated Pro DSLR Camera',
            'sku' => $sku,
            'price' => 899.00,
            'stock_quantity' => 8,
            'short_description' => 'Flagship camera',
            'description' => 'Full frame sensor camera body',
            'image_files' => [$adminFile],
            'status' => 'approved',
            'is_active' => '1',
            'is_featured' => '1',
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', $sku)->first();
        $this->assertNotNull($product);
        $this->assertCount(1, $product->images);
        Storage::disk('public')->assertExists($product->images[0]);
        $this->assertStringContainsString('storage/products/', $product->primary_image);

        // Edit as admin and add another device picture
        $newAngle = UploadedFile::fake()->image('admin-angle-2.webp');
        $editResp = $this->actingAs($adminUser)->put(route('admin.products.update', $product->id), [
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Curated Pro DSLR Camera Mark II',
            'sku' => $sku,
            'price' => 949.00,
            'stock_quantity' => 12,
            'description' => 'Full frame sensor camera body updated',
            'existing_images' => $product->images,
            'image_files' => [$newAngle],
            'status' => 'approved',
            'is_active' => '1',
        ]);

        $editResp->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertCount(2, $product->images);
        $this->assertEquals('Curated Pro DSLR Camera Mark II', $product->name);
    }

    public function test_admin_can_create_category_with_device_image()
    {
        Storage::fake('public');

        $adminUser = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $catImage = UploadedFile::fake()->image('custom-category.jpg');

        $response = $this->actingAs($adminUser)->post(route('admin.categories.store'), [
            'name' => 'Custom Gear ' . rand(100, 999),
            'description' => 'Top tier gear',
            'image_file' => $catImage,
        ]);

        $response->assertRedirect();
        $category = Category::where('name', 'like', 'Custom Gear%')->latest()->first();
        $this->assertNotNull($category);
        Storage::disk('public')->assertExists($category->getRawOriginal('image'));
        $this->assertStringContainsString('storage/categories/', $category->image);
    }
}
