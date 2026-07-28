<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Repairs databases where 2026_05_19_000006 no-op'd because the legacy
     * `barang_dalam_proses` table (id_barang, tanggal_selesai, ...) was still
     * present, leaving the modular table stuck under the name `pemesanan_produk`
     * while later migrations (no_po/no_gambar) were applied to the legacy table.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pemesanan_produk') || ! Schema::hasTable('barang_dalam_proses')) {
            return;
        }

        if (Schema::hasColumn('barang_dalam_proses', 'qty')) {
            // Already the modular table under this name; nothing to fix.
            return;
        }

        $this->dropForeignKeysReferencing('barang_keluar', 'barang_dalam_proses');

        Schema::dropIfExists('barang_dalam_proses');

        if (! Schema::hasColumn('pemesanan_produk', 'no_po')) {
            Schema::table('pemesanan_produk', function (Blueprint $table) {
                $table->string('no_po', 100)->nullable()->after('id_pelanggan');
            });
        }

        if (! Schema::hasColumn('pemesanan_produk', 'no_gambar')) {
            Schema::table('pemesanan_produk', function (Blueprint $table) {
                $table->string('no_gambar', 100)->nullable()->after('id_produk');
            });
        }

        Schema::rename('pemesanan_produk', 'barang_dalam_proses');

        if (Schema::hasColumn('barang_keluar', 'id_pemesanan_produk')) {
            Schema::table('barang_keluar', function (Blueprint $table) {
                $table->foreign('id_pemesanan_produk')
                    ->references('id_pemesanan_produk')
                    ->on('barang_dalam_proses')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible: the legacy barang_dalam_proses schema this replaces
        // no longer exists once dropped.
    }

    private function dropForeignKeysReferencing(string $table, string $referencedTable): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $constraints = DB::select('
            SELECT tc.constraint_name
            FROM information_schema.table_constraints tc
            JOIN information_schema.constraint_column_usage ccu
                ON ccu.constraint_name = tc.constraint_name AND ccu.table_schema = tc.table_schema
            WHERE tc.table_name = ?
            AND tc.constraint_type = \'FOREIGN KEY\'
            AND tc.table_schema = \'public\'
            AND ccu.table_name = ?
        ', [$table, $referencedTable]);

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$constraint->constraint_name}");
        }
    }
};
