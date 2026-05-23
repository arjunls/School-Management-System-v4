<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
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

    public function test_admin_can_create_event()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/events', [
                'title' => 'School Exam',
                'start_date' => '2026-06-01',
                'type' => 'exam',
            ]);
        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('events', ['title' => 'School Exam']);
    }

    public function test_non_admin_teacher_cannot_create_event()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/events', [
                'title' => 'Event', 'start_date' => '2026-06-01', 'type' => 'academic',
            ]);
        $response->assertStatus(403);
    }

    public function test_any_user_can_view_events()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/events');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_teacher_can_create_event()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/events', [
                'title' => 'Field Trip',
                'start_date' => '2026-06-15',
                'type' => 'academic',
            ]);
        $response->assertStatus(201);
    }

    public function test_event_requires_valid_type()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/events', [
                'title' => 'Invalid', 'start_date' => '2026-06-01', 'type' => 'invalid_type',
            ]);
        $response->assertStatus(422);
    }
}
