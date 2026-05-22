<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            if (Schema::hasTable('customers') && ! Schema::hasTable('pelanggan')) {
                Schema::rename('customers', 'pelanggan');
            }

            if (Schema::hasTable('pelanggan') && Schema::hasColumn('pelanggan', 'id_customer') && ! Schema::hasColumn('pelanggan', 'id_pelanggan')) {
                Schema::table('pelanggan', function (Blueprint $table): void {
                    $table->renameColumn('id_customer', 'id_pelanggan');
                });
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            if (Schema::hasTable('pelanggan') && ! Schema::hasTable('customers')) {
                Schema::rename('pelanggan', 'customers');
            }

            if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'id_pelanggan') && ! Schema::hasColumn('customers', 'id_customer')) {
                Schema::table('customers', function (Blueprint $table): void {
                    $table->renameColumn('id_pelanggan', 'id_customer');
                });
            }
        });
    }
};
