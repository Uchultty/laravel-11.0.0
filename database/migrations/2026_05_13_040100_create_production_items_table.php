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
        Schema::create('pemesanan_produk', function (Blueprint $table) {
            $table->id('id_pemesanan_produk');
            $table->string('legacy_barang_proses_id')->nullable()->unique();
            $table->foreignId('id_produk')->constrained('products', 'id_product')->restrictOnDelete();
            $table->foreignId('id_material')->constrained('materials', 'id_material')->restrictOnDelete();
            $table->foreignId('id_pelanggan')->nullable()->constrained('customers', 'id_customer')->nullOnDelete();
            $table->foreignId('id_user')->constrained('users', 'id_user')->restrictOnDelete();
            $table->integer('qty');
            $table->string('satuan', 50)->nullable();
            $table->string('ukuran')->nullable();
            $table->date('tgl_dibuat');
            $table->date('tgl_selesai')->nullable();
            $table->boolean('status_kirim')->default(false);
            $table->boolean('processing')->default(false);
            $table->string('reserve_token')->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->foreignId('processing_by')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->timestamps();

            $table->index(['id_produk', 'id_material']);
            $table->index('id_pelanggan');
            $table->index('status_kirim');
            $table->index('processing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan_produk');
    }
};