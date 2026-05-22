<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearApiTest extends TestCase
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

    public function test_admin_can_create_academic_year()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/academic-years', [
                'name' => '2025/2026',
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
            ]);
        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('academic_years', ['name' => '2025/2026']);
    }

    public function test_non_admin_cannot_create_academic_year()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/academic-years', ['name' => '2025/2026', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30']);
        $response->assertStatus(403);
    }

    public function test_admin_can_list_academic_years()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/academic-years');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_can_get_active_academic_year()
    {
        $token = $this->authToken(['role' => 'admin']);

        \App\Modules\AcademicYear\Models\AcademicYear::create([
            'name' => 'Active Year', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30', 'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/academic-years/active');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_can_update_academic_year()
    {
        $year = \App\Modules\AcademicYear\Models\AcademicYear::create([
            'name' => 'Old Year', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30',
        ]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/academic-years/{$year->id}", ['name' => 'Updated Year']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('academic_years', ['id' => $year->id, 'name' => 'Updated Year']);
    }

    public function test_admin_can_delete_academic_year()
    {
        $year = \App\Modules\AcademicYear\Models\AcademicYear::create([
            'name' => 'Temp Year', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30',
        ]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/academic-years/{$year->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('academic_years', ['id' => $year->id]);
    }

    public function test_end_date_must_be_after_start_date()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/academic-years', [
                'name' => 'Invalid', 'start_date' => '2026-01-01', 'end_date' => '2025-01-01',
            ]);
        $response->assertStatus(422);
    }

    public function test_admin_can_create_term()
    {
        $year = \App\Modules\AcademicYear\Models\AcademicYear::create([
            'name' => '2025/2026', 'start_date' => '2025-07-01', 'end_date' => '2026-06-30',
        ]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/academic-years/{$year->id}/terms", [
                'academic_year_id' => $year->id,
                'name' => 'Semester 1',
                'start_date' => '2025-07-01',
                'end_date' => '2025-12-31',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('terms', ['name' => 'Semester 1']);
    }

    public function test_unauthenticated_user_cannot_access()
    {
        $response = $this->getJson('/api/academic-years');
        $response->assertStatus(401);
    }
}
