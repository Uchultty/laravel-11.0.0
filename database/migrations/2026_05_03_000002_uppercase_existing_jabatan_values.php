<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('suppliers')
            ->whereNotNull('jabatan')
            ->update(['jabatan' => DB::raw('UPPER(jabatan)')]);

        DB::table('customers')
            ->whereNotNull('jabatan')
            ->update(['jabatan' => DB::raw('UPPER(jabatan)')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reliable way to restore the original mixed-case values.
    }
};
