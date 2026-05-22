<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    protected function authToken(array $attrs = []): string
    {
        $user = User::factory()->create($attrs + ['status' => 'active']);
        return $user->createToken('test')->plainTextToken;
    }

    // ─── Admin: Create ──────────────────────────────────────

    public function test_admin_can_create_student()
    {
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/students', [
                'name' => 'New Student',
                'email' => 'student@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'student@test.com',
            'role' => 'student',
        ]);
    }

    public function test_non_admin_cannot_create_student()
    {
        foreach (['teacher', 'student', 'parent'] as $role) {
            $token = $this->authToken(['role' => $role]);

            $response = $this->withHeader('Authorization', "Bearer $token")
                ->postJson('/api/students', [
                    'name' => 'Should Fail',
                    'email' => "{$role}@test.com",
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                ]);

            $response->assertStatus(403);
        }
    }

    // ─── Read ───────────────────────────────────────────────

    public function test_any_authenticated_user_can_view_students()
    {
        User::factory(3)->create(['role' => 'student']);

        foreach (['admin', 'teacher', 'student'] as $role) {
            $token = $this->authToken(['role' => $role]);

            $response = $this->withHeader('Authorization', "Bearer $token")
                ->getJson('/api/students');

            $response->assertStatus(200)
                ->assertJson(['success' => true]);
        }
    }

    public function test_unauthenticated_user_cannot_view_students()
    {
        $response = $this->getJson('/api/students');
        $response->assertStatus(401);
    }

    public function test_can_view_single_student()
    {
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/students/{$student->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $student->id);
    }

    public function test_returns_404_for_nonexistent_student()
    {
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/students/99999');

        $response->assertStatus(404);
    }

    // ─── Filtering ──────────────────────────────────────────

    public function test_students_can_be_filtered_by_status()
    {
        User::factory()->create(['role' => 'student', 'status' => 'active']);
        User::factory()->create(['role' => 'student', 'status' => 'inactive']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/students?status=inactive');

        $response->assertStatus(200);
        foreach ($response->json('data') as $s) {
            $this->assertEquals('inactive', $s['status']);
        }
    }

    public function test_students_list_does_not_include_other_roles()
    {
        User::factory()->create(['role' => 'student']);
        User::factory()->create(['role' => 'teacher']);
        User::factory()->create(['role' => 'admin']);

        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/students');

        $response->assertStatus(200);
        foreach ($response->json('data') as $s) {
            $this->assertEquals('student', $s['role']);
        }
    }

    // ─── Update ─────────────────────────────────────────────

    public function test_admin_can_update_student()
    {
        $student = User::factory()->create(['role' => 'student', 'name' => 'Old Name']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/students/{$student->id}", ['name' => 'Updated Name']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $student->id, 'name' => 'Updated Name']);
    }

    public function test_non_admin_cannot_update_student()
    {
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'student']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/students/{$student->id}", ['name' => 'Hacked']);

        $response->assertStatus(403);
    }

    // ─── Delete ─────────────────────────────────────────────

    public function test_admin_can_delete_student()
    {
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/students/{$student->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $student->id]);
    }

    public function test_non_admin_cannot_delete_student()
    {
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'teacher']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/students/{$student->id}");

        $response->assertStatus(403);
    }

    // ─── Pagination ─────────────────────────────────────────

    public function test_students_are_paginated()
    {
        User::factory(20)->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/students/paginated?per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure(['pagination' => ['total', 'per_page', 'current_page']])
            ->assertJsonPath('pagination.per_page', 5);
    }
}
