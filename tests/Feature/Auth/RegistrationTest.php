<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_master_registration_redirects_back_to_q_space_dashboard(): void
    {
        Config::set('app.auth_bridge.allow_local_registration', false);
        Config::set('app.q_link_master_url', 'https://q-link.my.id');

        $response = $this->get('/register');

        $response->assertRedirect(
            'https://q-link.my.id/register?role=guru&redirect=http%3A%2F%2Fspace.q-link.my.id%2Fdashboard'
        );
    }
}
