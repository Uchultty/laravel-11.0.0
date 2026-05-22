<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JenisBarang;
use App\Models\Barang;

try {
    $jenis = JenisBarang::firstOrCreate(['nama' => 'Material'], ['deskripsi' => 'Jenis barang material']);

    $barang = Barang::create([
        'kode' => 'MAT-TST-' . strtoupper(bin2hex(random_bytes(3))),
        'nama' => 'TST MATERIAL ' . time(),
        'id_jenis_barang' => $jenis->id_jenis_barang,
        'status' => 'material',
        'quantity' => 12,
        'satuan' => 'cm',
        'stok_minimum' => 1,
    ]);

    echo "CREATED:\n";
    echo json_encode($barang->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}
