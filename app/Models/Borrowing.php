<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
        'fine_amount', // <-- TAMBAHKAN INI
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
        'fine_amount' => 'decimal:2', // <-- TAMBAHKAN INI
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Helper untuk mengecek apakah peminjaman terlambat
    public function isOverdue(): bool
    {
        // Jika belum dikembalikan dan tanggal sekarang sudah lewat dari due_date
        return !$this->returned_at && now()->greaterThan($this->due_date);
    }
}