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
        Schema::table('pemesanan_material', function (Blueprint $table) {
            $table->string('no_po', 100)->nullable()->after('status');
            $table->unique('no_po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan_material', function (Blueprint $table) {
            $table->dropUnique(['no_po']);
            $table->dropColumn('no_po');
        });
    }
};
