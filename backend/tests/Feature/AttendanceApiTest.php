<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceApiTest extends TestCase
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

    public function test_admin_can_create_attendance()
    {
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/attendance', [
                'student_id' => $student->id,
                'date' => now()->format('Y-m-d'),
                'status' => 'present',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
    }

    public function test_non_admin_cannot_create_attendance()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/attendance', [
                'student_id' => 1, 'date' => now()->format('Y-m-d'), 'status' => 'present',
            ]);
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_view_attendance()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/attendance');
        $response->assertStatus(200);
    }

    public function test_attendance_validates_status()
    {
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/attendance', [
                'student_id' => $student->id,
                'date' => now()->format('Y-m-d'),
                'status' => 'invalid_status',
            ]);

        $response->assertStatus(422);
    }
}
