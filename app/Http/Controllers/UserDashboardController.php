<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Book; // Import model Book

class UserDashboardController extends Controller
{
    /**
     * Show the application dashboard for regular users.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();

        // Mengambil buku "Populer"
        // Contoh: Ambil 4 buku secara acak yang stoknya > 0
        // Anda bisa mengganti ini dengan logika yang lebih kompleks (misal: berdasarkan rating, view, dll.)
        $popularBooks = Book::where('stock', '>', 0)
                            ->with('category') // Eager load kategori untuk ditampilkan di card
                            ->inRandomOrder()
                            ->take(4)
                            ->get();

        // Mengambil buku "Ongoing" atau "Terbaru"
        // Contoh: Ambil 4 buku terbaru yang stoknya > 0
        $ongoingBooks = Book::where('stock', '>', 0)
                            ->with('category')
                            ->latest() // Buku terbaru dulu
                            ->take(4)
                            ->get();

        return view('user.dashboard', compact(
            'user',
            'popularBooks',
            'ongoingBooks'
        ));
    }
}