<?php

namespace App\Http\Controllers; // Pastikan namespace ini benar, bukan App\Http\Controllers\Auth

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Diperlukan jika ada logic registrasi di sini
use App\Models\User; // Diperlukan jika ada logic registrasi di sini
use Illuminate\Validation\ValidationException; // Untuk menangani error login
use Illuminate\Auth\Events\Registered; // <-- Import event Registered
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login'); // Pastikan view auth.login sudah menggunakan Tailwind CSS
    }

     public function showRegistrationForm()
    {
        return view('auth.register'); // Kita akan buat view ini
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on user role
            if ($user->isAdmin()) { // Asumsi ada method isAdmin() di model User
                return redirect()->intended(route('admin.dashboard'));
            }

            // Untuk semua user lain (non-admin)
            return redirect()->intended(route('user.dashboard')); // Arahkan ke dashboard pengguna biasa
        }

        // If authentication fails
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')], // Pesan error standar Laravel
        ]);
    }

     public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // Menggunakan Password::defaults()
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Otomatis set role sebagai 'user'
        ]);

        event(new Registered($user)); // Memicu event Registered (berguna untuk verifikasi email, dll.)

        Auth::login($user); // Langsung login pengguna setelah registrasi

        // Arahkan ke dashboard pengguna biasa
        return redirect()->route('user.dashboard')->with('success', 'Registration successful! Welcome aboard.');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login'); // Arahkan ke halaman login setelah logout
    }

    // --- Opsional: Metode Registrasi Admin (Jika diperlukan dari halaman khusus) ---
    // Biasanya registrasi admin dilakukan via Seeder atau oleh admin lain via UserController.
    // Jika Anda benar-benar memerlukan halaman registrasi admin publik, Anda bisa uncomment ini
    // dan pastikan rute serta view 'auth.register-admin' sudah ada.

    /*
    public function showAdminRegistrationForm()
    {
        // Pastikan rute untuk ini dilindungi atau hanya diketahui oleh pihak tertentu
        return view('auth.register-admin');
    }

    public function registerAdmin(Request $request)
    {
        // Pastikan rute untuk ini dilindungi atau hanya diketahui oleh pihak tertentu
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Set role sebagai admin
        ]);

        // Auth::login($user); // Opsional: Langsung login admin baru

        return redirect()->route('login')->with('success', 'Admin account registered successfully. You can now log in.');
        // Atau redirect ke admin.dashboard jika langsung login
        // return redirect()->route('admin.dashboard')->with('success', 'Admin registration successful! You are now logged in.');
    }
    */
}