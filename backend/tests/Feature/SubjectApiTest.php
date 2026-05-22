<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Subject\Models\Subject;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectApiTest extends TestCase
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

    public function test_admin_can_create_subject()
    {
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/subjects', [
                'name' => 'Mathematics',
                'code' => 'MATH101',
                'credits' => 4,
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('subjects', ['code' => 'MATH101']);
    }

    public function test_non_admin_cannot_create_subject()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/subjects', ['name' => 'Physics', 'code' => 'PHY101']);
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_view_subjects()
    {
        Subject::insert([
            ['name' => 'Math', 'code' => 'MTH', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Science', 'code' => 'SCI', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/subjects');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_can_update_subject()
    {
        $subject = Subject::create(['name' => 'Old', 'code' => 'OLD']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/subjects/{$subject->id}", ['name' => 'Updated']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => 'Updated']);
    }

    public function test_admin_can_delete_subject()
    {
        $subject = Subject::create(['name' => 'Temp', 'code' => 'TMP']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/subjects/{$subject->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_subject_code_must_be_unique()
    {
        $token = $this->authToken(['role' => 'admin']);
        Subject::create(['name' => 'First', 'code' => 'SAME']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/subjects', ['name' => 'Second', 'code' => 'SAME']);

        $response->assertStatus(422);
    }
}
