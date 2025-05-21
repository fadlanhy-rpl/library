@extends('layouts.app')

@section('title', 'Browse Books')

@section('content')
<div class="container mx-auto px-6 py-2 md:py-4"> {{-- Hapus p-4 md:p-6 dari div ini jika sudah ada di layout app --}}

    <div class="mb-8 text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Explore Our Book Collection</h1>
        <p class="text-gray-600 mt-2">Find your next favorite read from our diverse catalog.</p>
    </div>

    <!-- Filter dan Search -->
    <form method="GET" action="{{ route('books.index') }}" class="mb-8 p-4 bg-white rounded-xl shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Book</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Title, Author, ISBN..."
                       class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
            </div>
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <div class="relative">
                    <select name="category_id" id="category_id"
                            class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 appearance-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $category) {{-- Asumsi $categories dikirim dari BookController@index --}}
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <i class="ri-arrow-down-s-line absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="submit"
                        class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    <i class="ri-filter-3-line mr-1"></i> Filter
                </button>
                 <a href="{{ route('books.index') }}"
                        class="w-full sm:w-auto px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition text-center">
                    Reset
                </a>
            </div>
        </div>
    </form>

    @if(isset($books) && $books->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($books as $book)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1">
                <a href="{{ route('books.show', $book->id) }}" class="block">
                    @if($book->cover_image_path)
                        <img src="{{ asset('storage/' . $book->cover_image_path) }}" alt="{{ $book->title }}" class="w-full h-64 object-cover">
                    @else
                        <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                            <i class="ri-book-open-line text-5xl text-gray-400"></i>
                        </div>
                    @endif
                </a>
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">
                        <a href="{{ route('books.show', $book->id) }}" class="hover:text-indigo-600 transition-colors">{{ Str::limit($book->title, 45) }}</a>
                    </h3>
                    @if($book->author)
                    <p class="text-xs text-gray-500 mb-2">By {{ Str::limit($book->author, 30) }}</p>
                    @endif
                    @if($book->category)
                    <span class="inline-block bg-indigo-100 text-indigo-700 text-xs font-medium px-2.5 py-0.5 rounded-full mb-3 self-start">{{ $book->category->name }}</span>
                    @endif
                    <p class="text-sm text-gray-600 mb-4 flex-grow">
                        {{ Str::limit($book->description, 80) }}
                    </p>
                    <div class="mt-auto">
                        <a href="{{ route('books.show', $book->id) }}" class="inline-flex items-center justify-center w-full px-4 py-2 bg-indigo-500 text-white rounded-lg text-sm font-medium hover:bg-indigo-600 transition-colors">
                            View Details <i class="ri-arrow-right-s-line ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-10">
            {{ $books->links() }}
        </div>
    @else
        <div class="text-center text-gray-500 py-16">
            <i class="ri-search-eye-line text-6xl mb-3 text-gray-400"></i>
            <p class="text-xl font-semibold text-gray-700">No books found.</p>
            <p class="text-gray-500 mt-1">Try adjusting your search or filter criteria.</p>
        </div>
    @endif
</div>
@endsection