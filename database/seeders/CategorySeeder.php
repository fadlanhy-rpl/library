<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Fiction', 'Non-Fiction', 'Science', 'History', 'Biography',
            'Fantasy', 'Mystery', 'Romance', 'Technology', 'Self-Help',
            'Business', 'Poetry'
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName), // Otomatis generate slug
            ]);
        }
    }
}