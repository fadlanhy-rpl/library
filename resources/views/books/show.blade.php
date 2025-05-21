@extends('layouts.app')

@section('title', $book->title)

@section('content')
    <div class="container mx-auto px-4 py-2 md:py-4">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Book Header Section -->
            <div class="md:flex">
                <div class="md:flex-shrink-0">
                    @if ($book->cover_image_path)
                        <img class="h-64 w-full object-cover md:h-full md:w-64"
                            src="{{ asset('storage/' . $book->cover_image_path) }}" alt="{{ $book->title }}">
                    @else
                        <div class="h-64 w-full bg-gray-200 flex items-center justify-center md:h-full md:w-64">
                            <i class="ri-book-open-line text-6xl text-gray-400"></i>
                        </div>
                    @endif
                </div>
                <div class="p-6 md:p-8 flex-1">
                    <div class="uppercase tracking-wide text-xs text-indigo-600 font-semibold mb-1">
                        {{ $book->category?->name ?? 'Uncategorized' }}
                    </div>
                    <h1 class="block mt-1 text-2xl lg:text-3xl leading-tight font-bold text-gray-900">{{ $book->title }}
                    </h1>
                    @if ($book->author)
                        <p class="mt-1 text-gray-600">By <span class="font-medium">{{ $book->author }}</span></p>
                    @endif

                    <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-700">
                        <div>
                            <span class="font-medium text-gray-500">Publisher:</span> {{ $book->publisher }}
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Year:</span> {{ $book->year }}
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Pages:</span> {{ $book->pages }}
                        </div>
                        @if ($book->isbn)
                            <div>
                                <span class="font-medium text-gray-500">ISBN:</span> {{ $book->isbn }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        @if ($book->stock > 0)
                            @if (Auth::user()->hasBorrowed($book))
                                <p
                                    class="px-6 py-2.5 bg-yellow-100 text-yellow-700 rounded-lg text-sm font-medium text-center">
                                    <i class="ri-information-line mr-2"></i>You have already borrowed this book.
                                </p>
                            @else
                                <form action="{{ route('books.borrow', $book->id) }}" method="POST"
                                    id="borrowForm-{{ $book->id }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="due_date_input-{{ $book->id }}"
                                            class="block text-sm font-medium text-gray-700 mb-1">Select Return Date (Max. 1
                                            Month)</label>
                                        <input type="date" id="due_date_input-{{ $book->id }}" name="due_date"
                                            required min="{{ now()->addDay()->toDateString() }}" {{-- Minimal besok --}}
                                            max="{{ now()->addMonth()->toDateString() }}" {{-- Maksimal 1 bulan dari sekarang --}}
                                            class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                                        @error('due_date')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit"
                                        class="w-full px-6 py-2.5 bg-green-500 text-white rounded-lg text-sm font-medium hover:bg-green-600 transition flex items-center justify-center">
                                        <i class="ri-book-mark-line mr-2"></i> Borrow This Book
                                    </button>
                                </form>
                            @endif
                        @else
                            <p class="px-6 py-2.5 bg-red-100 text-red-700 rounded-lg text-sm font-medium text-center">
                                <i class="ri-close-circle-line mr-2"></i>Out of Stock
                            </p>
                        @endif
                        <p class="text-sm text-gray-600 mt-2 text-center">Available stock: <span
                                class="font-bold">{{ $book->stock }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Book Description and Details Section -->
            <div class="px-6 py-4 md:px-8 md:py-6 border-t border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-3">Book Description</h2>
                <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($book->description)) !!}
                </div>

                @if ($book->format || $book->shelf_location || $book->acquisition_date)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">Additional Information</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            @if ($book->format)
                                <div class="sm:col-span-1">
                                    <dt class="font-medium text-gray-500">Format</dt>
                                    <dd class="mt-1 text-gray-900">{{ $book->format }}</dd>
                                </div>
                            @endif
                            @if ($book->shelf_location)
                                <div class="sm:col-span-1">
                                    <dt class="font-medium text-gray-500">Shelf Location</dt>
                                    <dd class="mt-1 text-gray-900">{{ $book->shelf_location }}</dd>
                                </div>
                            @endif
                            @if ($book->acquisition_date)
                                <div class="sm:col-span-1">
                                    <dt class="font-medium text-gray-500">Acquisition Date</dt>
                                    <dd class="mt-1 text-gray-900">
                                        {{ \Carbon\Carbon::parse($book->acquisition_date)->format('F d, Y') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                @endif
            </div>

            <div class="px-6 py-4 md:px-8 md:py-6 border-t border-gray-200 text-center">
                <a href="{{ route('books.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="ri-arrow-left-s-line mr-1"></i> Back to Book List
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    {{-- Plugin Tailwind Typography jika ingin styling otomatis untuk deskripsi --}}
    {{-- Jika sudah di-load global di app.blade.php, tidak perlu di sini --}}
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/typography@0.5.x/dist/typography.min.css"> --}}
@endpush
