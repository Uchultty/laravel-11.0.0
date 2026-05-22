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
            $table->string('id_barang_mentah', 20)->nullable()->after('id_barang');
            $table->foreign('id_barang_mentah')->references('id_barang')->on('barang')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->dropForeign(['id_barang_mentah']);
            $table->dropColumn('id_barang_mentah');
        });
    }
};
