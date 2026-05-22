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
            if (! Schema::hasColumn('barang_dalam_proses', 'id_customer')) {
                $table->unsignedBigInteger('id_customer')->nullable()->after('id_user');
                $table->foreign('id_customer')->references('id_customer')->on('customers')->nullOnDelete();
            }

            if (! Schema::hasColumn('barang_dalam_proses', 'tanggal_selesai')) {
                $table->date('tanggal_selesai')->nullable()->after('barang_mentah');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            if (Schema::hasColumn('barang_dalam_proses', 'id_customer')) {
                $table->dropForeign(['id_customer']);
                $table->dropColumn('id_customer');
            }

            if (Schema::hasColumn('barang_dalam_proses', 'tanggal_selesai')) {
                $table->dropColumn('tanggal_selesai');
            }
        });
    }
};
