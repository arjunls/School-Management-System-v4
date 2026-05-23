<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_registration_validates_required_fields()
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_registration_rejects_duplicate_email()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_registration_rejects_invalid_role()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_login()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_login_fails_for_inactive_user()
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => Hash::make('password123'),
            'status' => 'inactive',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_get_profile()
    {
        $user = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/auth/profile');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_unauthenticated_user_cannot_get_profile()
    {
        $response = $this->getJson('/api/auth/profile');

        $response->assertStatus(401);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_user_can_change_password()
    {
        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'password' => Hash::make('current_password'),
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/change-password', [
                'current_password' => 'current_password',
                'password' => 'new_password123',
                'password_confirmation' => 'new_password123',
            ]);

        $response->assertStatus(200);
    }

    public function test_change_password_fails_with_wrong_current()
    {
        $user = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
            'password' => Hash::make('current_password'),
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/auth/change-password', [
                'current_password' => 'wrong_password',
                'password' => 'new_password123',
                'password_confirmation' => 'new_password123',
            ]);

        $response->assertStatus(422);
    }

    public function test_forgot_password_validates_email()
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422);
    }
}
