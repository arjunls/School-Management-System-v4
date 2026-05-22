<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadApiTest extends TestCase
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

    public function test_any_authenticated_user_can_upload_photo()
    {
        $token = $this->authToken(['role' => 'student']);

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/upload/photo', ['file' => $file]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_photo_validation_rejects_non_image()
    {
        $token = $this->authToken(['role' => 'student']);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/upload/photo', ['file' => $file]);

        $response->assertStatus(422);
    }

    public function test_any_authenticated_user_can_upload_document()
    {
        $token = $this->authToken(['role' => 'teacher']);

        $file = UploadedFile::fake()->create('report.pdf', 500);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/upload/document', ['file' => $file]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}
