<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LandLibrary') - {{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f3fb;
        }

        .sidebar-item.active a {
            color: #6366f1;
            font-weight: 500;
        }

        .sidebar-item.active {
            border-left: 3px solid #6366f1;
            background-color: rgba(99, 102, 241, 0.08);
        }

        .table-responsive {
            overflow-x: auto;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #c4b5fd;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a78bfa;
        }
    </style>
    @stack('styles')
</head>

<body class="h-screen overflow-hidden">
    <div class="flex h-screen bg-gray-100"> {{-- Tambah bg-gray-100 untuk konsistensi --}}
        @include('layouts.sidebar')

        <main
            class="flex-1 overflow-x-hidden overflow-y-auto  ml-[0] md:ml-[16rem] lg:ml-[16rem] xl:ml-[16rem] sm:ml-0  w-full isi">
            {{-- Padding utama konten --}}

            @include('layouts.header')

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md shadow-sm">
                    <i class="ri-checkbox-circle-line mr-2"></i>{{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md shadow-sm">
                    <i class="ri-error-warning-line mr-2"></i>{{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md shadow-sm">
                    <strong class="font-bold">Oops! Something went wrong.</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- Versi pendek tanpa /dist/sweetalert2.min.js juga biasanya berfungsi --}}

    <!-- Custom JS (confirm.js) -->
    <script src="{{ asset('js/confirm.js') }}"></script> {{-- Pastikan path ini benar --}}

    <script>
        // Fungsi untuk men-decode entitas HTML
        function decodeHtmlEntities(text) {
            if (typeof text !== 'string') {
                return text; // Kembalikan apa adanya jika bukan string
            }
            const textarea = document.createElement('textarea');
            textarea.innerHTML = text;
            return textarea.value;
        }
    </script>

    @if (session('success'))
        <script>
            // Ambil pesan dari session (Blade akan melakukan HTML escaping)
            let rawSuccessMessage = "{{ session('success') }}";
            // Decode entitas HTML sebelum ditampilkan oleh SweetAlert
            let decodedSuccessMessage = decodeHtmlEntities(rawSuccessMessage);

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: decodedSuccessMessage,
                timer: 3000, // Notifikasi akan hilang setelah 3 detik
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            let rawErrorMessage = "{{ session('error') }}";
            let decodedErrorMessage = decodeHtmlEntities(rawErrorMessage);

            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: decodedErrorMessage,
                // timer: 5000,
                // showConfirmButton: true // Defaultnya true
            });
        </script>
    @endif

    {{-- @stack('scripts') <!-- Untuk JS spesifik halaman lainnya --> --}}

    <script>
        // JavaScript Anda sebelumnya sudah cukup baik
        // ... (kode JavaScript Anda) ...
        document.addEventListener('DOMContentLoaded', () => {
            const sidebar = document.getElementById('sidebar');
            const mainContentArea = document.querySelector('main').parentElement;
            const toggleBtn = document.getElementById('sidebar-toggle');
            const closeBtn = document.getElementById('sidebar-close');

            // Fungsi untuk mengatur margin konten utama berdasarkan status sidebar
            function adjustMainContentMargin() {
                if (window.innerWidth >= 768) { // md breakpoint
                    if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                        mainContentArea.style.marginLeft = sidebar.offsetWidth + 'px';
                    } else {
                        mainContentArea.style.marginLeft = '0px';
                    }
                } else {
                    mainContentArea.style.marginLeft = '0px'; // Tidak ada margin di layar kecil
                }
            }


            toggleBtn?.addEventListener('click', () => {
                sidebar?.classList.remove('-translate-x-full');
                sidebar?.classList.add('translate-x-0');
                adjustMainContentMargin();
            });

            closeBtn?.addEventListener('click', () => {
                sidebar?.classList.add('-translate-x-full');
                sidebar?.classList.remove('translate-x-0');
                adjustMainContentMargin();
            });

            function handleResize() {
                if (window.innerWidth < 768) {
                    if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                        sidebar.classList.remove('translate-x-0');
                    }
                } else {
                    if (sidebar && sidebar.classList.contains('-translate-x-full')) {
                        // Opsional: Buka sidebar secara otomatis di layar besar jika sebelumnya tertutup
                        // sidebar.classList.remove('-translate-x-full');
                        // sidebar.classList.add('translate-x-0');
                    }
                }
                adjustMainContentMargin();
            }

            // Panggil saat load dan resize
            adjustMainContentMargin(); // Panggil saat load
            window.addEventListener('resize', handleResize);

            const sidebarLinks = document.querySelectorAll('#sidebar a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 768 && sidebar && !sidebar.classList.contains(
                            '-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                        sidebar.classList.remove('translate-x-0');
                        adjustMainContentMargin();
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
