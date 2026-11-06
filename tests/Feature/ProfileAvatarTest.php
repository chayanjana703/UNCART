<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProfileAvatarTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_view_profile_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.index'));
        $response->assertStatus(200);
        $response->assertSee('Personal Information');
        $response->assertSee('Profile Picture');
    }

    public function test_user_can_upload_avatar_via_direct_endpoint()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('profile-picture.jpg', 400, 400);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('profile.avatar.update'), [
                'avatar_file' => $file,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('/storage/avatars/', $user->avatar);

        $storedPath = str_replace('/storage/', '', $user->avatar);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_user_can_delete_custom_avatar()
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'avatar' => '/storage/avatars/old-photo.jpg',
        ]);
        Storage::disk('public')->put('avatars/old-photo.jpg', 'fake-image-content');

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete(route('profile.avatar.delete'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $user->refresh();
        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing('avatars/old-photo.jpg');
    }

    public function test_user_can_update_profile_and_avatar_via_form()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('new-avatar.png', 300, 300);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => 'Updated Profile Name',
            'phone' => '+1 555 123 4567',
            'bio' => 'Passionate about precision engineering',
            'avatar_file' => $file,
        ]);

        $response->assertRedirect();
        $user->refresh();

        $this->assertEquals('Updated Profile Name', $user->name);
        $this->assertEquals('+1 555 123 4567', $user->phone);
        $this->assertEquals('Passionate about precision engineering', $user->bio);
        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('/storage/avatars/', $user->avatar);
    }

    public function test_avatar_rejects_invalid_file_type()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $invalidFile = UploadedFile::fake()->create('malicious-document.pdf', 100);

        $response = $this->actingAs($user)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('profile.avatar.update'), [
                'avatar_file' => $invalidFile,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['avatar_file']);
    }
}
