@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="mb-6 p-4"> <!-- Hapus p-4 atau p-6 dari sini karena sudah ada di main layout -->
        <!-- Hero Banner (Mirip desain tapi lebih sederhana) -->
        <div class="bg-indigo-600 rounded-2xl p-6 md:p-8 mb-6 md:mb-8 relative overflow-hidden text-white">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="md:w-2/3">
                    @auth
                        <h1 class="text-2xl md:text-3xl font-bold mb-2">Hi, {{ Auth::user()->name }}</h1>
                    @endauth
                    <p class="text-indigo-100 text-sm mb-4">The library contains a collection of books for knowledge seekers
                        and avid readers alike. Manage your collection with ease.</p>
                    <a href="{{ route('admin.books.index') }}"
                        class="bg-white text-indigo-600 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                        Manage Books
                    </a>
                </div>
                <div class="md:w-1/3 relative h-32 md:h-40 mt-6 md:mt-0 flex justify-center items-center">
                    <!-- Ilustrasi bisa diganti dengan SVG atau gambar yang lebih relevan -->
                    <i class="ri-book-open-fill text-8xl text-indigo-400 opacity-50"></i>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute top-4 right-8 w-6 h-6 rounded-full bg-yellow-300 opacity-30 animate-pulse"></div>
            <div class="absolute bottom-8 left-1/3 w-4 h-4 rounded-full bg-pink-300 opacity-30 animate-ping"></div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6">
            <!-- Total Books -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Books</p>
                        <h3 class="text-xl md:text-2xl font-bold mt-1">{{ $totalBooks ?? 'N/A' }}</h3>
                    </div>
                    <div class="h-10 w-10 md:h-12 md:w-12 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="ri-book-3-line text-indigo-600 text-lg md:text-xl"></i>
                    </div>
                </div>
                {{-- <div class="mt-3 md:mt-4 flex items-center text-xs text-green-600">
                <i class="ri-arrow-up-line mr-1"></i>
                <span>12% increase this month</span>
            </div> --}}
            </div>

            <!-- Active Loans -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Active Loans</p>
                        <h3 class="text-xl md:text-2xl font-bold mt-1">{{ $activeLoans ?? 0 }}</h3> {{-- Akan menampilkan nilai dari controller --}}
                    </div>
                    <div class="h-10 w-10 md:h-12 md:w-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="ri-bookmark-3-line text-green-600 text-lg md:text-xl"></i>
                    </div>
                </div>
                {{-- ... (komentar % increase/decrease) ... --}}
            </div>

            <!-- Overdue Returns -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Overdue Returns</p>
                        <h3 class="text-xl md:text-2xl font-bold mt-1">{{ $overdueReturns ?? 0 }}</h3> {{-- Akan menampilkan nilai dari controller --}}
                    </div>
                    <div class="h-10 w-10 md:h-12 md:w-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="ri-error-warning-line text-red-600 text-lg md:text-xl"></i>
                    </div>
                </div>
                {{-- ... (komentar % increase/decrease) ... --}}
            </div>

            <!-- Registered Users -->
            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Registered Users</p>
                        <h3 class="text-xl md:text-2xl font-bold mt-1">{{ $totalUsers ?? 'N/A' }}</h3>
                    </div>
                    <div class="h-10 w-10 md:h-12 md:w-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="ri-user-3-line text-blue-600 text-lg md:text-xl"></i>
                    </div>
                </div>
                {{-- <div class="mt-3 md:mt-4 flex items-center text-xs text-green-600">
                <i class="ri-arrow-up-line mr-1"></i>
                <span>5% increase this month</span>
            </div> --}}
            </div>
        </div>

        <!-- Recent Book Table Section -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                <h3 class="text-lg font-semibold text-gray-800">Recent Book Collection</h3>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('admin.books.create') }}"
                        class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium flex items-center w-full sm:w-auto justify-center sm:justify-start hover:bg-indigo-700 transition">
                        <i class="ri-add-line mr-1"></i> Add Book
                    </a>
                </div>
            </div>

            @if (isset($recentBooks) && $recentBooks->count() > 0)
                <div class="table-responsive rounded-lg border border-gray-200">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                {{-- Kolom untuk Cover --}}
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                    Cover</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Book Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Publisher</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                    Year</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($recentBooks as $book)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        {{-- Area untuk menampilkan cover buku --}}
                                        <div
                                            class="h-16 w-12 flex-shrink-0 rounded-md bg-gray-100 flex items-center justify-center overflow-hidden shadow-sm">
                                            @if ($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path))
                                                <img src="{{ asset('storage/' . $book->cover_image_path) }}"
                                                    alt="{{ Str::limit($book->title, 20) }}"
                                                    class="h-full w-full object-cover">
                                            @else
                                                {{-- Placeholder jika tidak ada gambar atau gambar tidak ditemukan --}}
                                                <i class="ri-image-line text-gray-400 text-2xl" title="No cover image"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            {{-- Hapus div gambar dari sini karena sudah ada di kolom terpisah --}}
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 hover:text-indigo-600">
                                                    <a
                                                        href="{{ route('admin.books.show', $book->id) }}">{{ Str::limit($book->title, 35) }}</a>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $book->category?->name ?? 'Uncategorized' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ Str::limit($book->publisher, 25) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 hidden md:table-cell">
                                        {{ $book->year }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <a href="{{ route('admin.books.show', $book->id) }}"
                                                class="p-1.5 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 transition"
                                                title="View">
                                                <i class="ri-eye-line text-base"></i>
                                            </a>
                                            <a href="{{ route('admin.books.edit', $book->id) }}"
                                                class="p-1.5 bg-amber-100 text-amber-600 rounded-md hover:bg-amber-200 transition"
                                                title="Edit">
                                                <i class="ri-pencil-line text-base"></i>
                                            </a>
                                            <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this book: {{ addslashes($book->title) }}?');"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition"
                                                    title="Delete">
                                                    <i class="ri-delete-bin-line text-base"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500 py-8">No recent books found.</p>
            @endif
            <div class="mt-4 text-right">
                <a href="{{ route('admin.books.index') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    View All Books <i class="ri-arrow-right-s-line"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
