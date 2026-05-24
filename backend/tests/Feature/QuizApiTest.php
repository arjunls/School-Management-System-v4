<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use App\Modules\Learning\Quiz\Models\Quiz;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizApiTest extends TestCase
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

    public function test_teacher_can_create_quiz()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $token = $this->authToken(['role' => 'teacher']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/quizzes', [
                'title' => 'Midterm',
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'status' => 'draft',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('quizzes', ['title' => 'Midterm']);
    }

    public function test_student_cannot_create_quiz()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/quizzes', ['title' => 'Quiz', 'class_id' => 1, 'subject_id' => 1, 'status' => 'draft']);
        $response->assertStatus(403);
    }

    public function test_teacher_can_add_question()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);
        $token = $teacher->createToken('test')->plainTextToken;

        $quiz = Quiz::create([
            'title' => 'Quiz', 'class_id' => $class->id, 'subject_id' => $subject->id,
            'teacher_id' => $teacher->id, 'status' => 'draft',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/quizzes/{$quiz->id}/questions", [
                'question_text' => 'What is 2+2?',
                'type' => 'multiple_choice',
                'options' => ['3', '4', '5'],
                'correct_answer' => '4',
                'points' => 10,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('quiz_questions', ['quiz_id' => $quiz->id, 'question_text' => 'What is 2+2?']);
    }

    public function test_student_can_start_and_submit_quiz()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $quiz = Quiz::create([
            'title' => 'Quiz', 'class_id' => $class->id, 'subject_id' => $subject->id,
            'teacher_id' => $teacher->id, 'status' => 'published',
        ]);
        $q = $quiz->questions()->create([
            'question_text' => '2+2?', 'type' => 'multiple_choice',
            'options' => ['3', '4', '5'], 'correct_answer' => '4', 'points' => 10,
        ]);

        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $student->createToken('test')->plainTextToken;

        $startResponse = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/quizzes/{$quiz->id}/start");
        $startResponse->assertStatus(200);
        $attemptId = $startResponse->json('data.attempt.id');

        $submitResponse = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/quizzes/attempts/{$attemptId}/submit", [
                'answers' => [['question_id' => $q->id, 'answer_text' => '4']],
            ]);
        $submitResponse->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_student_cannot_submit_twice()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $teacher = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $quiz = Quiz::create([
            'title' => 'Quiz', 'class_id' => $class->id, 'subject_id' => $subject->id,
            'teacher_id' => $teacher->id, 'status' => 'published',
        ]);
        $q = $quiz->questions()->create([
            'question_text' => '2+2?', 'type' => 'multiple_choice',
            'options' => ['3', '4', '5'], 'correct_answer' => '4', 'points' => 10,
        ]);

        $student = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $student->createToken('test')->plainTextToken;

        $startResponse = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/quizzes/{$quiz->id}/start");
        $attemptId = $startResponse->json('data.attempt.id');

        $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/quizzes/attempts/{$attemptId}/submit", [
                'answers' => [['question_id' => $q->id, 'answer_text' => '4']],
            ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/quizzes/attempts/{$attemptId}/submit", [
                'answers' => [['question_id' => $q->id, 'answer_text' => '5']],
            ]);
        $response->assertStatus(400);
    }
}
