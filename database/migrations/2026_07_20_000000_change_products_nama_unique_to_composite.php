<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // allow same nama for different ukuran; only nama+ukuran together must be unique
            $table->dropUnique('products_nama_unique');
            $table->unique(['nama', 'ukuran'], 'products_nama_ukuran_unique');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_nama_ukuran_unique');
            $table->unique('nama', 'products_nama_unique');
        });
    }
};
