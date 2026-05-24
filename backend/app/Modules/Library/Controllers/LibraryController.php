<?php

namespace App\Modules\Library\Controllers;

use App\Kernel\Http\Controllers\Controller;
use App\Modules\Library\Models\Book;
use App\Modules\Library\Models\BookLoan;
use Illuminate\Http\Request;
use App\Modules\Library\Requests\StoreBookRequest;
use App\Modules\Library\Requests\UpdateBookRequest;
use App\Modules\Library\Requests\StoreLoanRequest;

/**
 * @group Library
 *
 * APIs for managing library
 */
class LibraryController extends Controller
{
    /**
     * List all books
     */
    public function books(Request $request)
    {
        $query = Book::query();
        if ($s = $request->search) $query->where(function ($q) use ($s) { $q->where('title', 'like', "%{$s}%")->orWhere('author', 'like', "%{$s}%")->orWhere('isbn', 'like', "%{$s}%"); });
        if ($c = $request->category) $query->where('category', $c);
        return $this->paginated($query->orderBy('title')->paginate($request->per_page ?? 20));
    }

    /**
     * Add a new book
     */
    public function bookStore(StoreBookRequest $request)
    {
        $data = $request->validated();
        $data['available_copies'] = $data['total_copies'] ?? 1;
        $book = Book::create($data);
        return $this->created($book, 'Book added');
    }

    /**
     * Update a book
     */
    public function bookUpdate(UpdateBookRequest $request, int $id)
    {
        $book = Book::findOrFail($id);
        $book->update($request->validated());
        return $this->success($book, 'Book updated');
    }

    /**
     * Delete a book
     */
    public function bookDelete(int $id)
    {
        Book::findOrFail($id)->delete();
        return $this->deleted('Book deleted');
    }

    // Loans
    /**
     * List all book loans
     */
    public function loans(Request $request)
    {
        $user = $request->user();
        $query = BookLoan::with(['book:id,title,author,isbn', 'user:id,name']);

        if ($user->role === 'student') $query->where('user_id', $user->id);

        if ($s = $request->status) $query->where('status', $s);
        return $this->paginated($query->orderByDesc('loan_date')->paginate($request->per_page ?? 20));
    }

    /**
     * Create a new book loan
     */
    public function loanStore(StoreLoanRequest $request)
    {
        $data = $request->validated();
        $book = Book::findOrFail($data['book_id']);

        if ($book->available_copies < 1) {
            return $this->error('No copies available', 400);
        }

        $loan = BookLoan::create([
            'book_id' => $data['book_id'],
            'user_id' => $data['user_id'],
            'loan_date' => now()->format('Y-m-d'),
            'due_date' => $data['due_date'],
            'notes' => $data['notes'] ?? null,
            'status' => 'borrowed',
        ]);

        $book->decrement('available_copies');
        return $this->created($loan, 'Book loaned');
    }

    /**
     * Return a borrowed book
     */
    public function loanReturn(int $id)
    {
        $loan = BookLoan::with('book')->findOrFail($id);
        if ($loan->status === 'returned') {
            return $this->error('Already returned', 400);
        }

        $loan->update(['returned_date' => now()->format('Y-m-d'), 'status' => 'returned']);
        $loan->book->increment('available_copies');

        return $this->success(null, 'Book returned');
    }
}
