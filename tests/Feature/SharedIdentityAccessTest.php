<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SharedIdentityAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_teacher_can_open_q_space_dashboard(): void
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
            'is_active' => true,
        ]);

        $this->actingAs($teacher)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_student_cannot_open_q_space_dashboard(): void
    {
        $student = User::factory()->create([
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertRedirect(route('welcome'))
            ->assertSessionHas('error');
    }

    public function test_inactive_teacher_cannot_open_q_space_dashboard(): void
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
            'is_active' => false,
        ]);

        $this->actingAs($teacher)
            ->get('/dashboard')
            ->assertRedirect(route('welcome'))
            ->assertSessionHas('error');
    }

    public function test_google_drive_oauth_cannot_be_used_by_a_guest_to_create_an_account(): void
    {
        $this->get('/auth/google/redirect')
            ->assertRedirect(
                'https://q-link.my.id/login?redirect='.urlencode('http://space.q-link.my.id/dashboard')
            );
    }
}
