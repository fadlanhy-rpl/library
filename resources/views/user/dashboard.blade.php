@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="p-4 md:p-6">
    {{-- Hero Banner --}}
    <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 mb-8">
        {{-- ... (kode hero banner tetap sama) ... --}}
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-2/3">
                @auth
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Welcome, {{ Auth::user()->name }}!</h1>
                @endauth
                <p class="text-gray-600 text-sm mb-4">
                    Welcome to LandLibrary. Explore our collection of books and enjoy your reading journey.
                </p>
                <a href="{{ route('books.index') }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700 transition inline-flex items-center">
                    <i class="ri-book-open-line mr-2"></i>Browse All Books
                </a>
            </div>
            <div class="md:w-1/3 relative h-32 md:h-40 mt-6 md:mt-0 flex justify-center items-center">
                <i class="ri-quill-pen-fill text-8xl text-indigo-300 opacity-80"></i>
            </div>
        </div>
    </div>

    {{-- ... (kode card discover tetap sama) ... --}}
     <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 card-hover-original"> {{-- Ganti nama class agar tidak konflik --}}
            <div class="flex items-center text-indigo-600 mb-3">
                <i class="ri-search-eye-line text-3xl mr-3"></i>
                <h3 class="text-lg font-semibold">Discover More Titles</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                Find your next favorite book from our ever-growing collection.
            </p>
            <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                Start Exploring <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 card-hover">
            <div class="flex items-center text-green-600 mb-3">
                <i class="ri-user-settings-line text-3xl mr-3"></i>
                <h3 class="text-lg font-semibold">My Profile</h3>
            </div>
            <p class="text-sm text-gray-600 mb-4">
                View and update your personal information.
            </p>
            <a href="{{ route('profile.show') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                Go to Profile <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>
    </div>

    {{-- Section Buku Populer --}}
    @if(isset($popularBooks) && $popularBooks->count() > 0)
    <div class="mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Popular</h2>
            <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                View All <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($popularBooks as $book)
            <div class="bg-gradient-to-br from-purple-100 via-purple-50 to-fuchsia-100 rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1 group flex flex-col">
                <a href="{{ route('books.show', $book->id) }}" class="block h-48 md:h-56 overflow-hidden"> {{-- Beri tinggi tetap untuk area gambar --}}
                    @if($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path))
                        <img src="{{ asset('storage/' . $book->cover_image_path) }}" alt="{{ $book->title }}"
                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300">
                    @else
                        {{-- Placeholder jika tidak ada gambar atau gambar tidak ditemukan --}}
                        <div class="w-full h-full flex items-center justify-center bg-purple-200">
                            <div class="w-20 h-28 md:w-24 md:h-32 bg-purple-400 rounded-lg shadow-md transform -rotate-6 group-hover:rotate-0 transition-transform duration-300"></div>
                        </div>
                    @endif
                </a>
                <div class="p-5 flex flex-col flex-grow"> {{-- flex-grow agar teks mengisi sisa ruang --}}
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-indigo-700 transition-colors mb-1 truncate" title="{{ $book->title }}">
                        <a href="{{ route('books.show', $book->id) }}">{{ Str::limit($book->title, 25) }}</a>
                    </h3>
                    <p class="text-xs text-gray-600 group-hover:text-gray-700 truncate">
                        {{ Str::limit($book->description, 40) }}
                    </p>
                    {{--
                    @if($book->category)
                    <span class="mt-2 inline-block bg-purple-200 text-purple-800 text-xs font-medium px-2 py-0.5 rounded-full self-start">{{ $book->category->name }}</span>
                    @endif
                    --}}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif


    {{-- Section Buku Ongoing/Terbaru --}}
    @if(isset($ongoingBooks) && $ongoingBooks->count() > 0)
    <div class="mb-10">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-800">New Additions</h2>
            <a href="{{ route('books.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                View All <i class="ri-arrow-right-s-line"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($ongoingBooks as $book)
            <div class="bg-gradient-to-br from-sky-100 via-sky-50 to-blue-100 rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl transform hover:-translate-y-1 group flex flex-col">
                <a href="{{ route('books.show', $book->id) }}" class="block h-48 md:h-56 overflow-hidden">
                    @if($book->cover_image_path && Storage::disk('public')->exists($book->cover_image_path))
                        <img src="{{ asset('storage/' . $book->cover_image_path) }}" alt="{{ $book->title }}"
                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-sky-200">
                            <div class="w-20 h-28 md:w-24 md:h-32 bg-sky-400 rounded-lg shadow-md transform rotate-3 group-hover:rotate-0 transition-transform duration-300"></div>
                            <div class="w-20 h-28 md:w-24 md:h-32 bg-yellow-300 rounded-lg shadow-md transform -rotate-3 group-hover:rotate-0 transition-transform duration-300 absolute top-1/2 left-1/2 -translate-x-1/4 -translate-y-3/4 group-hover:left-1/2 group-hover:-translate-x-1/2 group-hover:-translate-y-1/2"></div>
                        </div>
                    @endif
                </a>
                <div class="p-5 flex flex-col flex-grow">
                    <h3 class="text-lg font-semibold text-gray-800 group-hover:text-sky-700 transition-colors mb-1 truncate" title="{{ $book->title }}">
                        <a href="{{ route('books.show', $book->id) }}">{{ Str::limit($book->title, 25) }}</a>
                    </h3>
                    <p class="text-xs text-gray-600 group-hover:text-gray-700 truncate">
                        {{ Str::limit($book->description, 40) }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Card Discover yang sudah ada sebelumnya bisa tetap dipertahankan atau dihapus --}}
    
</div>
@endsection

@push('styles')
<style>
    .card-hover-original{
        transition: all  0.3s ease;
    }

    .card-hover-original:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush