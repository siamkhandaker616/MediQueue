<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGoogleUser(): SocialiteUser
    {
        $googleUser = new SocialiteUser();
        $googleUser->map([
            'id' => '1234567890',
            'nickname' => 'googleuser',
            'name' => 'Google User',
            'email' => 'google@example.com',
        ]);

        return $googleUser;
    }

    public function test_google_redirect_sends_user_to_google(): void
    {
        $this->get('/auth/google/redirect')
            ->assertRedirect();
    }

    public function test_google_callback_creates_and_logs_in_a_new_user(): void
    {
        Socialite::fake('google', $this->fakeGoogleUser());

        $this->get('/auth/google/callback')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Google User',
            'email' => 'google@example.com',
        ]);
    }

    public function test_google_callback_logs_in_an_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'google@example.com',
            'name' => 'Original Name',
        ]);

        Socialite::fake('google', $this->fakeGoogleUser());

        $this->get('/auth/google/callback')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame('Original Name', $user->fresh()->name);
    }
}
