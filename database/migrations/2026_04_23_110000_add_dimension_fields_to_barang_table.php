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
        Schema::table('barang', function (Blueprint $table) {
            if (! Schema::hasColumn('barang', 'panjang')) {
                $table->decimal('panjang', 10, 2)->nullable()->after('stok_minimum');
            }

            if (! Schema::hasColumn('barang', 'lebar')) {
                $table->decimal('lebar', 10, 2)->nullable()->after('panjang');
            }

            if (! Schema::hasColumn('barang', 'tinggi')) {
                $table->decimal('tinggi', 10, 2)->nullable()->after('lebar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            if (Schema::hasColumn('barang', 'tinggi')) {
                $table->dropColumn('tinggi');
            }

            if (Schema::hasColumn('barang', 'lebar')) {
                $table->dropColumn('lebar');
            }

            if (Schema::hasColumn('barang', 'panjang')) {
                $table->dropColumn('panjang');
            }
        });
    }
};
