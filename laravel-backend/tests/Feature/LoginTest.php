<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login functionality for admin users.
     */
    public function test_admin_user_can_login_and_redirect_to_dashboard()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'email' => 'admin@kaplato.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Attempt to log in
        $response = $this->post('/login', [
            'email' => 'admin@kaplato.com',
            'password' => 'admin123',
        ]);

        // Assert redirection to dashboard
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test login functionality for non-admin users.
     */
    public function test_non_admin_user_cannot_login()
    {
        // Create a non-admin user
        $user = User::factory()->create([
            'email' => 'user@kaplato.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);

        // Attempt to log in
        $response = $this->post('/login', [
            'email' => 'user@kaplato.com',
            'password' => 'password123',
        ]);

        // Assert redirection back to login with error
        $response->assertSessionHasErrors(['email' => 'Access denied. Only admin users are allowed.']);
        $this->assertGuest();
    }
}