<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentApiTest extends TestCase
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

    public function test_teacher_can_create_assignment()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $token = $this->authToken(['role' => 'teacher']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/assignments', [
                'title' => 'Homework 1',
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'due_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('assignments', ['title' => 'Homework 1']);
    }

    public function test_student_cannot_create_assignment()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/assignments', [
                'title' => 'HW', 'class_id' => 1, 'subject_id' => 1,
                'due_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            ]);
        $response->assertStatus(403);
    }

    public function test_any_authenticated_user_can_view_assignments()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")->getJson('/api/assignments');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_student_can_submit_assignment()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $assignment = \App\Modules\Learning\Assignment\Models\Assignment::create([
            'title' => 'HW', 'class_id' => $class->id, 'subject_id' => $subject->id,
            'teacher_id' => $teacher->id, 'due_date' => now()->addWeek(),
        ]);

        $token = $this->authToken(['role' => 'student']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/assignments/{$assignment->id}/submit", [
                'notes' => 'Here is my work',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('submissions', ['assignment_id' => $assignment->id, 'notes' => 'Here is my work']);
    }

    public function test_teacher_can_grade_submission()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $tToken = $teacher->createToken('test')->plainTextToken;

        $assignment = \App\Modules\Learning\Assignment\Models\Assignment::create([
            'title' => 'HW', 'class_id' => $class->id, 'subject_id' => $subject->id,
            'teacher_id' => $teacher->id, 'due_date' => now()->addWeek(), 'max_score' => 100,
        ]);

        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $submission = $assignment->submissions()->create([
            'student_id' => $student->id, 'notes' => 'My work', 'submitted_at' => now(),
        ]);

        $response = $this->withHeader('Authorization', "Bearer $tToken")
            ->postJson("/api/assignments/{$assignment->id}/submissions/{$submission->id}/grade", [
                'score' => 85,
                'feedback' => 'Good job!',
            ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('submissions', ['id' => $submission->id, 'score' => 85]);
    }
}
