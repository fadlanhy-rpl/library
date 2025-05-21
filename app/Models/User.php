<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Borrowing;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens; // If using Sanctum for API tokens

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image_path', // <-- BARU
        'date_of_birth',    // <-- BARU
        // 'phone_number',
        // 'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
    ];

    /**
     * Get the books created by this user (admin).
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'created_by');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function hasBorrowed(Book $book): bool
    {
        return $this->borrowings()
            ->where('book_id', $book->id)
            ->where('status', 'borrowed') // Atau bisa juga cek whereNull('returned_at')
            ->exists();
    }

    public function getAgeAttribute(): ?int
    {
        if ($this->date_of_birth) {
            return Carbon::parse($this->date_of_birth)->age;
        }
        return null;
    }
}
