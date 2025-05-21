@extends('layouts.app')

@section('title', 'Category Management')

@section('content')
<div class="p-4 md:p-6">
    <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 md:mb-6 gap-3">
            <h2 class="text-xl font-bold text-gray-800">Category List</h2>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.categories.create') }}"
                    class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium flex items-center w-full sm:w-auto justify-center sm:justify-start hover:bg-indigo-700 transition">
                    <i class="ri-add-line mr-1"></i> Add New Category
                </a>
            </div>
        </div>

        <!-- Filter dan Search (Sederhana untuk Kategori) -->
        <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Category</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Category name..."
                           class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                </div>
                <div class="sm:col-span-1 md:col-span-1">
                    {{-- Kolom kosong untuk alignment atau bisa diisi filter lain jika perlu --}}
                </div>
                <div class="flex space-x-2">
                    <button type="submit"
                            class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                        <i class="ri-filter-3-line mr-1"></i> Filter
                    </button>
                     <a href="{{ route('admin.categories.index') }}"
                            class="w-full sm:w-auto px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition text-center">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        @if(isset($categories) && $categories->count() > 0)
        <div class="table-responsive rounded-lg border border-gray-200">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Total Books</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                            {{ $category->slug }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                            {{ $category->books_count ?? 0 }} {{-- Asumsi ada books_count dari withCount('books') --}}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex justify-center items-center space-x-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-1.5 bg-amber-100 text-amber-600 rounded-md hover:bg-amber-200 transition" title="Edit">
                                    <i class="ri-pencil-line text-base"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category: {{ addslashes($category->name) }}? This might affect associated books.');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition" title="Delete">
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
            {{ $categories->links() }}
        </div>

        @else
            <div class="text-center text-gray-500 py-10">
                <i class="ri-price-tag-3-line text-4xl mb-2"></i>
                <p class="text-lg">No categories found.</p>
                <p class="text-sm">Try adjusting your search or filter criteria, or <a href="{{ route('admin.categories.create') }}" class="text-indigo-600 hover:underline">add a new category</a>.</p>
            </div>
        @endif
    </div>
</div>
@endsection