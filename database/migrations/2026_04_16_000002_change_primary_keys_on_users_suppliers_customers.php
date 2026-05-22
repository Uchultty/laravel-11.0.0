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
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_supplier_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_user_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuks_supplier_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuks_user_id_foreign');

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id', 'id_user');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->renameColumn('id', 'id_supplier');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('id', 'id_customer');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('supplier_id')->references('id_supplier')->on('suppliers')->onDelete('cascade');
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_supplier_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuk_user_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuks_supplier_id_foreign');
        DB::statement('ALTER TABLE barang_masuk DROP CONSTRAINT IF EXISTS barang_masuks_user_id_foreign');

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('id_customer', 'id');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->renameColumn('id_supplier', 'id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('id_user', 'id');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
