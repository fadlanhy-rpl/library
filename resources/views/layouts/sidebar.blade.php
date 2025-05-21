<!-- resources/views/layouts/sidebar.blade.php -->
<aside id="sidebar"
    class="w-64 bg-white h-screen fixed top-0 left-0 z-50 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shadow-lg flex flex-col">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200">
        <a href="{{ Auth::check() && Auth::user()->isAdmin() ? route('admin.dashboard') : route('home') }}"
            class="flex items-center">
            <i class="ri-book-3-fill text-3xl text-indigo-600 mr-2"></i>
            <span class="text-2xl font-bold text-indigo-600">LandLibrary</span>
        </a>
        <!-- Close button for mobile -->
        <button id="sidebar-close" class="md:hidden text-gray-600 hover:text-indigo-600">
            <i class="ri-close-line text-2xl"></i>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 space-y-1 px-2"> {{-- Tambah px-2 agar item tidak terlalu mepet --}}
        @auth
            @if (Auth::user()->isAdmin())
                {{-- MENU ADMIN --}}
                <div class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                        <i class="ri-dashboard-line mr-3 text-lg"></i> {{-- Icon Dashboard Admin --}}
                        Admin Dashboard
                    </a>
                </div>
                <div class="sidebar-item {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.books.index') }}"
                        class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                        <i class="ri-book-3-line mr-3 text-lg"></i> {{-- Icon Manage Books --}}
                        Manage Books
                    </a>
                </div>
                <div class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                        <i class="ri-group-2-line mr-3 text-lg"></i> {{-- Icon Manage Users --}}
                        Manage Users
                    </a>
                </div>
                <div class="sidebar-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                        <i class="ri-price-tag-3-line mr-3 text-lg"></i>
                        Manage Categories
                    </a>
                </div>
                <div class="sidebar-item {{ request()->routeIs('user.borrowings.index') ? 'active' : '' }}">
                    {{-- <-- ITEM BARU --}}
                    <a href="{{ route('user.borrowings.index') }}"
                        class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                        <i class="ri-bookmark-3-line mr-3 text-lg"></i>
                        My Borrowed Books
                    </a>
                </div>
            @else
                {{-- MENU USER BIASA --}}
                <div class="sidebar-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('user.dashboard') }}"
                        class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                        <i class="ri-home-smile-2-line mr-3 text-lg"></i> {{-- Icon User Dashboard --}}
                        My Dashboard
                    </a>
                </div>
                <div
                    class="sidebar-item {{ request()->routeIs('books.index') || request()->routeIs('books.show') ? 'active' : '' }}">
                    <a href="{{ route('books.index') }}"
                        class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                        <i class="ri-book-open-line mr-3 text-lg"></i>
                        Browse Books
                    </a>
                </div>
                <div class="sidebar-item {{ request()->routeIs('user.borrowings.index') ? 'active' : '' }}">
                    {{-- <-- ITEM BARU --}}
                    <a href="{{ route('user.borrowings.index') }}"
                        class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                        <i class="ri-bookmark-3-line mr-3 text-lg"></i>
                        My Borrowed Books
                    </a>
                </div>
                {{-- Tambahkan menu lain untuk user, misal: My Profile, My Borrowed Books --}}
                {{-- <div class="sidebar-item">
                    <a href="#" class="flex items-center py-2.5 px-4 ...">
                        <i class="ri-user-line mr-3 text-lg"></i> My Profile
                    </a>
                </div> --}}
            @endif

            {{-- Menu Settings (Umum atau bisa juga dispesifikkan per role) --}}
            {{-- <div class="sidebar-item">
                <a href="#"
                    class="flex items-center py-2.5 px-4 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 rounded-md transition duration-200">
                    <i class="ri-settings-3-line mr-3 text-lg"></i>
                    Settings
                </a>
            </div> --}}
        @endauth
    </nav>

    <!-- Sidebar Footer (Upgrade to Pro - bisa disembunyikan untuk user biasa jika perlu) -->
    @auth
        @if (Auth::user()->isAdmin())
            <div class="p-4 border-t border-gray-200">
                <div class="bg-indigo-50 p-4 rounded-lg text-center">
                    <h4 class="font-semibold text-indigo-700 mb-1">Welcome to E-library</h4>
                    <p class="text-xs text-indigo-500 mb-3">Unlock more features and facilities for your library.</p>
                    <button class="w-full bg-indigo-600 text-white py-2 rounded-md text-sm hover:bg-indigo-700 transition">
                        Upgrade <i class="ri-arrow-right-line ml-1"></i>
                    </button>
                </div>
            </div>
        @endif
    @endauth
</aside>
