<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Foreign key ke tabel categories
            $table->foreignId('category_id')->nullable()->after('pages')->constrained('categories')->onDelete('set null');
            $table->string('author')->nullable()->after('category_id');
            $table->string('isbn')->nullable()->unique()->after('author');
            $table->string('cover_image_path')->nullable()->after('isbn');
            $table->integer('stock')->nullable()->default(0)->after('cover_image_path');
            $table->string('shelf_location')->nullable()->after('stock');
            $table->string('format')->nullable()->after('shelf_location'); // Contoh: 'Hardcover', 'Paperback', 'E-Book'
            $table->date('acquisition_date')->nullable()->after('format');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['category_id']); // Hapus foreign key constraint dulu
            $table->dropColumn([
                'category_id',
                'author',
                'isbn',
                'cover_image_path',
                'stock',
                'shelf_location',
                'format',
                'acquisition_date',
            ]);
        });
    }
};