<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageApiTest extends TestCase
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

    public function test_user_can_create_conversation()
    {
        $other = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $this->authToken(['role' => 'student']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/messages/conversations', [
                'participant_ids' => [$other->id],
                'subject' => 'Homework help',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
    }

    public function test_user_can_send_message()
    {
        $sender = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $token = $sender->createToken('test')->plainTextToken;
        $receiver = User::factory()->create(['role' => 'teacher', 'status' => 'active']);

        $conv = \App\Modules\Communication\Message\Models\Conversation::create(['subject' => 'Help', 'created_by' => $sender->id]);
        $conv->participants()->attach([$sender->id, $receiver->id]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/messages/send', [
                'conversation_id' => $conv->id,
                'body' => 'Hello, I need help with math.',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
    }

    public function test_user_cannot_send_to_conversation_not_in()
    {
        $userA = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $userB = User::factory()->create(['role' => 'student', 'status' => 'active']);
        $tokenA = $userA->createToken('test')->plainTextToken;

        $conv = \App\Modules\Communication\Message\Models\Conversation::create(['subject' => 'Private', 'created_by' => $userB->id]);
        $conv->participants()->attach([$userB->id]);

        $response = $this->withHeader('Authorization', "Bearer $tokenA")
            ->postJson('/api/messages/send', [
                'conversation_id' => $conv->id,
                'body' => 'Can you see this?',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_view_conversations()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/messages/conversations');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_unauthenticated_cannot_access()
    {
        $response = $this->getJson('/api/messages/conversations');
        $response->assertStatus(401);
    }
}
