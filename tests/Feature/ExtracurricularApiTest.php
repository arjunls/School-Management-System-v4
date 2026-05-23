<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\StudentLife\Extracurricular\Models\Extracurricular;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtracurricularApiTest extends TestCase
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

    public function test_admin_can_create_extracurricular()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/extracurriculars', [
                'name' => 'Basketball',
                'coach' => 'Mr. Smith',
            ]);
        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('extracurriculars', ['name' => 'Basketball']);
    }

    public function test_non_admin_teacher_cannot_create()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/extracurriculars', ['name' => 'Club']);
        $response->assertStatus(403);
    }

    public function test_any_user_can_view()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/extracurriculars');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_teacher_can_create()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/extracurriculars', ['name' => 'Chess Club']);
        $response->assertStatus(201);
    }

    public function test_student_can_join_and_leave()
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $student->createToken('test')->plainTextToken;

        $ec = Extracurricular::create(['name' => 'Basketball', 'max_participants' => 20]);

        $joinResponse = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/extracurriculars/{$ec->id}/join");
        $joinResponse->assertStatus(200)->assertJson(['success' => true]);

        $leaveResponse = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/extracurriculars/{$ec->id}/leave");
        $leaveResponse->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_student_cannot_join_twice()
    {
        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $student->createToken('test')->plainTextToken;

        $ec = Extracurricular::create(['name' => 'Basketball', 'max_participants' => 20]);
        $ec->participants()->attach($student->id, ['joined_at' => now()->format('Y-m-d'), 'status' => 'active']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/extracurriculars/{$ec->id}/join");
        $response->assertStatus(400);
    }
}
