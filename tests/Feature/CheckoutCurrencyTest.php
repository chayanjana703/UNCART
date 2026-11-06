<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CheckoutCurrencyTest extends TestCase
{
    use DatabaseTransactions;
    public function test_checkout_page_renders_with_currency_store_integration()
    {
        $user = User::factory()->create();
        $product = Product::first();

        // Add product to cart
        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'is_saved_for_later' => false,
        ]);

        $response = $this->actingAs($user)->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertSee('formatMoney');
        $response->assertSee('$store.currency');
        $response->assertSee('Order Breakdown');
    }

    public function test_cart_page_renders_with_currency_store_integration()
    {
        $user = User::factory()->create();
        $product = Product::first();

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'is_saved_for_later' => false,
        ]);

        $response = $this->actingAs($user)->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee('formatMoney');
        $response->assertSee('Shopping Bag');
    }
}
