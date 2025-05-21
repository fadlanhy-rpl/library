@extends('layouts.app')

@section('title', 'Book List')

@section('content')
    <div class="p-4 md:p-6">
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
            <div class="flex flex-col  sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
                <h2 class="text-xl font-bold text-gray-800">Book Collection</h2>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                    <a href="{{ route('admin.books.create') }}"
                        class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium flex items-center w-full sm:w-auto justify-center sm:justify-start hover:bg-indigo-700 transition">
                        <i class="ri-add-line mr-1"></i> Add New Book
                    </a>
                </div>
            </div>

            <!-- Filter dan Search -->
            <form method="GET" action="{{ route('admin.books.index') }}" class="mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Book</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Title, Author, ISBN..."
                            class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    </div>
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <div class="relative">
                            <select name="category_id" id="category_id"
                                class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 appearance-none">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i
                                class="ri-arrow-down-s-line absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit"
                            class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                            <i class="ri-filter-3-line mr-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.books.index') }}"
                            class="w-full sm:w-auto px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition text-center">
                            Reset
                        </a>
                    </div>
                </div>
            </form>


            @if ($books->count() > 0)
                <div class="table-responsive rounded-lg border border-gray-200">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Cover</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                    Author</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                                    Category</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                                    Stock</th>
                                <th
                                    class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($books as $book)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div
                                            class="h-12 w-9 flex-shrink-0 rounded-sm bg-gray-200 flex items-center justify-center overflow-hidden">
                                            @if ($book->cover_image_path)
                                                <img src="{{ asset('storage/' . $book->cover_image_path) }}"
                                                    alt="{{ $book->title }}" class="h-full w-full object-cover">
                                            @else
                                                <i class="ri-book-mark-line text-gray-400 text-lg"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($book->title, 40) }}
                                        </div>
                                        <div class="text-xs text-gray-500 hidden sm:block">{{ $book->isbn ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 hidden md:table-cell">
                                        {{ $book->author ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 hidden lg:table-cell">
                                        {{ $book->category?->name ?? 'Uncategorized' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 hidden lg:table-cell">
                                        {{ $book->stock ?? 0 }}</td>
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
                                            {{-- Modifikasi Form Delete --}}
                                            <form id="delete-form-{{ $book->id }}"
                                                action="{{ route('admin.books.destroy', $book->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="confirmDelete(event, 'delete-form-{{ $book->id }}')"
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

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $books->links() }} {{-- Ini akan menggunakan default pagination view Laravel --}}
                </div>
            @else
                <div class="text-center text-gray-500 py-10">
                    <i class="ri-book-3-line text-4xl mb-2"></i>
                    <p class="text-lg">No books found.</p>
                    <p class="text-sm">Try adjusting your search or filter criteria, or <a
                            href="{{ route('admin.books.create') }}" class="text-indigo-600 hover:underline">add a new
                            book</a>.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
