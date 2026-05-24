<?php namespace App\Modules\Library\Controllers;
use App\Kernel\Http\Controllers\Controller; use App\Modules\Library\Models\Book; use App\Modules\Library\Models\BookLoan; use App\Models\User; use Illuminate\Http\Request;
class LibraryWebController extends Controller {
    public function index() { $books=Book::orderBy('title')->paginate(25); $loans=BookLoan::with(['book','user'])->whereNull('returned_at')->get(); return view('perpustakaan.index',compact('books','loans')); }
    public function store(Request $r) {
        $d=$r->validate(['title'=>'required','author'=>'nullable','isbn'=>'nullable|unique:books,isbn','publisher'=>'nullable','year'=>'nullable|integer','stock'=>'nullable|integer|min:0']);
        Book::create($d); return redirect()->route('perpustakaan.index')->with('success','Buku berhasil ditambahkan');
    }
    public function borrow(Request $r) {
        $d=$r->validate(['book_id'=>'required|exists:books,id','user_id'=>'required|exists:users,id','due_date'=>'required|date']);
        BookLoan::create($d); return redirect()->route('perpustakaan.index')->with('success','Peminjaman berhasil');
    }
    public function returnBook(BookLoan $pinjaman) { $pinjaman->update(['returned_at'=>now()]); return redirect()->route('perpustakaan.index')->with('success','Pengembalian berhasil'); }
    public function destroy(Book $buku) { $buku->delete(); return redirect()->route('perpustakaan.index')->with('success','Buku dihapus'); }
}
