<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Pastikan ini ada jika Anda menggunakan auth()->id() atau Auth::id()
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Category;
// use App\Models\Book; // Tidak digunakan di UserController sejauh ini

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        // $categories = Category::orderBy('name')->get(); // Kemungkinan tidak diperlukan di halaman user index
        // Jika Anda memutuskan tidak perlu categories di index user, hapus $categories dari sini dan dari compact()
        // return view('admin.users.index', compact('users', 'categories'));
        return view('admin.users.index', compact('users')); // Lebih umum untuk halaman user
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create() // <--- TAMBAHKAN METHOD INI
    {
        // Jika form create user memerlukan data tambahan (misalnya daftar role dinamis),
        // Anda bisa mengambilnya di sini dan mengirimkannya ke view.
        // Contoh: $roles = ['admin', 'user'];
        // return view('admin.users.create', compact('roles'));

        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        // For API:
        // return response()->json(['message' => 'User created successfully by admin.', 'user' => $user], 201);
        // For web:
        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        // For API:
        // return response()->json($user);
        // For web (jika ada halaman detail user):
        // return view('admin.users.show', compact('user'));
        // Jika tidak ada halaman show, Anda bisa redirect atau hapus method ini jika tidak dipakai oleh Route::resource
        return redirect()->route('admin.users.edit', $user->id); // Contoh: redirect ke edit
    }

    /**
     * Show the form for editing the specified user.
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function edit(User $user)
    {
        // For API:
        // return response()->json(['message' => 'Admin user edit form placeholder', 'user' => $user]);
        // For web:
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'user'])],
            'password' => ['nullable', 'confirmed', Password::defaults()], // Password opsional saat update
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        // For API:
        // return response()->json(['message' => 'User updated successfully.', 'user' => $user]);
        // For web:
        return redirect()->route('admin.users.index')->with('success', 'User "'.$user->name.'" updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if (Auth::check() && $user->id === Auth::id()) { // Menggunakan Auth facade
            // For API:
            // return response()->json(['error' => 'You cannot delete yourself.'], 403);
            // For web:
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }
        $user->delete();
        // For API:
        // return response()->json(['message' => 'User deleted successfully.'], 200);
        // For web:
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}