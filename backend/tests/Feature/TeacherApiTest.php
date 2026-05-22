<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherApiTest extends TestCase
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

    public function test_admin_can_create_teacher()
    {
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/teachers', [
                'name' => 'New Teacher',
                'email' => 'teacher@test.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('users', ['email' => 'teacher@test.com', 'role' => 'teacher']);
    }

    public function test_non_admin_cannot_create_teacher()
    {
        foreach (['teacher', 'student', 'parent'] as $role) {
            $token = $this->authToken(['role' => $role]);
            $response = $this->withHeader('Authorization', "Bearer $token")
                ->postJson('/api/teachers', [
                    'name' => 'Fail', 'email' => "{$role}@test.com",
                    'password' => 'password123', 'password_confirmation' => 'password123',
                ]);
            $response->assertStatus(403);
        }
    }

    public function test_authenticated_user_can_view_teachers()
    {
        User::factory(2)->create(['role' => 'teacher']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/teachers');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_can_update_teacher()
    {
        $teacher = User::factory()->create(['role' => 'teacher', 'name' => 'Old']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/teachers/{$teacher->id}", ['name' => 'Updated']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $teacher->id, 'name' => 'Updated']);
    }

    public function test_admin_can_delete_teacher()
    {
        $teacher = User::factory()->create(['role' => 'teacher']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/teachers/{$teacher->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $teacher->id]);
    }

    public function test_teachers_list_only_includes_teachers()
    {
        User::factory()->create(['role' => 'teacher']);
        User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/teachers');

        $response->assertStatus(200);
        foreach ($response->json('data') as $t) {
            $this->assertEquals('teacher', $t['role']);
        }
    }

    public function test_teachers_are_paginated()
    {
        User::factory(15)->create(['role' => 'teacher']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/teachers/paginated?per_page=5');

        $response->assertStatus(200)
            ->assertJsonPath('pagination.per_page', 5);
    }
}
