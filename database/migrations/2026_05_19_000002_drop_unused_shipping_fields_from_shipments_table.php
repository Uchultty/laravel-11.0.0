<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columnsToDrop = [
            'nomor_surat_jalan',
            'alamat_pengiriman',
            'nama_penerima',
            'catatan',
        ];

        $existingColumns = array_values(array_filter(
            $columnsToDrop,
            static fn (string $column): bool => Schema::hasColumn('shipments', $column)
        ));

        if ($existingColumns !== []) {
            Schema::table('shipments', function (Blueprint $table) use ($existingColumns): void {
                $table->dropColumn($existingColumns);
            });
        }
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            if (! Schema::hasColumn('shipments', 'nomor_surat_jalan')) {
                $table->string('nomor_surat_jalan')->nullable()->unique();
            }

            if (! Schema::hasColumn('shipments', 'alamat_pengiriman')) {
                $table->text('alamat_pengiriman')->nullable();
            }

            if (! Schema::hasColumn('shipments', 'nama_penerima')) {
                $table->string('nama_penerima')->nullable();
            }

            if (! Schema::hasColumn('shipments', 'catatan')) {
                $table->text('catatan')->nullable();
            }
        });
    }
};
