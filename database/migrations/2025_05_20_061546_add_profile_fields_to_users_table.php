<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_image_path')->nullable()->after('remember_token');
            $table->date('date_of_birth')->nullable()->after('profile_image_path');
            // Tambahkan kolom lain jika perlu, misal:
            // $table->string('phone_number')->nullable()->after('date_of_birth');
            // $table->text('bio')->nullable()->after('phone_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_image_path',
                'date_of_birth',
                // 'phone_number',
                // 'bio',
            ]);
        });
    }
};