<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Barang;

$app = require_once 'bootstrap/app.php';
$container = $app->make('Illuminate\Contracts\Container\Container');

$barangs = Barang::where('status', 'material')->limit(5)->get(['id_barang', 'nama', 'panjang', 'lebar', 'tinggi', 'satuan']);

echo "=== Data Material ===\n";
echo count($barangs) . " material(s) found\n\n";

foreach ($barangs as $barang) {
    echo "ID: {$barang->id_barang}\n";
    echo "Nama: {$barang->nama}\n";
    echo "Panjang: {$barang->panjang}\n";
    echo "Lebar: {$barang->lebar}\n";
    echo "Tinggi: {$barang->tinggi}\n";
    echo "Satuan: {$barang->satuan}\n";
    echo "---\n";
}
?>
