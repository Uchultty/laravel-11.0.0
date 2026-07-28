<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Renames the modular production table back to `pemesanan_produk`
     * (reverting the `barang_dalam_proses` name from 2026_05_19_000006).
     */
    public function up(): void
    {
        if (! Schema::hasTable('barang_dalam_proses') || Schema::hasTable('pemesanan_produk')) {
            return;
        }

        $this->dropForeignKeysReferencing('barang_keluar', 'barang_dalam_proses');
        $this->dropForeignKeysReferencing('pengiriman_barang', 'barang_dalam_proses');

        Schema::rename('barang_dalam_proses', 'pemesanan_produk');

        if (Schema::hasColumn('barang_keluar', 'id_pemesanan_produk')) {
            Schema::table('barang_keluar', function (Blueprint $table) {
                $table->foreign('id_pemesanan_produk')
                    ->references('id_pemesanan_produk')
                    ->on('pemesanan_produk')
                    ->nullOnDelete();
            });
        }

        Schema::table('pengiriman_barang', function (Blueprint $table) {
            $table->foreign('id_pemesanan_produk')
                ->references('id_pemesanan_produk')
                ->on('pemesanan_produk')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pemesanan_produk') || Schema::hasTable('barang_dalam_proses')) {
            return;
        }

        $this->dropForeignKeysReferencing('barang_keluar', 'pemesanan_produk');
        $this->dropForeignKeysReferencing('pengiriman_barang', 'pemesanan_produk');

        Schema::rename('pemesanan_produk', 'barang_dalam_proses');

        if (Schema::hasColumn('barang_keluar', 'id_pemesanan_produk')) {
            Schema::table('barang_keluar', function (Blueprint $table) {
                $table->foreign('id_pemesanan_produk')
                    ->references('id_pemesanan_produk')
                    ->on('barang_dalam_proses')
                    ->nullOnDelete();
            });
        }

        Schema::table('pengiriman_barang', function (Blueprint $table) {
            $table->foreign('id_pemesanan_produk')
                ->references('id_pemesanan_produk')
                ->on('barang_dalam_proses')
                ->restrictOnDelete();
        });
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
