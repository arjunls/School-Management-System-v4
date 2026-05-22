<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Class\Models\Kelas;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassApiTest extends TestCase
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

    public function test_admin_can_create_class()
    {
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/classes', [
                'name' => 'X A',
                'grade_level' => 10,
                'capacity' => 30,
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('kelas', ['name' => 'X A']);
    }

    public function test_non_admin_cannot_create_class()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/classes', ['name' => 'X B', 'grade_level' => 10]);
        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_view_classes()
    {
        Kelas::insert([
            ['name' => 'X A', 'grade_level' => 10, 'capacity' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'XI A', 'grade_level' => 11, 'capacity' => 30, 'created_at' => now(), 'updated_at' => now()],
        ]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/classes');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_can_update_class()
    {
        $class = Kelas::create(['name' => 'Old Name', 'grade_level' => 10]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/classes/{$class->id}", ['name' => 'Updated']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('kelas', ['id' => $class->id, 'name' => 'Updated']);
    }

    public function test_admin_can_delete_class()
    {
        $class = Kelas::create(['name' => 'Temp', 'grade_level' => 10]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/classes/{$class->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('kelas', ['id' => $class->id]);
    }

    public function test_admin_can_add_student_to_class()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/classes/{$class->id}/students/{$student->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $student->id, 'kelas_id' => $class->id]);
    }

    public function test_admin_can_remove_student_from_class()
    {
        $class = Kelas::create(['name' => 'X A', 'grade_level' => 10]);
        $student = User::factory()->create(['role' => 'student', 'kelas_id' => $class->id]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/classes/{$class->id}/students/{$student->id}");

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['id' => $student->id, 'kelas_id' => null]);
    }

    public function test_unauthenticated_user_cannot_access_classes()
    {
        $response = $this->getJson('/api/classes');
        $response->assertStatus(401);
    }
}
