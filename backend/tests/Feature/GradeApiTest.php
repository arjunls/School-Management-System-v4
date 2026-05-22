<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Grade\Models\Grade;
use App\Modules\Subject\Models\Subject;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradeApiTest extends TestCase
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

    public function test_admin_can_create_grade()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/grades', [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'score' => 85,
                'grade' => 'A',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
    }

    public function test_non_admin_cannot_create_grade()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/grades', ['student_id' => 1, 'subject_id' => 1]);
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_view_grades()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/grades');
        $response->assertStatus(200);
    }

    public function test_admin_can_update_grade()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $grade = Grade::create(['student_id' => $student->id, 'subject_id' => $subject->id, 'score' => 50]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/grades/{$grade->id}", ['score' => 95]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('grades', ['id' => $grade->id, 'score' => 95]);
    }

    public function test_grade_score_must_be_within_range()
    {
        $student = User::factory()->create(['role' => 'student']);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/grades', [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'score' => 150,
            ]);

        $response->assertStatus(422);
    }
}
