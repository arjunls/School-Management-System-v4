<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Library\Models\Book;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryApiTest extends TestCase
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

    public function test_admin_can_create_book()
    {
        $token = $this->authToken(['role' => 'admin']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/library/books', [
                'title' => 'Mathematics 101',
                'author' => 'John Doe',
                'isbn' => '978-3-16-148410-0',
            ]);
        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('books', ['isbn' => '978-3-16-148410-0']);
    }

    public function test_non_admin_cannot_create_book()
    {
        $token = $this->authToken(['role' => 'student']);
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/library/books', [
                'title' => 'Test', 'author' => 'Author', 'isbn' => '123',
            ]);
        $response->assertStatus(403);
    }

    public function test_any_user_can_view_books()
    {
        Book::create(['title' => 'Math', 'author' => 'John', 'isbn' => '111', 'total_copies' => 5, 'available_copies' => 5]);
        $token = $this->authToken(['role' => 'student']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/library/books');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_admin_can_loan_book()
    {
        $book = Book::create(['title' => 'Math', 'author' => 'John', 'isbn' => '111', 'total_copies' => 5, 'available_copies' => 5]);
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/library/loans', [
                'book_id' => $book->id,
                'user_id' => $student->id,
                'due_date' => now()->addDays(14)->format('Y-m-d'),
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('book_loans', ['book_id' => $book->id, 'status' => 'borrowed']);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'available_copies' => 4]);
    }

    public function test_cannot_loan_unavailable_book()
    {
        $book = Book::create(['title' => 'Math', 'author' => 'John', 'isbn' => '111', 'total_copies' => 1, 'available_copies' => 0]);
        $student = User::factory()->create(['role' => 'student']);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/library/loans', [
                'book_id' => $book->id,
                'user_id' => $student->id,
                'due_date' => now()->addDays(14)->format('Y-m-d'),
            ]);

        $response->assertStatus(400);
    }

    public function test_admin_can_return_loan()
    {
        $book = Book::create(['title' => 'Math', 'author' => 'John', 'isbn' => '111', 'total_copies' => 5, 'available_copies' => 4]);
        $loan = \App\Modules\Library\Models\BookLoan::create([
            'book_id' => $book->id,
            'user_id' => User::factory()->create(['role' => 'student'])->id,
            'loan_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'status' => 'borrowed',
        ]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/library/loans/{$loan->id}/return");

        $response->assertStatus(200);
        $this->assertDatabaseHas('book_loans', ['id' => $loan->id, 'status' => 'returned']);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'available_copies' => 5]);
    }

    public function test_duplicate_isbn_fails()
    {
        Book::create(['title' => 'Math', 'author' => 'John', 'isbn' => '111', 'total_copies' => 5, 'available_copies' => 5]);
        $token = $this->authToken(['role' => 'admin']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/library/books', [
                'title' => 'Another', 'author' => 'Jane', 'isbn' => '111',
            ]);
        $response->assertStatus(422);
    }
}
