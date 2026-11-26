<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\ProductReview;
use App\Models\ReviewVote;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReviewVoteTest extends TestCase
{
    use DatabaseTransactions;

    protected User $author;
    protected User $voter;
    protected Vendor $vendor;
    protected Category $category;
    protected Product $product;
    protected ProductReview $review;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = User::factory()->create(['role' => 'customer']);
        $this->voter = User::factory()->create(['role' => 'customer']);
        $vendorUser = User::factory()->create(['role' => 'vendor']);

        $this->vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'shop_name' => 'AudioCraft ' . rand(100, 999),
            'shop_slug' => 'audiocraft-' . rand(100, 999),
            'status' => 'active',
            'commission_rate' => 10.0,
        ]);

        $this->category = Category::first() ?? Category::create([
            'name' => 'Audio Gear',
            'slug' => 'audio-gear-' . rand(100, 999),
        ]);

        $this->product = Product::create([
            'vendor_id' => $this->vendor->id,
            'category_id' => $this->category->id,
            'name' => 'Audiophile Over-Ear Headphones ' . rand(100, 999),
            'slug' => 'audiophile-headphones-' . rand(100, 999),
            'sku' => 'APH-' . rand(1000, 9999),
            'price' => 320.00,
            'stock_quantity' => 10,
            'is_active' => true,
            'status' => 'approved',
            'rating_avg' => 5.0,
            'rating_count' => 1,
        ]);

        $this->review = ProductReview::create([
            'product_id' => $this->product->id,
            'user_id' => $this->author->id,
            'rating' => 5,
            'title' => 'Masterpiece in acoustic tuning',
            'comment' => 'Crystal clear highs and punchy bass without distortion.',
            'helpful_votes' => 0,
            'unhelpful_votes' => 0,
            'status' => 'approved',
        ]);
    }

    public function test_authenticated_user_can_vote_helpful_on_review()
    {
        $response = $this->actingAs($this->voter)->postJson(route('reviews.vote', $this->review->id), [
            'vote_type' => 'helpful',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'helpful' => 1,
            'unhelpful' => 0,
            'user_vote' => 'helpful',
        ]);

        $this->assertDatabaseHas('review_votes', [
            'review_id' => $this->review->id,
            'user_id' => $this->voter->id,
            'vote_type' => 'helpful',
        ]);

        $this->assertEquals(1, $this->review->fresh()->helpful_votes);
    }

    public function test_user_clicking_same_vote_toggles_it_off()
    {
        // First vote
        $this->actingAs($this->voter)->postJson(route('reviews.vote', $this->review->id), [
            'vote_type' => 'helpful',
        ]);

        $this->assertEquals(1, $this->review->fresh()->helpful_votes);

        // Click again -> untoggle
        $response = $this->actingAs($this->voter)->postJson(route('reviews.vote', $this->review->id), [
            'vote_type' => 'helpful',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'helpful' => 0,
            'unhelpful' => 0,
            'user_vote' => null,
        ]);

        $this->assertDatabaseMissing('review_votes', [
            'review_id' => $this->review->id,
            'user_id' => $this->voter->id,
        ]);

        $this->assertEquals(0, $this->review->fresh()->helpful_votes);
    }

    public function test_user_can_switch_vote_between_helpful_and_unhelpful()
    {
        // Vote helpful first
        $this->actingAs($this->voter)->postJson(route('reviews.vote', $this->review->id), [
            'vote_type' => 'helpful',
        ]);

        $this->assertEquals(1, $this->review->fresh()->helpful_votes);
        $this->assertEquals(0, $this->review->fresh()->unhelpful_votes);

        // Switch to unhelpful
        $response = $this->actingAs($this->voter)->postJson(route('reviews.vote', $this->review->id), [
            'vote_type' => 'unhelpful',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'helpful' => 0,
            'unhelpful' => 1,
            'user_vote' => 'unhelpful',
        ]);

        $this->assertDatabaseHas('review_votes', [
            'review_id' => $this->review->id,
            'user_id' => $this->voter->id,
            'vote_type' => 'unhelpful',
        ]);

        $fresh = $this->review->fresh();
        $this->assertEquals(0, $fresh->helpful_votes);
        $this->assertEquals(1, $fresh->unhelpful_votes);
    }

    public function test_user_cannot_vote_on_their_own_review()
    {
        $response = $this->actingAs($this->author)->postJson(route('reviews.vote', $this->review->id), [
            'vote_type' => 'helpful',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'You cannot vote on your own review.',
        ]);

        $this->assertDatabaseMissing('review_votes', [
            'review_id' => $this->review->id,
            'user_id' => $this->author->id,
        ]);

        $this->assertEquals(0, $this->review->fresh()->helpful_votes);
    }

    public function test_guest_cannot_vote_on_review()
    {
        $response = $this->postJson(route('reviews.vote', $this->review->id), [
            'vote_type' => 'helpful',
        ]);

        $response->assertStatus(401);
    }

    public function test_invalid_vote_type_is_rejected()
    {
        $response = $this->actingAs($this->voter)->postJson(route('reviews.vote', $this->review->id), [
            'vote_type' => 'super_awesome',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['vote_type']);
    }

    public function test_user_vote_accessor_returns_correct_state()
    {
        // Not voted yet
        $this->actingAs($this->voter);
        $this->assertNull($this->review->user_vote);

        // Vote helpful
        ReviewVote::create([
            'review_id' => $this->review->id,
            'user_id' => $this->voter->id,
            'vote_type' => 'helpful',
        ]);

        $this->assertEquals('helpful', $this->review->user_vote);
    }
}
