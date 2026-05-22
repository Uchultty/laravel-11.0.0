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
            $table->string('satuan')->nullable()->after('barang_mentah');
            $table->string('ukuran')->nullable()->after('satuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'ukuran']);
        });
    }
};
