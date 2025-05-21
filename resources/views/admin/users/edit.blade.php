@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="p-4 md:p-6">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm p-6 md:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Edit User Information</h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                <i class="ri-arrow-left-line mr-1"></i>Back to User List
            </a>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Method untuk update --}}

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Password (Opsional saat edit) -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password (Optional)</label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                        placeholder="Leave blank to keep current password">
                    <p class="text-xs text-gray-500 mt-1">Min. 8 characters if changing.</p>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm Password (Opsional saat edit, hanya jika password baru diisi) -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                        placeholder="Confirm new password">
                </div>

                <!-- Role -->
                <div class="relative">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select id="role" name="role" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 appearance-none"
                        {{ Auth::id() === $user->id ? 'disabled' : '' }} {{-- Admin tidak bisa mengubah role diri sendiri --}} >
                        <option value="">Select a role</option>
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <i class="ri-arrow-down-s-line absolute right-3 top-1/2 mt-2.5 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    @if(Auth::id() === $user->id)
                        <p class="text-xs text-gray-500 mt-1">You cannot change your own role.</p>
                        <input type="hidden" name="role" value="{{ $user->role }}"> {{-- Kirim role saat ini jika disabled --}}
                    @endif
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection