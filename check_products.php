<?php
require __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$app = require __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== LEGACY BARANGS ===\n";
$legacy = DB::table('barangs')->get();
foreach($legacy as $row){
    echo "ID: {$row->id_barang}, Nama: {$row->nama}, Status: {$row->status}\n";
}
echo "Total: " . count($legacy) . "\n\n";

echo "=== MODULAR PRODUCTS ===\n";
$modular = DB::table('products')->get();
foreach($modular as $row){
    echo "ID: {$row->id_product}, Nama: {$row->nama}, Status Barang: {$row->status_barang}, Source: {$row->source_barang_id}\n";
}
echo "Total: " . count($modular) . "\n";
