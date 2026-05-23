<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementApiTest extends TestCase
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

    public function test_admin_can_create_announcement()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/announcements', [
                'title' => 'School Holiday',
                'content' => 'School will be closed on Friday.',
                'target_role' => 'all',
            ]);
        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('announcements', ['title' => 'School Holiday']);
    }

    public function test_non_admin_teacher_cannot_create_announcement()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/announcements', [
                'title' => 'Test', 'content' => 'Content', 'target_role' => 'all',
            ]);
        $response->assertStatus(403);
    }

    public function test_any_user_can_view_announcements()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/announcements');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_teacher_can_create_announcement()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/announcements', [
                'title' => 'Class Meeting',
                'content' => 'Meeting tomorrow.',
                'target_role' => 'student',
            ]);
        $response->assertStatus(201);
    }

    public function test_validation_fails_for_missing_fields()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/announcements', []);
        $response->assertStatus(422);
    }
}
