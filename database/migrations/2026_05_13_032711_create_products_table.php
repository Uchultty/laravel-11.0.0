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
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product');
            $table->string('source_barang_id')->unique();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->unsignedBigInteger('id_jenis_barang')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('panjang', 12, 2)->nullable();
            $table->decimal('lebar', 12, 2)->nullable();
            $table->decimal('tinggi', 12, 2)->nullable();
            $table->string('satuan')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('gambar_path')->nullable();
            $table->timestamps();

            $table->index('id_jenis_barang');
        });

        DB::table('products')->insertUsing(
            [
                'source_barang_id',
                'kode',
                'nama',
                'id_jenis_barang',
                'quantity',
                'panjang',
                'lebar',
                'tinggi',
                'satuan',
                'ukuran',
                'gambar_path',
                'created_at',
                'updated_at',
            ],
            DB::table('barang')
                ->select([
                    'id_barang as source_barang_id',
                    'kode',
                    'nama',
                    'id_jenis_barang',
                    'quantity',
                    'panjang',
                    'lebar',
                    'tinggi',
                    'satuan',
                    'ukuran',
                    'gambar_path',
                    'created_at',
                    'updated_at',
                ])
                ->whereRaw("LOWER(COALESCE(status, 'produk')) != 'material'")
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
