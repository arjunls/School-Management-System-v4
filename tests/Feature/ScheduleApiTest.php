<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Academic\Class\Models\Kelas;
use App\Modules\Academic\Subject\Models\Subject;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleApiTest extends TestCase
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

    public function test_admin_can_create_schedule()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $teacher = User::factory()->create(['role' => 'teacher']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/schedules', [
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'day_of_week' => 'monday',
                'start_time' => '08:00',
                'end_time' => '09:30',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
    }

    public function test_non_admin_cannot_create_schedule()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/schedules', [
                'class_id' => 1, 'subject_id' => 1,
                'day_of_week' => 'monday', 'start_time' => '08:00', 'end_time' => '09:30',
            ]);
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_view_schedules()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/schedules');
        $response->assertStatus(200);
    }

    public function test_schedule_validates_time_order()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $subject = Subject::create(['name' => 'Math', 'code' => 'MTH']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/schedules', [
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'day_of_week' => 'monday',
                'start_time' => '10:00',
                'end_time' => '08:00',
            ]);

        $response->assertStatus(422);
    }
}
