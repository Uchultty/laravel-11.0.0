<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id('id_pengiriman');
            $table->string('legacy_barang_keluar_id')->nullable()->unique();
            $table->foreignId('id_pemesanan_produk')->constrained('pemesanan_produk', 'id_pemesanan_produk')->restrictOnDelete();
            $table->foreignId('id_produk')->constrained('products', 'id_product')->restrictOnDelete();
            $table->foreignId('id_pelanggan')->nullable()->constrained('customers', 'id_customer')->nullOnDelete();
            $table->foreignId('id_user')->constrained('users', 'id_user')->restrictOnDelete();
            $table->integer('qty');
            $table->date('tanggal_pengiriman');
            $table->string('status_pengiriman', 100)->default('Menunggu Pengiriman');
            $table->string('material_type')->nullable();
            $table->string('invoice_path')->nullable();
            $table->string('surat_jalan_path')->nullable();
            $table->string('gambar_path')->nullable();
            $table->timestamps();

            $table->index(['id_pemesanan_produk', 'id_produk']);
            $table->index('tanggal_pengiriman');
            $table->index('status_pengiriman');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};