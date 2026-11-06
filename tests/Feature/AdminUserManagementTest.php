<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminUserManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_update_user_via_put_resource_route()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create([
            'name' => 'Original Customer',
            'email' => 'original.customer@test.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($admin)->put('/admin/users/' . $targetUser->id, [
            'name' => 'Updated Customer Name',
            'email' => 'updated.customer@test.com',
            'phone' => '+1 555 987 6543',
            'role' => 'vendor',
            'password' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertEquals('Updated Customer Name', $targetUser->name);
        $this->assertEquals('updated.customer@test.com', $targetUser->email);
        $this->assertEquals('+1 555 987 6543', $targetUser->phone);
        $this->assertEquals('vendor', $targetUser->role);
    }

    public function test_admin_can_update_user_via_update_suffix_route()
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $targetUser = User::factory()->create([
            'name' => 'Original Name 2',
            'email' => 'original2@test.com',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($admin)->put('/admin/users/' . $targetUser->id . '/update', [
            'name' => 'Updated Suffix Name',
            'email' => 'updated.suffix@test.com',
            'phone' => '+1 555 111 2222',
            'role' => 'customer',
            'password' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertEquals('Updated Suffix Name', $targetUser->name);
        $this->assertEquals('updated.suffix@test.com', $targetUser->email);
    }

    public function test_non_admin_cannot_update_users()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $targetUser = User::factory()->create();

        $response = $this->actingAs($customer)->put('/admin/users/' . $targetUser->id, [
            'name' => 'Hacked Name',
            'email' => 'hacked@test.com',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }
}
