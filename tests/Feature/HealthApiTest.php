<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthApiTest extends TestCase
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

    public function test_admin_can_upsert_health_record()
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/health/{$student->id}", [
                'blood_type' => 'O+',
                'allergies' => 'Peanuts',
            ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('health_records', ['student_id' => $student->id, 'blood_type' => 'O+']);
    }

    public function test_any_authenticated_user_can_view_health_record()
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $this->authToken(['role' => 'teacher']);

        \App\Modules\StudentManagement\Health\Models\HealthRecord::create([
            'student_id' => $student->id, 'blood_type' => 'A+',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/health/{$student->id}");

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_student_cannot_upsert()
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $this->authToken(['role' => 'student']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/health/{$student->id}", ['blood_type' => 'B+']);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_cannot_access()
    {
        $response = $this->getJson('/api/health/1');
        $response->assertStatus(401);
    }
}
