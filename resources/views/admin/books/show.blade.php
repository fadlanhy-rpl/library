@extends('layouts.app')

@section('title', 'Book Details - ' . $book->title)

@section('content')
<div class="max-w-5xl mx-auto p-4 ">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Book Header -->
        <div class="bg-indigo-600 p-6 md:p-8 text-white">
            <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                <div class="w-36 h-52 md:w-40 md:h-56 bg-indigo-400 rounded-md shadow-lg flex-shrink-0 mx-auto md:mx-0 overflow-hidden">
                    @if($book->cover_image_path)
                        <img src="{{ asset('storage/' . $book->cover_image_path) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-300 flex items-center justify-center">
                            <i class="ri-book-2-line text-6xl text-gray-500"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-2xl lg:text-3xl font-bold">{{ $book->title }}</h1>
                    @if($book->author)
                    <p class="text-indigo-200 mt-1 text-md">By {{ $book->author }}</p>
                    @endif
                    @if($book->category)
                    <div class="mt-3">
                        <span class="px-3 py-1 bg-indigo-500 rounded-full text-xs font-medium">{{ $book->category->name }}</span>
                    </div>
                    @endif
                    <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-x-6 gap-y-3 text-sm">
                        <div>
                            <p class="text-xs text-indigo-200 uppercase tracking-wider">Publisher</p>
                            <p class="font-medium">{{ $book->publisher }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-200 uppercase tracking-wider">Year</p>
                            <p class="font-medium">{{ $book->year }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-200 uppercase tracking-wider">Pages</p>
                            <p class="font-medium">{{ $book->pages }}</p>
                        </div>
                        @if($book->isbn)
                        <div>
                            <p class="text-xs text-indigo-200 uppercase tracking-wider">ISBN</p>
                            <p class="font-medium">{{ $book->isbn }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Content -->
        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                <div class="md:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Description</h2>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        {!! nl2br(e($book->description)) !!}
                    </div>

                    @if($book->format || $book->shelf_location || $book->acquisition_date)
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 mt-8">Additional Details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if($book->format)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Format</h3>
                            <p class="mt-1 text-gray-800">{{ $book->format }}</p>
                        </div>
                        @endif
                        @if($book->shelf_location)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Shelf Location</h3>
                            <p class="mt-1 text-gray-800">{{ $book->shelf_location }}</p>
                        </div>
                        @endif
                        @if($book->acquisition_date)
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Acquisition Date</h3>
                            <p class="mt-1 text-gray-800">{{ \Carbon\Carbon::parse($book->acquisition_date)->format('M d, Y') }}</p>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="md:col-span-1 space-y-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Availability</h3>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm">Total Stock:</span>
                            <span class="font-medium text-gray-800">{{ $book->stock ?? 0 }}</span>
                        </div>
                        {{-- Fitur Available & Borrowed memerlukan sistem peminjaman --}}
                        {{-- <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Available:</span>
                            <span class="font-medium text-green-600">8</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Borrowed:</span>
                            <span class="font-medium text-amber-600">4</span>
                        </div> --}}
                    </div>

                    {{-- Fitur Borrowing History memerlukan sistem peminjaman --}}
                    {{-- <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Borrowing History</h3>
                        <p class="text-sm text-gray-500">No borrowing history available yet.</p>
                    </div> --}}

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Actions</h3>
                        <div class="space-y-2">
                            <a href="{{ route('admin.books.edit', $book->id) }}" class="flex items-center justify-center w-full px-4 py-2.5 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600 transition">
                                <i class="ri-pencil-line mr-2"></i> Edit Book
                            </a>
                            <form action="{{ route('admin.books.destroy', $book->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this book: {{ addslashes($book->title) }}?');" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center justify-center w-full px-4 py-2.5 bg-red-500 text-white rounded-lg text-sm font-medium hover:bg-red-600 transition">
                                    <i class="ri-delete-bin-line mr-2"></i> Delete Book
                                </button>
                            </form>
                            {{-- <button class="flex items-center justify-center w-full px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium">
                                <i class="ri-printer-line mr-1"></i> Print Details
                            </button> --}}
                        </div>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="{{ route('admin.books.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                            <i class="ri-arrow-left-s-line mr-1"></i>Back to Book List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection