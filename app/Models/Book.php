<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Borrowing; // <--- TAMBAHKAN BARIS INI

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'publisher',
        'description',
        'year',
        'pages',
        'created_by',
        'category_id',
        'author',
        'isbn',
        'cover_image_path',
        'stock',
        'shelf_location',
        'format',
        'acquisition_date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all of the borrowings for the Book.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class); // Sekarang Borrowing dikenali
    }
}