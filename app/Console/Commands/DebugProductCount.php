<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugProductCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-product-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug products count mismatch';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->line("=== LEGACY BARANGS ===");
        $legacy = DB::table('barangs')->get();
        foreach($legacy as $row){
            $this->line("ID: {$row->id_barang}, Nama: {$row->nama}, Status: {$row->status}");
        }
        $this->line("Total: " . count($legacy));

        $this->line("\n=== MODULAR PRODUCTS ===");
        $modular = DB::table('products')->get();
        foreach($modular as $row){
            $this->line("ID: {$row->id_product}, Nama: {$row->nama}, Source: {$row->source_barang_id}");
        }
        $this->line("Total: " . count($modular));

        $this->line("\n=== BY STATUS ===");
        $byStatus = DB::table('barangs')->groupBy('status')->selectRaw('status, COUNT(*) as count')->get();
        foreach($byStatus as $row){
            $this->line("Status {$row->status}: {$row->count}");
        }
    }
}
