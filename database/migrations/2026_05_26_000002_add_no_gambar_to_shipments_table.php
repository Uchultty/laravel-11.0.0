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
        $tableName = Schema::hasTable('pengiriman_barang') ? 'pengiriman_barang' : (Schema::hasTable('shipments') ? 'shipments' : null);

        if ($tableName === null) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            if (! Schema::hasColumn($tableName, 'no_gambar')) {
                $table->string('no_gambar', 100)->nullable()->after('no_po');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableName = Schema::hasTable('pengiriman_barang') ? 'pengiriman_barang' : (Schema::hasTable('shipments') ? 'shipments' : null);

        if ($tableName === null) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
            if (Schema::hasColumn($tableName, 'no_gambar')) {
                $table->dropColumn('no_gambar');
            }
        });
    }
};
