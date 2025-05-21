@extends('layouts.app')

@section('title', 'Edit Profile')

@push('styles') {{-- Menggunakan @push untuk CSS spesifik halaman --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<style>
    /* Style untuk preview gambar dan modal cropping */
    .img-container img {
        max-width: 100%; /* Pastikan gambar tidak melebihi kontainer */
    }
    #cropModal .modal-content {
        max-width: 500px; /* Lebar modal */
    }
    #imageToCrop {
        display: block;
        max-width: 100%; /* Gambar di dalam cropper */
    }
    .cropper-view-box,
    .cropper-face {
      border-radius: 50%; /* Membuat area crop bulat jika aspectRatio 1 dan viewMode 1/2/3 */
    }
</style>
@endpush

@section('content')
<div class="p-4 md:p-6">
    <div class="max-w-3xl mx-auto">
        {{-- Form Update Informasi Profil --}}
        <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 mb-8">
            {{-- ... (Bagian Nama, Email, Tanggal Lahir tetap sama) ... --}}
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileUpdateForm">
                @csrf
                @method('PATCH')
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}"
                               max="{{ now()->toDateString() }}"
                               class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                        @error('date_of_birth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Bagian Upload Gambar Profil dengan Cropper --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                        <div class="mt-1 flex items-center">
                            <span class="inline-block h-20 w-20 rounded-full overflow-hidden bg-gray-100 mr-4 shadow">
                                <img id="profileImagePreview" class="h-full w-full object-cover"
                                     src="{{ $user->profile_image_path ? asset('storage/' . $user->profile_image_path) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random&color=fff&size=80' }}"
                                     alt="Current profile photo">
                            </span>
                            <label for="profile_image_input" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Change
                            </label>
                            <input type="file" id="profile_image_input" name="profile_image_original" accept="image/*" class="hidden">
                            {{-- Input hidden untuk menyimpan data gambar yang sudah di-crop (base64) --}}
                            <input type="hidden" name="profile_image" id="cropped_image_data">
                        </div>
                        @error('profile_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('profile_image_original') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
        {{-- ... (Form Update Password tetap sama) ... --}}
    </div>
</div>

<!-- Modal untuk Cropping Gambar -->
<div id="cropModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full modal-content">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Crop Your Image
                        </h3>
                        <div class="mt-2 img-container">
                            <img id="imageToCrop" src="#" alt="Image to crop">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="cropAndUploadButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Crop & Use
                </button>
                <button type="button" id="cancelCropButton" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('profile_image_input');
    const imagePreview = document.getElementById('profileImagePreview');
    const imageToCrop = document.getElementById('imageToCrop');
    const cropModal = document.getElementById('cropModal');
    const cropAndUploadButton = document.getElementById('cropAndUploadButton');
    const cancelCropButton = document.getElementById('cancelCropButton');
    const croppedImageDataInput = document.getElementById('cropped_image_data');
    let cropper;
    let originalFile = null;

    // Trigger file input saat tombol "Change" (label) diklik
    document.querySelector('label[for="profile_image_input"]').addEventListener('click', (e) => {
        imageInput.click();
    });

    imageInput.addEventListener('change', function (event) {
        const files = event.target.files;
        if (files && files.length > 0) {
            originalFile = files[0]; // Simpan file asli untuk nama dan tipe jika perlu
            const reader = new FileReader();
            reader.onload = function (e) {
                imageToCrop.src = e.target.result;
                cropModal.classList.remove('hidden'); // Tampilkan modal
                if (cropper) {
                    cropper.destroy(); // Hancurkan instance cropper lama jika ada
                }
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1 / 1, // Untuk gambar profil persegi atau bulat
                    viewMode: 1,        // Batasi area crop agar tidak keluar dari canvas
                    dragMode: 'move',
                    background: false,  // Hilangkan grid background
                    cropBoxResizable: true,
                    cropBoxMovable: true,
                    // Untuk membuat area crop bulat secara visual (CSS juga diperlukan)
                    // checkCrossOrigin: false, // Jika gambar dari sumber eksternal (tidak relevan di sini)
                    // autoCropArea: 0.8,
                    ready: function () {
                        // Untuk membuat cropper box bulat (jika aspectRatio 1)
                        // this.cropper.setCropBoxData({ width: Math.min(this.cropper.getContainerData().width, this.cropper.getContainerData().height) });
                    }
                });
            };
            reader.readAsDataURL(originalFile);
        }
    });

    cancelCropButton.addEventListener('click', function () {
        cropModal.classList.add('hidden');
        if (cropper) {
            cropper.destroy();
        }
        imageInput.value = ''; // Reset input file
        croppedImageDataInput.value = ''; // Hapus data crop lama
    });

    cropAndUploadButton.addEventListener('click', function () {
        if (cropper) {
            // Dapatkan canvas yang sudah di-crop
            // Anda bisa mengatur ukuran output di sini
            const canvas = cropper.getCroppedCanvas({
                width: 256,  // Ukuran output gambar profil
                height: 256,
                // fillColor: '#fff' // Jika ada area transparan dan ingin diisi putih
            });

            // Ubah canvas menjadi data URL (base64)
            const croppedImageDataURL = canvas.toDataURL(originalFile.type || 'image/jpeg'); // Gunakan tipe file asli

            // Tampilkan preview gambar yang sudah di-crop
            imagePreview.src = croppedImageDataURL;

            // Set nilai input hidden dengan data base64
            croppedImageDataInput.value = croppedImageDataURL;

            // Sembunyikan modal dan hancurkan cropper
            cropModal.classList.add('hidden');
            cropper.destroy();
            imageInput.value = ''; // Reset input file asli karena sudah di-handle
        }
    });

    // Mencegah submit form jika modal crop aktif
    // document.getElementById('profileUpdateForm').addEventListener('submit', function(e) {
    //     if (!cropModal.classList.contains('hidden')) {
    //         e.preventDefault();
    //         Swal.fire('Info', 'Please finish cropping or cancel before submitting the form.', 'info');
    //     }
    // });
});
</script>
@endpush
@endsection