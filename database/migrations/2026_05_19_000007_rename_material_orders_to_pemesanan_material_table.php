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
        // Rename the table
        Schema::rename('material_orders', 'pemesanan_material');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename table back
        Schema::rename('pemesanan_material', 'material_orders');
    }
};
