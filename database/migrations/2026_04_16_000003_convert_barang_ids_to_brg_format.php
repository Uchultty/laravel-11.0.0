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
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_barang_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuks_barang_id_foreign');

        DB::statement("ALTER TABLE barang ALTER COLUMN id_barang TYPE VARCHAR(20) USING 'BRG' || LPAD(id_barang::text, 5, '0')");
        DB::statement("ALTER TABLE barang_masuk ALTER COLUMN id_barang_masuk TYPE VARCHAR(20) USING 'BRG' || LPAD(id_barang_masuk::text, 5, '0')");
        DB::statement("ALTER TABLE barang_masuk ALTER COLUMN barang_id TYPE VARCHAR(20) USING 'BRG' || LPAD(barang_id::text, 5, '0')");

        DB::statement('ALTER TABLE barang_masuk ADD CONSTRAINT barang_masuk_barang_id_foreign FOREIGN KEY (barang_id) REFERENCES barang(id_barang) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_barang_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuks_barang_id_foreign');

        DB::statement("ALTER TABLE barang_masuk ALTER COLUMN barang_id TYPE BIGINT USING REGEXP_REPLACE(barang_id, '^BRG', '')::BIGINT");
        DB::statement("ALTER TABLE barang_masuk ALTER COLUMN id_barang_masuk TYPE BIGINT USING REGEXP_REPLACE(id_barang_masuk, '^BRG', '')::BIGINT");
        DB::statement("ALTER TABLE barang ALTER COLUMN id_barang TYPE BIGINT USING REGEXP_REPLACE(id_barang, '^BRG', '')::BIGINT");

        DB::statement('ALTER TABLE barang_masuk ADD CONSTRAINT barang_masuk_barang_id_foreign FOREIGN KEY (barang_id) REFERENCES barang(id_barang) ON DELETE CASCADE');
    }
};
