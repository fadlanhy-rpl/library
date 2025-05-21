<!-- resources/views/layouts/header.blade.php -->
<header class="bg-white shadow-sm h-[4.27rem] flex items-center justify-between px-4 md:px-6 sticky top-0 z-50">
    <div class="flex items-center">
        <!-- Mobile Sidebar Toggle -->
        <button id="sidebar-toggle" class="md:hidden text-gray-600 hover:text-indigo-600 mr-4">
            <i class="ri-menu-line text-2xl"></i>
        </button>

        <!-- Search Bar -->
        <form id="globalSearchForm" action="{{ route('books.index') }}" method="GET" class="relative hidden md:block">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <button type="submit" class="focus:outline-none"> {{-- Tombol submit untuk ikon --}}
                    <i class="ri-search-line text-gray-400 hover:text-indigo-600"></i>
                </button>
            </span>
            <input type="search" name="search" {{-- Tambahkan atribut name="search" --}} id="globalSearchInput"
                value="{{ request('search') }}" {{-- Tampilkan query pencarian saat ini jika ada --}}
                class="w-full md:w-64 lg:w-96 pl-10 pr-4 py-2 rounded-lg bg-gray-100 text-sm focus:outline-none focus:bg-white focus:ring-1 focus:ring-indigo-300"
                placeholder="Search books Title, Author, Genre...">
        </form>

    </div>

    <div class="flex items-center space-x-3 md:space-x-4">
        @auth
            @if (Auth::user()->isAdmin())
                <a href="{{ route('admin.books.create') }}"
                    class="hidden sm:flex items-center bg-indigo-600 text-white px-3 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    <i class="ri-add-line mr-1"></i> Add New Book
                </a>
            @endif
        @endauth

        <button class="text-gray-500 hover:text-indigo-600 relative">
            <i class="ri-notification-3-line text-xl"></i>
            <span class="absolute -top-1 -right-1 h-2 w-2 bg-red-500 rounded-full"></span>
        </button>

        @auth
            <div class="relative" x-data="{ dropdownOpen: false }">
                <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 focus:outline-none">
                    <img src="{{ Auth::user()->profile_image_path ? asset('storage/' . Auth::user()->profile_image_path) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=random&color=fff&size=32&rounded=true' }}"
                        alt="User Avatar" class="w-8 h-8 rounded-full object-cover"> {{-- Tambah object-cover --}}
                    <span class="hidden md:inline text-sm text-gray-700">{{ Auth::user()->name }}</span>
                    <i class="ri-arrow-down-s-line hidden md:inline text-gray-500"
                        :class="{ 'rotate-180': dropdownOpen }"></i>
                </button>
                <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" x-transition...
                    style="display: none;">
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Admin Panel</a>
                    @else
                        <a href="{{ route('user.dashboard') }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">My Dashboard</a>
                    @endif
                    <a href="{{ route('profile.show') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">My Profile</a>
                    {{-- <-- LINK BARU --}}
                    {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">Settings</a> --}}
                    <hr class="my-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</header>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('globalSearchForm');
            const searchInput = document.getElementById('globalSearchInput');

            // Jika ingin submit saat menekan Enter di input
            searchInput?.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    // e.preventDefault(); // Tidak perlu jika tombol submit ada di dalam form
                    searchForm?.submit();
                }
            });

            // Jika Anda ingin membuat input search ini juga bisa muncul di mobile
            // dan memicu pencarian, Anda perlu logika JS tambahan untuk toggle
            // visibilitas form dan menangani submit.
        });
    </script>
@endpush
