<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can authenticate with valid credentials
     */
    public function test_user_can_authenticate_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
    }

    /**
     * Test user cannot authenticate with invalid credentials
     */
    public function test_user_cannot_authenticate_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Test user can logout
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $this->assertGuest();
    }

    /**
     * Test authenticated user can access protected routes
     */
    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        // Expecting either 200 or redirect, depending on your routing
        $this->assertIn($response->status(), [200, 302]);
    }

    /**
     * Test guest cannot access protected routes
     */
    public function test_guest_cannot_access_protected_routes()
    {
        $response = $this->get('/dashboard');

        $this->assertRedirect('/login');
    }
}
