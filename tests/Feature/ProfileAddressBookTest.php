<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProfileAddressBookTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_add_address()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.address.store'), [
            'name' => 'Jane Smith',
            'type' => 'shipping',
            'phone' => '+1 (555) 321-9988',
            'address_line1' => '742 Evergreen Terrace',
            'address_line2' => 'Suite 101',
            'city' => 'Springfield',
            'state' => 'OR',
            'postal_code' => '97477',
            'country' => 'United States',
            'is_default' => '1',
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'addresses']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'name' => 'Jane Smith',
            'phone' => '+1 (555) 321-9988',
            'address_line1' => '742 Evergreen Terrace',
            'city' => 'Springfield',
            'is_default' => 1,
        ]);
    }

    public function test_user_can_add_address_with_legacy_full_name_and_default_fields()
    {
        $user = User::factory()->create(['name' => 'Default User Name']);

        $response = $this->actingAs($user)->post(route('profile.address.store'), [
            'full_name' => 'Bob Builder',
            'phone' => '+1 (555) 999-0000',
            'address_line1' => '100 Construction Way',
            'city' => 'Denver',
            'state' => 'CO',
            'postal_code' => '80202',
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'addresses']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'name' => 'Bob Builder',
            'type' => 'shipping',
            'country' => 'United States',
            'address_line1' => '100 Construction Way',
        ]);
    }

    public function test_user_can_update_address()
    {
        $user = User::factory()->create();
        $addr = Address::create([
            'user_id' => $user->id,
            'name' => 'Old Recipient',
            'phone' => '+1 (555) 000-1111',
            'type' => 'shipping',
            'address_line1' => 'Old Street 1',
            'city' => 'Austin',
            'state' => 'TX',
            'postal_code' => '73301',
            'country' => 'United States',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->put('/profile/address/' . $addr->id . '/update', [
            'name' => 'Updated Recipient',
            'phone' => '+1 (555) 222-3333',
            'type' => 'billing',
            'address_line1' => 'New Avenue 200',
            'city' => 'Dallas',
            'state' => 'TX',
            'postal_code' => '75201',
            'country' => 'United States',
            'is_default' => '1',
        ]);

        $response->assertRedirect(route('profile.index', ['tab' => 'addresses']));
        $response->assertSessionHas('success');

        $addr->refresh();
        $this->assertEquals('Updated Recipient', $addr->name);
        $this->assertEquals('New Avenue 200', $addr->address_line1);
        $this->assertEquals('Dallas', $addr->city);
        $this->assertEquals('billing', $addr->type);
        $this->assertTrue($addr->is_default);
    }

    public function test_user_can_set_default_address()
    {
        $user = User::factory()->create();
        $addr1 = Address::create([
            'user_id' => $user->id,
            'name' => 'Address 1',
            'phone' => '123456',
            'type' => 'shipping',
            'address_line1' => 'Street 1',
            'city' => 'City',
            'state' => 'ST',
            'postal_code' => '12345',
            'country' => 'United States',
            'is_default' => true,
        ]);

        $addr2 = Address::create([
            'user_id' => $user->id,
            'name' => 'Address 2',
            'phone' => '654321',
            'type' => 'shipping',
            'address_line1' => 'Street 2',
            'city' => 'City',
            'state' => 'ST',
            'postal_code' => '12345',
            'country' => 'United States',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->post(route('profile.address.default', $addr2->id));
        $response->assertRedirect(route('profile.index', ['tab' => 'addresses']));

        $addr1->refresh();
        $addr2->refresh();

        $this->assertFalse($addr1->is_default);
        $this->assertTrue($addr2->is_default);
    }

    public function test_user_can_delete_address()
    {
        $user = User::factory()->create();
        $addr = Address::create([
            'user_id' => $user->id,
            'name' => 'Address to delete',
            'phone' => '123456',
            'type' => 'shipping',
            'address_line1' => 'Street 1',
            'city' => 'City',
            'state' => 'ST',
            'postal_code' => '12345',
            'country' => 'United States',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user)->delete(route('profile.address.delete', $addr->id));
        $response->assertRedirect(route('profile.index', ['tab' => 'addresses']));

        $this->assertDatabaseMissing('addresses', ['id' => $addr->id]);
    }

    public function test_user_cannot_update_another_users_address()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $addr = Address::create([
            'user_id' => $user1->id,
            'name' => 'User 1 Address',
            'phone' => '123456',
            'type' => 'shipping',
            'address_line1' => 'Street 1',
            'city' => 'City',
            'state' => 'ST',
            'postal_code' => '12345',
            'country' => 'United States',
            'is_default' => false,
        ]);

        $response = $this->actingAs($user2)->put('/profile/address/' . $addr->id . '/update', [
            'name' => 'Hijacked Name',
            'phone' => '999999',
            'address_line1' => 'Hacked Street',
            'city' => 'Hacked',
            'state' => 'ST',
            'postal_code' => '12345',
        ]);

        $response->assertStatus(404);
    }
}
