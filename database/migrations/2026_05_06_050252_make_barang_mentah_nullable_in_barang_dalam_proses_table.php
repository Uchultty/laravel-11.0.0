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
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->string('barang_mentah')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->string('barang_mentah')->nullable(false)->change();
        });
    }
};
