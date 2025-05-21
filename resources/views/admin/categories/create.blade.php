@extends('layouts.app')

@section('title', 'Add New Category')

@section('content')
<div class="p-4 md:p-6">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm p-6 md:p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Create New Category</h2>
            <a href="{{ route('admin.categories.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                <i class="ri-arrow-left-line mr-1"></i>Back to Category List
            </a>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <!-- Category Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                        placeholder="Enter category name (e.g., Fiction, Science)">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Slug (Opsional, bisa di-generate otomatis di backend) -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug (Optional)</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400"
                        placeholder="e.g., fiction, science (auto-generated if blank)">
                    <p class="text-xs text-gray-500 mt-1">If left blank, it will be auto-generated from the name. Use only lowercase letters, numbers, and hyphens.</p>
                    @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    Create Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection