<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Fee\Models\FeeType;
use App\Modules\Fee\Models\FeeInvoice;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeApiTest extends TestCase
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

    public function test_admin_can_create_fee_type()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/fees/types', [
                'name' => 'SPP',
                'amount' => 500000,
                'frequency' => 'monthly',
            ]);
        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('fee_types', ['name' => 'SPP']);
    }

    public function test_non_admin_cannot_create_fee_type()
    {
        $token = $this->authToken(['role' => 'teacher']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/fees/types', ['name' => 'SPP', 'amount' => 500000, 'frequency' => 'monthly']);
        $response->assertStatus(403);
    }

    public function test_any_authenticated_user_can_view_fee_types()
    {
        FeeType::create(['name' => 'SPP', 'amount' => 500000, 'frequency' => 'monthly']);
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/fees/types');
        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_can_create_invoice()
    {
        $type = FeeType::create(['name' => 'SPP', 'amount' => 500000, 'frequency' => 'monthly']);
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/fees/invoices', [
                'fee_type_id' => $type->id,
                'student_id' => $student->id,
                'due_date' => now()->addMonth()->format('Y-m-d'),
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('fee_invoices', ['fee_type_id' => $type->id, 'amount' => 500000]);
    }

    public function test_admin_can_record_payment()
    {
        $type = FeeType::create(['name' => 'SPP', 'amount' => 500000, 'frequency' => 'monthly']);
        $student = User::factory()->create(['role' => 'student']);
        $invoice = FeeInvoice::create([
            'fee_type_id' => $type->id,
            'student_id' => $student->id,
            'amount' => 500000,
            'due_date' => now()->addMonth()->format('Y-m-d'),
        ]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/fees/invoices/{$invoice->id}/pay", [
                'amount' => 500000,
                'payment_date' => now()->format('Y-m-d'),
                'payment_method' => 'cash',
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('fee_invoices', ['id' => $invoice->id, 'status' => 'paid']);
    }

    public function test_invoice_validation_fails()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/fees/invoices', []);
        $response->assertStatus(422);
    }
}
