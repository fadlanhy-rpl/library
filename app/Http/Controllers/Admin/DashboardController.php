<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Borrowing;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View | \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $totalBooks = Book::count();
        $totalUsers = User::where('role', 'user')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        $recentBooks = Book::with('category')->latest()->take(5)->get();

        // Menghitung Peminjaman Aktif
        // Peminjaman aktif adalah yang statusnya 'borrowed' DAN belum melewati due_date
        // ATAU statusnya 'overdue' (karena overdue juga masih dianggap aktif belum dikembalikan)
        $activeLoans = Borrowing::whereIn('status', ['borrowed', 'overdue'])
                                ->count();

        // Menghitung Pengembalian yang Terlambat (Overdue Returns)
        // Ini adalah peminjaman yang statusnya 'overdue'
        // atau yang statusnya 'borrowed' TAPI due_date sudah lewat
        $overdueReturns = Borrowing::where('status', 'overdue')
                                   ->orWhere(function ($query) {
                                       $query->where('status', 'borrowed')
                                             ->where('due_date', '<', Carbon::now());
                                   })
                                   ->count();

        // Untuk API (jika Anda membutuhkannya):
        // return response()->json([
        //     'message' => 'Welcome to Admin Dashboard',
        //     'stats' => [
        //         'totalBooks' => $totalBooks,
        //         'totalUsers' => $totalUsers,
        //         'totalAdmins' => $totalAdmins,
        //         'activeLoans' => $activeLoans,
        //         'overdueReturns' => $overdueReturns,
        //     ],
        //     'recentBooks' => $recentBooks
        // ]);

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalUsers',
            'totalAdmins',
            'recentBooks',
            'activeLoans',    // Kirim data activeLoans
            'overdueReturns'  // Kirim data overdueReturns
        ));
    }
}