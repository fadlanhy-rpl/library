<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Borrowing;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder; // Import Builder

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query() // Mulai dengan Book::query() untuk fleksibilitas
                     ->with('category') // Eager load category untuk ditampilkan
                     ->where('stock', '>', 0) // Hanya buku yang stoknya ada
                     ->latest(); // Urutkan berdasarkan terbaru

        // Logika Pencarian Utama
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function (Builder $q) use ($searchTerm) { // Gunakan Type Hint Builder
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('author', 'like', '%' . $searchTerm . '%') // Cari berdasarkan penulis
                  ->orWhere('isbn', 'like', '%' . $searchTerm . '%')
                  // Cari berdasarkan nama kategori melalui relasi
                  ->orWhereHas('category', function (Builder $categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Filter berdasarkan kategori (jika ada dropdown filter kategori terpisah)
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(); // Untuk dropdown filter kategori

        return view('books.index', compact('books', 'categories'));
    }

    public function show(Book $book)
    {
        $book->load('category');
        return view('books.show', compact('book'));
    }

     public function borrow(Request $request, Book $book)
    {
        $user = Auth::user();

        // Validasi input tanggal pengembalian
        $request->validate([
            'due_date' => [
                'required',
                'date',
                'after_or_equal:' . now()->addDay()->toDateString(), // Minimal besok
                'before_or_equal:' . now()->addMonth()->addDay()->toDateString(), // Maksimal 1 bulan (+1 hari untuk toleransi)
            ],
        ]);

        if ($book->stock <= 0) {
            return redirect()->back()->with('error', 'Sorry, this book is currently out of stock.');
        }
        if ($user->hasBorrowed($book)) {
            return redirect()->back()->with('error', 'You have already borrowed this book and not returned it yet.');
        }
        $maxBorrows = 3;
        if ($user->borrowings()->where('status', 'borrowed')->count() >= $maxBorrows) {
            return redirect()->back()->with('error', "You have reached the maximum limit of {$maxBorrows} borrowed books at a time.");
        }

        try {
            $dueDate = Carbon::parse($request->input('due_date')); // Ambil dari input

            Borrowing::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'borrowed_at' => Carbon::now(),
                'due_date' => $dueDate, // Gunakan tanggal dari input
                'status' => 'borrowed',
            ]);

            $book->decrement('stock');

            return redirect()->route('user.borrowings.index')
                             ->with('success', 'Book "' . $book->title . '" borrowed successfully! Please return by: ' . $dueDate->format('M d, Y') . '.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Display a listing of the user's borrowed books.
     *
     * @return \Illuminate\View\View
     */
    public function myBorrowings()
    {
        $user = Auth::user();
        $borrowings = $user->borrowings()
                            ->with('book.category') // Eager load buku dan kategori buku
                            ->orderBy('borrowed_at', 'desc')
                            ->paginate(10);

        return view('user.borrowings.index', compact('borrowings'));
    }

    /**
     * Process a book return request by the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Borrowing  $borrowing Record peminjaman yang akan dikembalikan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function returnBook(Request $request, Borrowing $borrowing)
    {
        // Pastikan pengguna yang login adalah pemilik peminjaman ini
        if ($borrowing->user_id !== Auth::id()) {
            return redirect()->route('user.borrowings.index')->with('error', 'Unauthorized action.');
        }

        // Pastikan buku belum dikembalikan
        if ($borrowing->status === 'returned') {
            return redirect()->route('user.borrowings.index')->with('info', 'This book has already been returned.');
        }

        try {
            $fineAmount = 0;
            $finePerDay = 20000; // Denda Rp 20.000 per hari keterlambatan

            // Cek apakah terlambat
            $returnDate = Carbon::now();
            if ($returnDate->greaterThan($borrowing->due_date)) {
                // Hitung selisih hari keterlambatan (pembulatan ke atas)
                $daysOverdue = $returnDate->diffInDays($borrowing->due_date->startOfDay(), false) < 0
                               ? $borrowing->due_date->startOfDay()->diffInDays($returnDate->startOfDay())
                               : 0;

                if ($daysOverdue > 0) {
                    $fineAmount = $daysOverdue * $finePerDay;
                    $borrowing->status = 'overdue'; // Set status ke overdue jika ada denda
                }
            }

            $borrowing->returned_at = $returnDate;
            $borrowing->fine_amount = $fineAmount;
            if ($borrowing->status !== 'overdue') { // Jika tidak overdue setelah perhitungan denda
                $borrowing->status = 'returned';
            }
            $borrowing->save();

            // Tambah stok buku kembali
            $borrowing->book->increment('stock');

            $message = 'Book "' . $borrowing->book->title . '" returned successfully.';
            if ($fineAmount > 0) {
                $message .= ' A fine of Rp ' . number_format($fineAmount, 0, ',', '.') . ' has been applied due to late return.';
            }

            return redirect()->route('user.borrowings.index')->with('success', $message);

        } catch (\Exception $e) {
            // Log::error('Error returning book: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while returning the book. Please try again.');
        }
    }
}