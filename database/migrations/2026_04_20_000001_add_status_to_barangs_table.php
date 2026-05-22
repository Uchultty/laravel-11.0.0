<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            if (!Schema::hasColumn('barang', 'status')) {
                $table->string('status')->nullable()->after('nama');
            }
        });

        DB::statement("UPDATE barang SET status = 'material' WHERE EXISTS (SELECT 1 FROM barang_masuk WHERE barang_masuk.barang_id = barang.id_barang)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
