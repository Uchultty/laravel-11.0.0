<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('pemesanan_produk')) {
            return;
        }

        if (Schema::hasTable('barang_dalam_proses')) {
            if (Schema::hasColumn('barang_dalam_proses', 'qty')) {
                // Already the modular table under this name; nothing to do.
                return;
            }

            // The pre-refactor `barang_dalam_proses` table is still occupying this
            // name, which is why this migration used to no-op and leave the new
            // modular `pemesanan_produk` table stuck under its temporary name.
            $this->dropForeignKeysReferencing('barang_keluar', 'barang_dalam_proses');

            Schema::dropIfExists('barang_dalam_proses');
        }

        // First, drop the existing foreign key constraint referencing pemesanan_produk
        // We need to get the actual constraint name from the database
        $constraints = DB::select("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'pengiriman_barang'
            AND constraint_type = 'FOREIGN KEY'
            AND table_schema = 'public'
        ");

        foreach ($constraints as $constraint) {
            if (strpos($constraint->constraint_name, 'id_pemesanan_produk') !== false) {
                DB::statement("ALTER TABLE pengiriman_barang DROP CONSTRAINT {$constraint->constraint_name}");
            }
        }

        // Rename the table
        Schema::rename('pemesanan_produk', 'barang_dalam_proses');

        // Add new foreign key constraint referencing the renamed table
        Schema::table('pengiriman_barang', function (Blueprint $table) {
            $table->foreign('id_pemesanan_produk')
                ->references('id_pemesanan_produk')
                ->on('barang_dalam_proses')
                ->restrictOnDelete();
        });

        if (Schema::hasColumn('barang_keluar', 'id_pemesanan_produk')) {
            Schema::table('barang_keluar', function (Blueprint $table) {
                $table->foreign('id_pemesanan_produk')
                    ->references('id_pemesanan_produk')
                    ->on('barang_dalam_proses')
                    ->nullOnDelete();
            });
        }
    }

    private function dropForeignKeysReferencing(string $table, string $referencedTable): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $constraints = DB::select("
            SELECT tc.constraint_name
            FROM information_schema.table_constraints tc
            JOIN information_schema.constraint_column_usage ccu
                ON ccu.constraint_name = tc.constraint_name AND ccu.table_schema = tc.table_schema
            WHERE tc.table_name = ?
            AND tc.constraint_type = 'FOREIGN KEY'
            AND tc.table_schema = 'public'
            AND ccu.table_name = ?
        ", [$table, $referencedTable]);

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$constraint->constraint_name}");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('barang_dalam_proses') || Schema::hasTable('pemesanan_produk')) {
            return;
        }

        // Drop foreign key constraint
        $constraints = DB::select("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'pengiriman_barang'
            AND constraint_type = 'FOREIGN KEY'
            AND table_schema = 'public'
        ");

        foreach ($constraints as $constraint) {
            if (strpos($constraint->constraint_name, 'id_pemesanan_produk') !== false) {
                DB::statement("ALTER TABLE pengiriman_barang DROP CONSTRAINT {$constraint->constraint_name}");
            }
        }

        // Rename table back
        Schema::rename('barang_dalam_proses', 'pemesanan_produk');

        // Add old foreign key constraint
        Schema::table('pengiriman_barang', function (Blueprint $table) {
            $table->foreign('id_pemesanan_produk')
                ->references('id_pemesanan_produk')
                ->on('pemesanan_produk')
                ->restrictOnDelete();
        });
    }
};
