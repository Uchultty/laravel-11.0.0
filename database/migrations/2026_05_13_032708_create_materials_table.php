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
        Schema::create('materials', function (Blueprint $table) {
            $table->id('id_material');
            $table->string('source_barang_id')->unique();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->unsignedBigInteger('id_jenis_barang')->nullable();
            $table->integer('quantity')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->string('material_type')->nullable();
            $table->string('satuan')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('gambar_path')->nullable();
            $table->timestamps();

            $table->index('id_jenis_barang');
        });

        DB::table('materials')->insertUsing(
            [
                'source_barang_id',
                'kode',
                'nama',
                'id_jenis_barang',
                'quantity',
                'stok_minimum',
                'material_type',
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
                    'stok_minimum',
                    'material_type',
                    'satuan',
                    'ukuran',
                    'gambar_path',
                    'created_at',
                    'updated_at',
                ])
                ->whereRaw("LOWER(COALESCE(status, '')) = 'material'")
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
