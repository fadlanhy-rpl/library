@extends('layouts.app')

@section('title', 'Add New Book')

@push('styles') {{-- Menggunakan @push untuk CSS spesifik halaman --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<style>
    .img-container img { max-width: 100%; }
    #cropBookCoverModal .modal-content { max-width: 600px; } /* Sesuaikan lebar modal jika perlu */
    #imageToCropBookCover { display: block; max-width: 100%; }
    /* .cropper-view-box, .cropper-face { border-radius: 0; } */ /* Biarkan default (persegi) untuk cover buku */
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Add New Book Information</h2>
            <a href="{{ route('admin.books.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                <i class="ri-arrow-left-line mr-1"></i>Back to List
            </a>
        </div>

        <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" id="addBookForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- ... (Input Title, Publisher, Year, Pages, Category, Author tetap sama) ... --}}
                <div class="col-span-2 md:col-span-1">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Book Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2 md:col-span-1">
                    <label for="publisher" class="block text-sm font-medium text-gray-700 mb-1">Publisher</label>
                    <input type="text" id="publisher" name="publisher" value="{{ old('publisher') }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    @error('publisher') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-1">
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Publication Year</label>
                    <input type="number" id="year" name="year" value="{{ old('year') }}" min="1000" max="{{ date('Y') }}" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    @error('year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-1">
                    <label for="pages" class="block text-sm font-medium text-gray-700 mb-1">Page Count</label>
                    <input type="number" id="pages" name="pages" value="{{ old('pages') }}" min="1" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    @error('pages') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-1 relative">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category_id" name="category_id"
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 appearance-none">
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                     <i class="ri-arrow-down-s-line absolute right-3 top-1/2 mt-2.5 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-1">
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author</label>
                    <input type="text" id="author" name="author" value="{{ old('author') }}"
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                    @error('author') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Cover Image dengan Cropper -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image</label>
                    <div class="mt-1 flex items-center">
                        <span class="inline-block h-40 w-32 {{-- Sesuaikan rasio cover buku --}} rounded-md overflow-hidden bg-gray-100 mr-4 shadow">
                            <img id="bookCoverPreview" class="h-full w-full object-cover"
                                 src="https://via.placeholder.com/128x160.png?text=Cover+Preview" {{-- Placeholder awal --}}
                                 alt="Book cover preview">
                        </span>
                        <label for="cover_image_input" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Select Image
                        </label>
                        <input type="file" id="cover_image_input" name="cover_image_original" accept="image/*" class="hidden">
                        <input type="hidden" name="cover_image" id="cropped_cover_data"> {{-- Untuk data base64 --}}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Recommended aspect ratio approx. 2:3. Max file size: 2MB</p>
                    @error('cover_image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @error('cover_image_original') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- ... (Input Description, ISBN, Stock, Format, Shelf Location, Acquisition Date tetap sama) ... --}}
                 <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="4" required
                        class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="col-span-2">
                    <h3 class="text-md font-semibold text-gray-700 mb-3 border-b border-gray-200 pb-2">Additional Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="isbn" class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                            <input type="text" id="isbn" name="isbn" value="{{ old('isbn') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                            @error('isbn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0"
                                class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                            @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                         <div class="relative">
                            <label for="format" class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                            <select id="format" name="format" class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 appearance-none">
                                <option value="">Select format</option>
                                <option value="Hardcover" {{ old('format') == 'Hardcover' ? 'selected' : '' }}>Hardcover</option>
                                <option value="Paperback" {{ old('format') == 'Paperback' ? 'selected' : '' }}>Paperback</option>
                                <option value="E-Book" {{ old('format') == 'E-Book' ? 'selected' : '' }}>E-Book</option>
                                <option value="Audiobook" {{ old('format') == 'Audiobook' ? 'selected' : '' }}>Audiobook</option>
                            </select>
                            <i class="ri-arrow-down-s-line absolute right-3 top-1/2 mt-2.5 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                             @error('format') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="shelf_location" class="block text-sm font-medium text-gray-700 mb-1">Shelf Location</label>
                            <input type="text" id="shelf_location" name="shelf_location" value="{{ old('shelf_location') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                            @error('shelf_location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                         <div class="col-span-2 md:col-span-1">
                            <label for="acquisition_date" class="block text-sm font-medium text-gray-700 mb-1">Acquisition Date</label>
                            <input type="date" id="acquisition_date" name="acquisition_date" value="{{ old('acquisition_date') }}"
                                class="w-full px-4 py-2.5 bg-gray-50 rounded-lg text-sm focus:outline-none border border-gray-200 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400">
                            @error('acquisition_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.books.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                    Save Book
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal untuk Cropping Gambar Cover Buku -->
<div id="cropBookCoverModal" class="fixed z-[60] inset-0 overflow-y-auto hidden" aria-labelledby="modal-title-cover" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full modal-content">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-cover">
                            Crop Book Cover
                        </h3>
                        <div class="mt-2 img-container">
                            <img id="imageToCropBookCover" src="#" alt="Image to crop for book cover">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="cropAndSetCoverButton" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Crop & Set Cover
                </button>
                <button type="button" id="cancelCropCoverButton" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Cropper.js sudah di-include di layouts/app.blade.php jika global, atau include di sini jika spesifik --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script> --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const coverImageInput = document.getElementById('cover_image_input');
    const coverPreview = document.getElementById('bookCoverPreview');
    const imageToCropCover = document.getElementById('imageToCropBookCover');
    const cropCoverModal = document.getElementById('cropBookCoverModal');
    const cropAndSetButton = document.getElementById('cropAndSetCoverButton');
    const cancelCropCoverBtn = document.getElementById('cancelCropCoverButton');
    const croppedCoverDataInput = document.getElementById('cropped_cover_data');
    let bookCoverCropper;
    let originalBookCoverFile = null;

    document.querySelector('label[for="cover_image_input"]').addEventListener('click', (e) => {
        coverImageInput.click();
    });

    coverImageInput.addEventListener('change', function (event) {
        const files = event.target.files;
        if (files && files.length > 0) {
            originalBookCoverFile = files[0];
            const reader = new FileReader();
            reader.onload = function (e) {
                imageToCropCover.src = e.target.result;
                cropCoverModal.classList.remove('hidden');
                if (bookCoverCropper) {
                    bookCoverCropper.destroy();
                }
                bookCoverCropper = new Cropper(imageToCropCover, {
                    aspectRatio: 2 / 3, // Rasio aspek umum untuk cover buku (tinggi lebih dominan)
                                        // Sesuaikan jika Anda ingin rasio lain, misal 3/4 atau 0 (bebas)
                    viewMode: 1,
                    dragMode: 'move',
                    background: false,
                    cropBoxResizable: true,
                    cropBoxMovable: true,
                    // Anda bisa mengatur minCropBoxWidth dan minCropBoxHeight
                    // minCropBoxWidth: 100,
                    // minCropBoxHeight: 150,
                });
            };
            reader.readAsDataURL(originalBookCoverFile);
        }
    });

    cancelCropCoverBtn.addEventListener('click', function () {
        cropCoverModal.classList.add('hidden');
        if (bookCoverCropper) {
            bookCoverCropper.destroy();
        }
        coverImageInput.value = '';
        croppedCoverDataInput.value = '';
    });

    cropAndSetButton.addEventListener('click', function () {
        if (bookCoverCropper) {
            const canvas = bookCoverCropper.getCroppedCanvas({
                // Sesuaikan ukuran output. Kualitas lebih baik dengan ukuran lebih besar,
                // tapi perhatikan ukuran file.
                // Contoh: width: 400, (height akan menyesuaikan aspectRatio)
                // Jika ingin kualitas lebih baik untuk detail:
                // width: 600,
                // height: 900, // Jika aspectRatio 2/3
                // Atau biarkan Cropper menentukan berdasarkan crop box:
                // fillStyle: '#fff', // Untuk mengisi area transparan jika output PNG
            });

            const croppedImageDataURL = canvas.toDataURL(originalBookCoverFile.type || 'image/jpeg', 0.9); // Kualitas 0.9 untuk JPEG

            coverPreview.src = croppedImageDataURL;
            croppedCoverDataInput.value = croppedImageDataURL;

            cropCoverModal.classList.add('hidden');
            bookCoverCropper.destroy();
            coverImageInput.value = '';
        }
    });
});
</script>
@endpush