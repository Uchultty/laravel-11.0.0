<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('shipments', 'tanggal_surat_jalan')) {
            Schema::table('shipments', function (Blueprint $table): void {
                $table->dropColumn('tanggal_surat_jalan');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('shipments', 'tanggal_surat_jalan')) {
            Schema::table('shipments', function (Blueprint $table): void {
                $table->date('tanggal_surat_jalan')->nullable();
            });
        }
    }
};
