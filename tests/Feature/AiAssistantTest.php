<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Vendor;
use App\Models\Order;
use App\Models\CustomerInquiry;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AiAssistantTest extends TestCase
{
    use DatabaseTransactions;

    public function test_ai_suggestions_endpoint_returns_starter_prompts()
    {
        $response = $this->getJson(route('ai.suggestions'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'bot_name',
            'prompts' => [
                '*' => ['label', 'query']
            ]
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('prompts'));
    }

    public function test_ai_chat_general_query_returns_smart_reply()
    {
        $response = $this->postJson(route('ai.chat'), [
            'message' => 'Hello! What can you help me with?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertNotEmpty($response->json('reply'));
        $this->assertEquals('general', $response->json('intent'));
    }

    public function test_ai_chat_product_search_finds_relevant_products()
    {
        // Ensure a product exists with known keyword
        $category = Category::first() ?? Category::create(['name' => 'Acoustics', 'slug' => 'acoustics']);
        $vendorUser = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::create([
            'user_id' => $vendorUser->id,
            'shop_name' => 'Studio Acoustics',
            'shop_slug' => 'studio-acoustics-' . rand(100, 999),
            'status' => 'active',
            'commission_rate' => 8.5,
        ]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $category->id,
            'name' => 'Apex Studio Pro Headphones',
            'slug' => 'apex-studio-pro-' . rand(100, 999),
            'sku' => 'APX-' . rand(1000, 9999),
            'price' => 179.00,
            'stock_quantity' => 15,
            'is_active' => true,
            'status' => 'approved',
            'rating_avg' => 4.9,
            'rating_count' => 18,
            'images' => ['https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800'],
        ]);

        $response = $this->postJson(route('ai.chat'), [
            'message' => 'Find me headphones under $200',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('products'));

        $matchedIds = collect($response->json('products'))->pluck('id')->toArray();
        $this->assertContains($product->id, $matchedIds);
    }

    public function test_ai_chat_product_comparison_generates_comparison()
    {
        $response = $this->postJson(route('ai.chat'), [
            'message' => 'Compare headphones vs speakers and tell me which is better',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals('comparison', $response->json('intent'));
        $this->assertNotEmpty($response->json('reply'));
        $this->assertNotNull($response->json('comparison'));
    }

    public function test_ai_chat_order_tracking_recognizes_order_number()
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'order_number' => 'ORD-AI998877',
            'user_id' => $user->id,
            'subtotal' => 150.00,
            'total_amount' => 165.00,
            'shipping_address' => '100 Studio Way, Tech City, CA 94016',
            'billing_address' => '100 Studio Way, Tech City, CA 94016',
            'payment_method' => 'card',
            'order_status' => 'shipped',
            'payment_status' => 'paid',
            'carrier_name' => 'DHL Express',
            'tracking_number' => 'DHL-TRACK-9988',
        ]);

        $response = $this->actingAs($user)->postJson(route('ai.chat'), [
            'message' => 'Can you track order ORD-AI998877?',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals('tracking', $response->json('intent'));
        $this->assertNotNull($response->json('order'));
        $this->assertEquals('ORD-AI998877', $response->json('order.order_number'));
        $this->assertStringContainsString('Shipped', $response->json('order.status'));
    }

    public function test_ai_human_handoff_creates_support_inquiry()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->postJson(route('ai.handoff'), [
            'subject' => 'Need advice on custom mechanical switch warranty',
            'message' => 'I would like to speak directly with an employee regarding switch lubrications.',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertNotNull($response->json('ticket_id'));

        $this->assertDatabaseHas('customer_inquiries', [
            'id' => $response->json('ticket_id'),
            'user_id' => $customer->id,
            'subject' => 'Need advice on custom mechanical switch warranty',
        ]);
    }

    public function test_ai_clear_endpoint_flushes_session()
    {
        session(['ai_chat_history' => [
            ['role' => 'user', 'text' => 'Hello'],
            ['role' => 'assistant', 'text' => 'Hi there'],
        ]]);

        $response = $this->postJson(route('ai.clear'));

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertFalse(session()->has('ai_chat_history'));
    }

    public function test_ai_openrouter_provider_handling()
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Hello from Ling 3.2 Flash on OpenRouter!',
                        ],
                    ],
                ],
            ], 200),
        ]);

        config([
            'ai.provider' => 'openrouter',
            'ai.openrouter.api_key' => 'sk-or-v1-fake-openrouter-key',
            'ai.openrouter.model' => 'inclusionai/ling-3.2-flash:free',
        ]);

        $response = $this->postJson(route('ai.chat'), [
            'message' => 'Hello Ling!',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('openrouter', $response->json('provider_used'));
        $this->assertStringContainsString('Hello from Ling 3.2 Flash on OpenRouter!', $response->json('reply'));
    }
}
