<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user's profile page.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user')); // Kita akan buat view ini
    }

    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user')); // Kita akan buat view ini
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_image' => ['nullable', 'string'], // Sekarang menerima base64 string
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
        ]);

        $profileData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
        ];

        // Menangani gambar profil yang sudah di-crop (base64)
        if ($request->filled('profile_image')) {
            // Hapus gambar profil lama jika ada
            if ($user->profile_image_path) {
                Storage::disk('public')->delete($user->profile_image_path);
            }

            $imageData = $request->input('profile_image'); // Ini adalah string base64
            // Pisahkan tipe mime dan data base64
            // Contoh: data:image/png;base64,iVBORw0KGgoAAAANSUhEUg...
            list($type, $imageData) = explode(';', $imageData);
            list(, $imageData)      = explode(',', $imageData);
            $imageData = base64_decode($imageData);

            // Tentukan ekstensi file dari tipe mime
            $extension = '';
            if (strpos($type, 'image/jpeg') !== false) {
                $extension = 'jpg';
            } elseif (strpos($type, 'image/png') !== false) {
                $extension = 'png';
            } elseif (strpos($type, 'image/gif') !== false) {
                $extension = 'gif';
            } elseif (strpos($type, 'image/webp') !== false) {
                $extension = 'webp';
            } else {
                // Tipe tidak didukung atau default
                return back()->withErrors(['profile_image' => 'Unsupported image type.'])->withInput();
            }

            $fileName = 'profile_images/' . Str::random(40) . '.' . $extension;
            Storage::disk('public')->put($fileName, $imageData);
            $profileData['profile_image_path'] = $fileName;
        }

        $user->update($profileData);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('The provided password does not match your current password.');
                }
            }],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password updated successfully.');
    }
}