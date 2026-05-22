<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('jenis_barang')
            ->where('nama', 'ALLOY')
            ->update(['nama' => 'Material']);
    }

    public function down(): void
    {
        DB::table('jenis_barang')
            ->where('nama', 'Material')
            ->update(['nama' => 'ALLOY']);
    }
};
