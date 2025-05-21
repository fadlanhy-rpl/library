@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="p-4 md:p-6">
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
            <h2 class="text-xl font-bold text-gray-800">User List</h2>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.users.create') }}"
                    class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium flex items-center w-full sm:w-auto justify-center sm:justify-start hover:bg-indigo-700 transition">
                    <i class="ri-user-add-line mr-1"></i> Add New User
                </a>
            </div>
        </div>

        <!-- Filter dan Search (Sudah ada sebelumnya) -->
        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-6">
            {{-- ... (kode form filter Anda) ... --}}
             <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search User</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, Email..."
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <div class="relative">
                        <select name="role" id="role"
                                class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 appearance-none">
                            <option value="">All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                        </select>
                        <i class="ri-arrow-down-s-line absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button type="submit"
                            class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                        <i class="ri-filter-3-line mr-1"></i> Filter
                    </button>
                     <a href="{{ route('admin.users.index') }}"
                            class="w-full sm:w-auto px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition text-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        @if(isset($users) && $users->count() > 0)
        <div class="table-responsive rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Joined Date</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 mr-3 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden shadow-sm">
                                    {{-- MODIFIKASI UNTUK MENAMPILKAN GAMBAR PROFIL --}}
                                    @if($user->profile_image_path && Storage::disk('public')->exists($user->profile_image_path))
                                        <img src="{{ asset('storage/' . $user->profile_image_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                    @else
                                        {{-- Fallback ke UI Avatars jika tidak ada gambar profil atau path tidak valid --}}
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff&size=40" alt="{{ $user->name }}" class="h-full w-full object-cover">
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">{{ $user->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex justify-center items-center space-x-2">
                                {{-- <a href="{{ route('admin.users.show', $user->id) }}" class="p-1.5 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition" title="View">
                                    <i class="ri-eye-line text-base"></i>
                                </a> --}}
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="p-1.5 bg-amber-100 text-amber-600 rounded-md hover:bg-amber-200 transition" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                @if(Auth::id() !== $user->id)
                                <form id="delete-user-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="confirmDelete(event, 'delete-user-form-{{ $user->id }}')"
                                            class="p-1.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition"
                                            title="Delete">
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </form>
                                @else
                                <button class="p-1.5 bg-gray-100 text-gray-400 rounded-md cursor-not-allowed" title="Cannot delete self" disabled>
                                    <i class="ri-delete-bin-line text-base"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>

        @else
            <div class="text-center text-gray-500 py-10">
                <i class="ri-user-search-line text-4xl mb-2"></i>
                <p class="text-lg">No users found.</p>
                <p class="text-sm">Try adjusting your search or filter criteria, or <a href="{{ route('admin.users.create') }}" class="text-indigo-600 hover:underline">add a new user</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection