<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Hapus import RegisterController jika logika ada di AuthController
// use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\BookController as UserBookController;
use App\Http\Controllers\AuthController; // AuthController Anda
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ProfileController;

// Halaman Awal (Root URL)
Route::get('/', function () {
    if (Auth::guest()) {
        return redirect()->route('login');
    }
    $user = Auth::user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->name('home');

// Rute Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // Rute Registrasi Pengguna Biasa
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register'); // Menampilkan form
    Route::post('register', [AuthController::class, 'register']); // Memproses form
});

Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// Rute untuk Pengguna Biasa (yang sudah login)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/books', [UserBookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [UserBookController::class, 'show'])->name('books.show');

    // Book Borrowing & Returning Routes
    Route::post('/books/{book}/borrow', [UserBookController::class, 'borrow'])->name('books.borrow');
    Route::get('/my-borrowings', [UserBookController::class, 'myBorrowings'])->name('user.borrowings.index');
    Route::patch('/my-borrowings/{borrowing}/return', [UserBookController::class, 'returnBook'])->name('user.borrowings.return'); // <-- RUTE BARU (PATCH)

    // Profile Routes (Bisa diakses semua role yang sudah login)
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update'); // Menggunakan PATCH untuk update data
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update'); // Rute khusus update password
});


// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('books', AdminBookController::class);
    Route::resource('categories', AdminCategoryController::class);
});