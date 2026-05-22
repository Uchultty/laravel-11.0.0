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
        Schema::create('material_orders', function (Blueprint $table) {
            $table->id('id_pemesanan');
            $table->string('legacy_barang_masuk_id')->nullable()->unique();
            $table->foreignId('id_material')->constrained('materials', 'id_material')->restrictOnDelete();
            $table->foreignId('id_supplier')->constrained('suppliers', 'id_supplier')->restrictOnDelete();
            $table->foreignId('id_user')->constrained('users', 'id_user')->restrictOnDelete();
            $table->integer('qty');
            $table->string('satuan', 50)->nullable();
            $table->string('status', 100)->nullable();
            $table->date('tgl_pemesanan');
            $table->date('estimasi_tiba')->nullable();
            $table->string('invoice_path')->nullable();
            $table->string('surat_jalan_path')->nullable();
            $table->string('gambar_path')->nullable();
            $table->timestamps();

            $table->index(['id_material', 'id_supplier']);
            $table->index('tgl_pemesanan');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_orders');
    }
};