<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Business/table columns that were left at the Postgres default of 255
     * chars. Lengths below are sized from how each field is actually
     * generated/validated in app code (see BarangController, CustomerController,
     * SupplierController, etc.) — not arbitrary, so validation rules were
     * updated to match rather than left free to exceed these caps.
     *
     * Framework tables (cache, jobs, sessions, users, ...) and free-form/path
     * columns (alamat, gambar_path, invoice_path, legacy_*_id, reserve_token)
     * are intentionally left untouched.
     */
    private function columnLengths(): array
    {
        return [
            'barang' => ['kode' => 20, 'nama' => 100, 'status' => 20, 'material_type' => 100, 'ukuran' => 50],
            'jenis_barang' => ['nama' => 50],
            'pelanggan' => ['nama' => 100, 'kontak' => 20, 'email' => 100, 'pic' => 100],
            'suppliers' => ['nama' => 100, 'kontak' => 20, 'email' => 100, 'pic' => 100],
            'materials' => ['kode' => 20, 'nama' => 100, 'material_type' => 100, 'satuan' => 10, 'ukuran' => 50],
            'products' => ['kode' => 20, 'nama' => 100, 'satuan' => 10, 'ukuran' => 50],
            'barang_dalam_proses' => ['ukuran' => 50],
            'barang_keluar' => ['status_pengiriman' => 30, 'material_type' => 100],
            'barang_masuk' => ['status' => 20, 'satuan' => 10],
            'pemesanan_material' => ['status' => 30],
            'pengiriman_barang' => ['status_pengiriman' => 30, 'material_type' => 100],
        ];
    }

    public function up(): void
    {
        foreach ($this->columnLengths() as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column => $length) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" TYPE VARCHAR({$length})");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->columnLengths() as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach (array_keys($columns) as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                DB::statement("ALTER TABLE \"{$table}\" ALTER COLUMN \"{$column}\" TYPE VARCHAR(255)");
            }
        }
    }
};
