<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE barang DROP CONSTRAINT IF EXISTS barang_jenis_barang_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_barang_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_jenis_barang_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_supplier_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_user_id_foreign');
        DB::statement('ALTER TABLE barang_keluar DROP CONSTRAINT IF EXISTS barang_keluar_barang_id_foreign');
        DB::statement('ALTER TABLE barang_keluar DROP CONSTRAINT IF EXISTS barang_keluar_customer_id_foreign');
        DB::statement('ALTER TABLE barang_keluar DROP CONSTRAINT IF EXISTS barang_keluar_user_id_foreign');
        DB::statement('ALTER TABLE barang_dalam_proses DROP CONSTRAINT IF EXISTS barang_dalam_proses_barang_id_foreign');
        DB::statement('ALTER TABLE barang_dalam_proses DROP CONSTRAINT IF EXISTS barang_dalam_proses_user_id_foreign');

        Schema::table('barang', function (Blueprint $table) {
            $table->renameColumn('jenis_barang_id', 'id_jenis_barang');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->renameColumn('barang_id', 'id_barang');
            $table->renameColumn('jenis_barang_id', 'id_jenis_barang');
            $table->renameColumn('supplier_id', 'id_supplier');
            $table->renameColumn('user_id', 'id_user');
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->renameColumn('barang_id', 'id_barang');
            $table->renameColumn('customer_id', 'id_customer');
            $table->renameColumn('user_id', 'id_user');
        });

        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->renameColumn('barang_id', 'id_barang');
            $table->renameColumn('user_id', 'id_user');
        });

        Schema::table('barang', function (Blueprint $table) {
            $table->foreign('id_jenis_barang')->references('id_jenis_barang')->on('jenis_barang')->nullOnDelete();
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('id_barang')->references('id_barang')->on('barang')->cascadeOnDelete();
            $table->foreign('id_jenis_barang')->references('id_jenis_barang')->on('jenis_barang')->nullOnDelete();
            $table->foreign('id_supplier')->references('id_supplier')->on('suppliers')->cascadeOnDelete();
            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->foreign('id_barang')->references('id_barang')->on('barang')->cascadeOnDelete();
            $table->foreign('id_customer')->references('id_customer')->on('customers')->cascadeOnDelete();
            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });

        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->foreign('id_barang')->references('id_barang')->on('barang')->cascadeOnDelete();
            $table->foreign('id_user')->references('id_user')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE barang DROP CONSTRAINT IF EXISTS barang_id_jenis_barang_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_id_barang_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_id_jenis_barang_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_id_supplier_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_id_user_foreign');
        DB::statement('ALTER TABLE barang_keluar DROP CONSTRAINT IF EXISTS barang_keluar_id_barang_foreign');
        DB::statement('ALTER TABLE barang_keluar DROP CONSTRAINT IF EXISTS barang_keluar_id_customer_foreign');
        DB::statement('ALTER TABLE barang_keluar DROP CONSTRAINT IF EXISTS barang_keluar_id_user_foreign');
        DB::statement('ALTER TABLE barang_dalam_proses DROP CONSTRAINT IF EXISTS barang_dalam_proses_id_barang_foreign');
        DB::statement('ALTER TABLE barang_dalam_proses DROP CONSTRAINT IF EXISTS barang_dalam_proses_id_user_foreign');

        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->renameColumn('id_barang', 'barang_id');
            $table->renameColumn('id_user', 'user_id');
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->renameColumn('id_barang', 'barang_id');
            $table->renameColumn('id_customer', 'customer_id');
            $table->renameColumn('id_user', 'user_id');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->renameColumn('id_barang', 'barang_id');
            $table->renameColumn('id_jenis_barang', 'jenis_barang_id');
            $table->renameColumn('id_supplier', 'supplier_id');
            $table->renameColumn('id_user', 'user_id');
        });

        Schema::table('barang', function (Blueprint $table) {
            $table->renameColumn('id_jenis_barang', 'jenis_barang_id');
        });

        Schema::table('barang', function (Blueprint $table) {
            $table->foreign('jenis_barang_id')->references('id_jenis_barang')->on('jenis_barang')->nullOnDelete();
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id_barang')->on('barang')->cascadeOnDelete();
            $table->foreign('jenis_barang_id')->references('id_jenis_barang')->on('jenis_barang')->nullOnDelete();
            $table->foreign('supplier_id')->references('id_supplier')->on('suppliers')->cascadeOnDelete();
            $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id_barang')->on('barang')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id_customer')->on('customers')->cascadeOnDelete();
            $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
        });

        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->foreign('barang_id')->references('id_barang')->on('barang')->cascadeOnDelete();
            $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
        });
    }
};
