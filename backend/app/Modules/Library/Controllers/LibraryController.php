<?php

namespace App\Modules\Library\Controllers;

use App\Http\Controllers\Controller;
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
        return response()->json(['success' => true, 'data' => $query->orderBy('title')->paginate($request->per_page ?? 20)]);
    }

    /**
     * Add a new book
     */
    public function bookStore(StoreBookRequest $request)
    {
        $data = $request->validated();
        $data['available_copies'] = $data['total_copies'] ?? 1;
        $book = Book::create($data);
        return response()->json(['success' => true, 'data' => $book, 'message' => 'Book added'], 201);
    }

    /**
     * Update a book
     */
    public function bookUpdate(UpdateBookRequest $request, int $id)
    {
        $book = Book::findOrFail($id);
        $book->update($request->validated());
        return response()->json(['success' => true, 'data' => $book, 'message' => 'Book updated']);
    }

    /**
     * Delete a book
     */
    public function bookDelete(int $id)
    {
        Book::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Book deleted']);
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
        return response()->json(['success' => true, 'data' => $query->orderByDesc('loan_date')->paginate($request->per_page ?? 20)]);
    }

    /**
     * Create a new book loan
     */
    public function loanStore(StoreLoanRequest $request)
    {
        $data = $request->validated();
        $book = Book::findOrFail($data['book_id']);

        if ($book->available_copies < 1) {
            return response()->json(['success' => false, 'message' => 'No copies available'], 400);
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
        return response()->json(['success' => true, 'data' => $loan, 'message' => 'Book loaned'], 201);
    }

    /**
     * Return a borrowed book
     */
    public function loanReturn(int $id)
    {
        $loan = BookLoan::with('book')->findOrFail($id);
        if ($loan->status === 'returned') {
            return response()->json(['success' => false, 'message' => 'Already returned'], 400);
        }

        $loan->update(['returned_date' => now()->format('Y-m-d'), 'status' => 'returned']);
        $loan->book->increment('available_copies');

        return response()->json(['success' => true, 'message' => 'Book returned']);
    }
}
