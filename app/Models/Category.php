<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Import Str facade

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Relasi one-to-many dengan model Book.
     * Satu kategori bisa memiliki banyak buku.
     */
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Boot method untuk model.
     * Otomatis membuat slug saat kategori baru dibuat atau namanya diubah.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                 $category->slug = Str::slug($category->name);
            } else if ($category->isDirty('name') && !empty($category->slug) && $category->slug === Str::slug($category->getOriginal('name'))) {
                // Jika slug sudah ada dan berasal dari nama lama, update slug juga
                $category->slug = Str::slug($category->name);
            }
        });
    }
}