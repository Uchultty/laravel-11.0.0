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
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_barang_id')->nullable()->after('barang_id');
            $table->foreign('jenis_barang_id')->references('id_jenis_barang')->on('jenis_barang')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['jenis_barang_id']);
            $table->dropColumn('jenis_barang_id');
        });
    }
};
