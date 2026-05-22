<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->boolean('processing')->default(false)->after('status_kirim');
            $table->string('reserve_token')->nullable()->after('processing');
            $table->timestamp('processing_started_at')->nullable()->after('reserve_token');
            $table->unsignedBigInteger('processing_by')->nullable()->after('processing_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('barang_dalam_proses', function (Blueprint $table) {
            $table->dropColumn(['processing', 'reserve_token', 'processing_started_at', 'processing_by']);
        });
    }
};
