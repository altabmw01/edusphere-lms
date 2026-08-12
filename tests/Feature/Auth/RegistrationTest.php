<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register_and_are_always_assigned_the_student_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Learner',
            'email' => 'jane@example.com',
            'phone' => '+15550001111',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('student.dashboard', absolute: false));

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame(User::ROLE_STUDENT, $user->role);
    }

    public function test_registration_cannot_be_used_to_self_assign_a_privileged_role(): void
    {
        // The registration form has no role field, but even if a malicious client
        // injects one directly, RegisterRequest doesn't validate/accept it, so it
        // must be silently ignored and the account still ends up as a student.
        $this->post('/register', [
            'name' => 'Sneaky Admin',
            'email' => 'sneaky@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'sneaky@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame(User::ROLE_STUDENT, $user->role);
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Jane Learner',
            'email' => 'jane@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
