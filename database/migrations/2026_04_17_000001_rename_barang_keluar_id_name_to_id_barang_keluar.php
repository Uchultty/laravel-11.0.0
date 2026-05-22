<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('barang_keluar') &&
            Schema::hasColumn('barang_keluar', 'id_name') &&
            ! Schema::hasColumn('barang_keluar', 'id_barang_keluar')
        ) {
            $driver = DB::getDriverName();

            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE barang_keluar RENAME COLUMN id_name TO id_barang_keluar');
            } else {
                DB::statement('ALTER TABLE barang_keluar CHANGE id_name id_barang_keluar VARCHAR(20) NOT NULL');
            }
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('barang_keluar') &&
            Schema::hasColumn('barang_keluar', 'id_barang_keluar') &&
            ! Schema::hasColumn('barang_keluar', 'id_name')
        ) {
            $driver = DB::getDriverName();

            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE barang_keluar RENAME COLUMN id_barang_keluar TO id_name');
            } else {
                DB::statement('ALTER TABLE barang_keluar CHANGE id_barang_keluar id_name VARCHAR(20) NOT NULL');
            }
        }
    }
};