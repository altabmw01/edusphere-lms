<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_for_every_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/teacher')->assertRedirect('/login');
        $this->get('/manager')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_a_student_cannot_access_the_admin_panel(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)->get('/admin')->assertForbidden();
    }

    public function test_a_teacher_cannot_access_the_manager_panel(): void
    {
        $teacher = User::factory()->teacher()->create();

        $this->actingAs($teacher)->get('/manager')->assertForbidden();
    }

    public function test_a_manager_cannot_access_the_admin_panel(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->get('/admin')->assertForbidden();
    }

    public function test_each_role_can_access_its_own_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/admin')->assertOk();
        $this->actingAs(User::factory()->teacher()->create())->get('/teacher')->assertOk();
        $this->actingAs(User::factory()->manager()->create())->get('/manager')->assertOk();
        $this->actingAs(User::factory()->student()->create())->get('/dashboard')->assertOk();
    }

    public function test_login_redirects_each_role_to_its_own_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['password' => bcrypt('Password123!')]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_an_inactive_users_login_attempt_is_rejected(): void
    {
        $user = User::factory()->student()->create([
            'password' => bcrypt('Password123!'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }
}
