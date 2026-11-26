<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\ProductReview;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductReviewImageTest extends TestCase
{
    use DatabaseTransactions;

    protected User $customer;
    protected User $vendorUser;
    protected User $adminUser;
    protected Vendor $vendor;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->vendorUser = User::factory()->create(['role' => 'vendor']);
        $this->adminUser = User::factory()->create(['role' => 'admin']);

        $this->vendor = Vendor::create([
            'user_id' => $this->vendorUser->id,
            'shop_name' => 'Acoustic Labs ' . rand(100, 999),
            'shop_slug' => 'acoustic-labs-' . rand(100, 999),
            'status' => 'active',
            'commission_rate' => 10.0,
        ]);

        $this->category = Category::first() ?? Category::create([
            'name' => 'Audio',
            'slug' => 'audio-' . rand(100, 999),
        ]);

        $this->product = Product::create([
            'vendor_id' => $this->vendor->id,
            'category_id' => $this->category->id,
            'name' => 'Studio Monitor Headphone ' . rand(100, 999),
            'slug' => 'studio-monitor-' . rand(100, 999),
            'sku' => 'MON-' . rand(1000, 9999),
            'price' => 249.00,
            'stock_quantity' => 20,
            'is_active' => true,
            'status' => 'approved',
            'rating_avg' => 0.0,
            'rating_count' => 0,
            'images' => ['https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800'],
        ]);
    }

    public function test_user_can_submit_review_with_uploaded_images()
    {
        $file1 = UploadedFile::fake()->image('unboxing1.jpg', 800, 600);
        $file2 = UploadedFile::fake()->image('unboxing2.png', 800, 600);

        $response = $this->actingAs($this->customer)->post(route('reviews.store'), [
            'product_id' => $this->product->id,
            'rating' => 5,
            'title' => 'Stunning Sound Quality & Fast Delivery',
            'comment' => 'The headphones arrived in pristine packaging. Soundstage is wide and build quality is superb.',
            'images' => [$file1, $file2],
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $review = ProductReview::where('product_id', $this->product->id)
            ->where('user_id', $this->customer->id)
            ->first();

        $this->assertNotNull($review);
        $this->assertEquals(5, $review->rating);
        $this->assertIsArray($review->images);
        $this->assertCount(2, $review->images);

        // Check storage
        foreach ($review->images as $imgPath) {
            $storageRelativePath = str_replace('/storage/', '', $imgPath);
            Storage::disk('public')->assertExists($storageRelativePath);
        }

        // Test model photo accessors
        $this->assertCount(2, $review->photos);
        $this->assertStringContainsString('storage/reviews/', $review->photos[0]);
        $this->assertStringEndsWith('.jpg', $review->photos[0]);
    }

    public function test_review_images_are_visible_on_product_page()
    {
        $review = ProductReview::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'rating' => 5,
            'title' => 'Impressive Finish and Bass',
            'comment' => 'Real customer photo attached to show cable thickness and finish.',
            'images' => ['/storage/reviews/test_proof_sample.jpg'],
            'status' => 'approved',
        ]);

        $response = $this->get(route('products.show', $this->product->slug));

        $response->assertStatus(200);
        $response->assertSee('Customer Photos');
        $response->assertSee('test_proof_sample.jpg');
        $response->assertSee('Impressive Finish and Bass');
    }

    public function test_seller_can_see_review_images_in_vendor_portal()
    {
        $review = ProductReview::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'rating' => 4,
            'title' => 'Solid Build and Quality',
            'comment' => 'Great product, attaching photo of the ear cushions for the seller to see.',
            'images' => ['/storage/reviews/ear_cushion_proof.jpg'],
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->vendorUser)->get(route('vendor.reviews'));

        $response->assertStatus(200);
        $response->assertSee('Buyer Photos');
        $response->assertSee('ear_cushion_proof.jpg');
    }

    public function test_admin_can_see_review_images_in_admin_portal()
    {
        $review = ProductReview::create([
            'product_id' => $this->product->id,
            'user_id' => $this->customer->id,
            'rating' => 5,
            'title' => 'Authentic item received',
            'comment' => 'Review with serial number proof for moderation check.',
            'images' => ['/storage/reviews/serial_proof.jpg'],
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reviews.index'));

        $response->assertStatus(200);
        $response->assertSee('Photographic Evidence');
        $response->assertSee('serial_proof.jpg');
    }

    public function test_review_submission_rejects_invalid_file_types()
    {
        $invalidFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->customer)->post(route('reviews.store'), [
            'product_id' => $this->product->id,
            'rating' => 4,
            'title' => 'Testing validation error',
            'comment' => 'This review should fail validation because of non-image file attachment.',
            'images' => [$invalidFile],
        ]);

        $response->assertSessionHasErrors(['images.0']);
    }

    public function test_review_submission_rejects_more_than_five_images()
    {
        $files = [];
        for ($i = 0; $i < 6; $i++) {
            $files[] = UploadedFile::fake()->image("photo{$i}.jpg");
        }

        $response = $this->actingAs($this->customer)->post(route('reviews.store'), [
            'product_id' => $this->product->id,
            'rating' => 5,
            'title' => 'Too many files attached',
            'comment' => 'Attempting to upload 6 photos which exceeds the maximum limit of 5.',
            'images' => $files,
        ]);

        $response->assertSessionHasErrors(['images']);
    }
}
