<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Untuk membuat slug
use Illuminate\Validation\Rule; // Untuk validasi unique

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Category::latest()->withCount('books'); // Mengambil jumlah buku terkait

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $query->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('slug', 'like', '%' . $searchTerm . '%');
        }

        $categories = $query->paginate(10)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('categories', 'slug')],
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);

        // Pastikan slug unik jika di-generate otomatis
        if (empty($validated['slug'])) {
            $originalSlug = $slug;
            $count = 1;
            while (Category::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
        }


        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Category $category)
    {
        // Biasanya untuk kategori, halaman 'show' tidak terlalu umum.
        // Lebih sering langsung ke halaman 'edit' atau ditampilkan di daftar.
        // Kita bisa redirect ke edit atau hapus method ini jika tidak digunakan oleh Route::resource
        return redirect()->route('admin.categories.edit', $category->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('categories', 'slug')->ignore($category->id)],
        ]);

        // Jika slug diubah dan ingin di-generate ulang dari nama JIKA SLUG SAAT INI BERASAL DARI NAMA LAMA
        // Atau jika slug diubah manual, validasi di atas sudah cukup.
        // Untuk kesederhanaan, kita asumsikan slug diisi manual atau di-generate saat create.
        // Jika slug kosong saat update, bisa diisi dengan slug lama atau di-generate ulang.
        // $slug = $validated['slug'] ?? $category->slug; // Jika slug boleh kosong saat update (tidak direkomendasikan)

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'], // Atau $slug jika ada logika generate ulang
        ]);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category)
    {
        // Pengecekan apakah kategori masih digunakan oleh buku
        if ($category->books()->count() > 0) {
            return redirect()->route('admin.categories.index')
                             ->with('error', 'Cannot delete category: It is currently associated with one or more books. Please reassign or delete those books first.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Category deleted successfully.');
    }
}