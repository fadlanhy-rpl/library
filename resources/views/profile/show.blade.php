@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="p-4 md:p-6">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="md:flex">
            <div class="md:w-1/3 p-6 bg-gray-50 flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-200">
                <div class="relative mb-4">
                    <img class="h-32 w-32 rounded-full object-cover shadow-md"
                         src="{{ $user->profile_image_path ? asset('storage/' . $user->profile_image_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random&color=fff&size=128' }}"
                         alt="{{ $user->name }}">
                    {{-- Badge Role --}}
                    <span class="absolute bottom-0 right-0 block h-7 w-7 rounded-full ring-2 ring-white
                        {{ $user->isAdmin() ? 'bg-red-500' : 'bg-indigo-500' }}
                        flex items-center justify-center text-white text-xs font-bold"
                        title="{{ ucfirst($user->role) }}">
                        {{ $user->isAdmin() ? 'A' : 'U' }}
                    </span>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <p class="text-xs text-gray-400 mt-1">Joined: {{ $user->created_at->format('M d, Y') }}</p>
            </div>
            <div class="md:w-2/3 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700">Profile Information</h3>
                    <a href="{{ route('profile.edit') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                        <i class="ri-pencil-line mr-1"></i>Edit Profile
                    </a>
                </div>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Role</dt>
                        <dd class="mt-1 text-gray-900">{{ ucfirst($user->role) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->date_of_birth ? $user->date_of_birth->format('F d, Y') : 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Age</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->age ? $user->age . ' years old' : 'Not set' }}</dd>
                    </div>
                    {{-- Tambahkan field lain jika ada (phone_number, bio) --}}
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection