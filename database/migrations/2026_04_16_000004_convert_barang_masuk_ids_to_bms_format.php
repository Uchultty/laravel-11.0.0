<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE barang_masuk SET id_barang_masuk = REGEXP_REPLACE(id_barang_masuk, '^BRG', 'BMS')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE barang_masuk SET id_barang_masuk = REGEXP_REPLACE(id_barang_masuk, '^BMS', 'BRG')");
    }
};
