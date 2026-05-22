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
            if (! Schema::hasColumn('barang_masuk', 'estimasi_tiba')) {
                $table->date('estimasi_tiba')->nullable()->after('tanggal_masuk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            if (Schema::hasColumn('barang_masuk', 'estimasi_tiba')) {
                $table->dropColumn('estimasi_tiba');
            }
        });
    }
};
