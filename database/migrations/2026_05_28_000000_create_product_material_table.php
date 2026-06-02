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
        Schema::create('product_material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_product');
            $table->unsignedBigInteger('id_material');
            $table->decimal('quantity', 12, 3)->default(1);
            $table->timestamps();

            $table->foreign('id_product')->references('id_product')->on('products')->cascadeOnDelete();
            $table->foreign('id_material')->references('id_material')->on('materials')->cascadeOnDelete();
            $table->unique(['id_product', 'id_material']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_material');
    }
};
