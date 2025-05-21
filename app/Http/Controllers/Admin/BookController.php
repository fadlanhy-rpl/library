<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class BookController extends Controller
{
    /**
     * Display a listing of the books.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Book::with(['creator', 'category'])->latest();

        // Search functionality
        if ($request->filled('search')) { // Gunakan filled() untuk cek apakah ada isinya
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('author', 'like', '%' . $searchTerm . '%')
                  ->orWhere('isbn', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $books = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new book.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.books.create', compact('categories'));
    }

    /**
     * Store a newly created book in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            // ... (validasi field lain tetap sama) ...
            'cover_image' => ['nullable', 'string'], // Menerima base64 string
            // 'cover_image_original' => ['nullable','image','mimes:jpeg,png,jpg,gif,svg,webp','max:2048'], // Validasi untuk file asli jika masih dikirim
            // ... (validasi field lain tetap sama) ...
            'stock' => ['nullable', 'integer', 'min:0'],
            'shelf_location' => ['nullable', 'string', 'max:255'],
            'format' => ['nullable', 'string', 'max:50'],
            'acquisition_date' => ['nullable', 'date'],
            'publisher' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'year' => ['required', 'integer', 'digits:4', 'min:1000', 'max:' . date('Y')],
            'pages' => ['required', 'integer', 'min:1'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')],
        ]);

        $bookData = $validated;
        $bookData['created_by'] = Auth::id();

        // Hapus 'cover_image_original' jika ada dari $bookData karena tidak disimpan ke DB
        // unset($bookData['cover_image_original']);

        if ($request->filled('cover_image')) { // Jika ada data base64 di input 'cover_image'
            $imageData = $request->input('cover_image');
            if (strpos($imageData, ';base64,') === false) {
                return back()->withErrors(['cover_image' => 'Invalid cover image data format.'])->withInput();
            }
            list($type, $imageData) = explode(';', $imageData);
            list(, $imageData)      = explode(',', $imageData);
            $imageData = base64_decode($imageData);

            $extension = '';
            if (strpos($type, 'image/jpeg') !== false || strpos($type, 'image/jpg') !== false) { $extension = 'jpg'; }
            elseif (strpos($type, 'image/png') !== false) { $extension = 'png'; }
            elseif (strpos($type, 'image/gif') !== false) { $extension = 'gif'; }
            elseif (strpos($type, 'image/webp') !== false) { $extension = 'webp'; }
            else { return back()->withErrors(['cover_image' => 'Unsupported cover image type.'])->withInput(); }

            $fileName = 'book_covers/' . Str::random(40) . '.' . $extension;
            Storage::disk('public')->put($fileName, $imageData);
            $bookData['cover_image_path'] = $fileName;
        }
        // Hapus key 'cover_image' dari $bookData karena kita sudah memprosesnya ke 'cover_image_path'
        unset($bookData['cover_image']);


        Book::create($bookData);

        return redirect()->route('admin.books.index')
                         ->with('success', 'Book "' . $bookData['title'] . '" created successfully.');
    }


    /**
     * Display the specified book.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\View\View
     */
    public function show(Book $book)
    {
        $book->load(['creator', 'category']); // Eager load relasi
        return view('admin.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\View\View
     */
    public function edit(Book $book)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            // ... (validasi field lain tetap sama) ...
            'cover_image' => ['nullable', 'string'], // Menerima base64 string
            // 'cover_image_original_edit' => ['nullable','image','mimes:jpeg,png,jpg,gif,svg,webp','max:2048'],
            // ... (validasi field lain tetap sama) ...
            'stock' => ['nullable', 'integer', 'min:0'],
            'shelf_location' => ['nullable', 'string', 'max:255'],
            'format' => ['nullable', 'string', 'max:50'],
            'acquisition_date' => ['nullable', 'date'],
            'publisher' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'year' => ['required', 'integer', 'digits:4', 'min:1000', 'max:' . date('Y')],
            'pages' => ['required', 'integer', 'min:1'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book->id)],
        ]);

        $bookData = $validated;
        // unset($bookData['cover_image_original_edit']);

        if ($request->filled('cover_image')) { // Jika ada data base64 baru
            // Hapus gambar lama jika ada
            if ($book->cover_image_path) {
                Storage::disk('public')->delete($book->cover_image_path);
            }

            $imageData = $request->input('cover_image');
            if (strpos($imageData, ';base64,') === false) {
                return back()->withErrors(['cover_image' => 'Invalid cover image data format.'])->withInput();
            }
            list($type, $imageData) = explode(';', $imageData);
            list(, $imageData)      = explode(',', $imageData);
            $imageData = base64_decode($imageData);

            $extension = '';
            if (strpos($type, 'image/jpeg') !== false || strpos($type, 'image/jpg') !== false) { $extension = 'jpg'; }
            elseif (strpos($type, 'image/png') !== false) { $extension = 'png'; }
            elseif (strpos($type, 'image/gif') !== false) { $extension = 'gif'; }
            elseif (strpos($type, 'image/webp') !== false) { $extension = 'webp'; }
            else { return back()->withErrors(['cover_image' => 'Unsupported cover image type.'])->withInput(); }

            $fileName = 'book_covers/' . Str::random(40) . '.' . $extension;
            Storage::disk('public')->put($fileName, $imageData);
            $bookData['cover_image_path'] = $fileName;
        }
        // Hapus key 'cover_image' dari $bookData
        unset($bookData['cover_image']);


        $book->update($bookData);

        return redirect()->route('admin.books.index')
                         ->with('success', 'Book "' . $book->title . '" updated successfully.');
    }

    /**
     * Remove the specified book from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Book $book)
    {
        // Hapus gambar terkait dari storage jika ada
        if ($book->cover_image_path) {
            Storage::disk('public')->delete($book->cover_image_path);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
                         ->with('success', 'Book " '.$book->title.' " deleted successfully.');
    }
}