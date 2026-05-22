<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            if (! Schema::hasColumn('shipments', 'nomor_surat_jalan')) {
                $table->string('nomor_surat_jalan')->nullable()->after('status_pengiriman')->unique();
            }

            if (! Schema::hasColumn('shipments', 'tanggal_surat_jalan')) {
                $table->date('tanggal_surat_jalan')->nullable()->after('nomor_surat_jalan');
            }

            if (! Schema::hasColumn('shipments', 'alamat_pengiriman')) {
                $table->text('alamat_pengiriman')->nullable()->after('tanggal_surat_jalan');
            }

            if (! Schema::hasColumn('shipments', 'nama_penerima')) {
                $table->string('nama_penerima')->nullable()->after('alamat_pengiriman');
            }

            if (! Schema::hasColumn('shipments', 'catatan')) {
                $table->text('catatan')->nullable()->after('nama_penerima');
            }
        });

        if (Schema::hasColumn('shipments', 'nomor_surat_jalan')) {
            DB::statement("UPDATE shipments SET nomor_surat_jalan = COALESCE(nomor_surat_jalan, 'SJ-' || LPAD(id_pengiriman::text, 5, '0')), tanggal_surat_jalan = COALESCE(tanggal_surat_jalan, tanggal_pengiriman), alamat_pengiriman = COALESCE(alamat_pengiriman, (SELECT alamat FROM customers WHERE customers.id_customer = shipments.id_pelanggan)), nama_penerima = COALESCE(nama_penerima, (SELECT nama FROM customers WHERE customers.id_customer = shipments.id_pelanggan)) WHERE nomor_surat_jalan IS NULL OR tanggal_surat_jalan IS NULL OR alamat_pengiriman IS NULL OR nama_penerima IS NULL");
        }
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropUnique(['nomor_surat_jalan']);
            $table->dropColumn([
                'nomor_surat_jalan',
                'tanggal_surat_jalan',
                'alamat_pengiriman',
                'nama_penerima',
                'catatan',
            ]);
        });
    }
};
