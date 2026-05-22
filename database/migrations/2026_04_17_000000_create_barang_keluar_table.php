<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->string('id_barang_keluar', 20)->primary();
            $table->string('barang_id', 20);
            $table->foreignId('customer_id')->constrained('customers', 'id_customer')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users', 'id_user')->cascadeOnDelete();
            $table->integer('quantity');
            $table->date('tanggal_keluar');
            $table->string('gambar_path')->nullable();
            $table->string('surat_jalan_path')->nullable();
            $table->string('invoice_path')->nullable();
            $table->timestamps();

            $table->foreign('barang_id')->references('id_barang')->on('barang')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_keluar');
    }
};