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
        Schema::create('barang_dalam_proses', function (Blueprint $table) {
            $table->id();
            $table->string('barang_id', 20);
            $table->unsignedBigInteger('user_id');
            $table->integer('quantity');
            $table->string('barang_mentah');
            $table->timestamps();

            $table->foreign('barang_id')->references('id_barang')->on('barang')->cascadeOnDelete();
            $table->foreign('user_id')->references('id_user')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_dalam_proses');
    }
};
