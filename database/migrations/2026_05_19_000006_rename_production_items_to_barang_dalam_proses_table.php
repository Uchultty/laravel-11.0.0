<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, drop the existing foreign key constraint referencing production_items
        // We need to get the actual constraint name from the database
        $constraints = DB::select("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'pengiriman_barang'
            AND constraint_type = 'FOREIGN KEY'
            AND table_schema = 'public'
        ");

        foreach ($constraints as $constraint) {
            if (strpos($constraint->constraint_name, 'id_barang_proses') !== false) {
                DB::statement("ALTER TABLE pengiriman_barang DROP CONSTRAINT {$constraint->constraint_name}");
            }
        }

        // Rename the table
        Schema::rename('production_items', 'barang_dalam_proses');

        // Add new foreign key constraint referencing the renamed table
        Schema::table('pengiriman_barang', function (Blueprint $table) {
            $table->foreign('id_barang_proses')
                ->references('id_barang_proses')
                ->on('barang_dalam_proses')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint
        $constraints = DB::select("
            SELECT constraint_name
            FROM information_schema.table_constraints
            WHERE table_name = 'pengiriman_barang'
            AND constraint_type = 'FOREIGN KEY'
            AND table_schema = 'public'
        ");

        foreach ($constraints as $constraint) {
            if (strpos($constraint->constraint_name, 'id_barang_proses') !== false) {
                DB::statement("ALTER TABLE pengiriman_barang DROP CONSTRAINT {$constraint->constraint_name}");
            }
        }

        // Rename table back
        Schema::rename('barang_dalam_proses', 'production_items');

        // Add old foreign key constraint
        Schema::table('pengiriman_barang', function (Blueprint $table) {
            $table->foreign('id_barang_proses')
                ->references('id_barang_proses')
                ->on('production_items')
                ->restrictOnDelete();
        });
    }
};
