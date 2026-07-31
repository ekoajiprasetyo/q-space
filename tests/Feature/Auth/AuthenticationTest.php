<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_centralized_in_q_link(): void
    {
        $response = $this->get('/login');

        $response->assertRedirect(
            'https://q-link.my.id/login?redirect='.urlencode('http://space.q-link.my.id/dashboard')
        );
    }

    public function test_login_post_cannot_authenticate_against_q_space(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(
            'https://q-link.my.id/login?redirect='.urlencode('http://space.q-link.my.id/dashboard')
        );
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
