<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create test data
echo "Creating test BarangKeluar...\n";

$newId = \App\Models\BarangKeluar::query()->orderByDesc('id_barang_keluar')->value('id_barang_keluar');
$nextNum = 1;
if ($newId && preg_match('/^BKL(\d+)$/', $newId, $m)) {
    $nextNum = ((int)$m[1]) + 1;
}
$newId = 'BKL' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
$item = \App\Models\BarangKeluar::create([
    'id_barang_keluar' => $newId,
    'id_barang' => 'BRG00001',  // Assuming this exists
    'id_customer' => 1,  // Assuming this exists
    'id_user' => 1,  // Assuming this exists
    'quantity' => 5,
    'tanggal_keluar' => now()->toDateString(),
]);

echo "Created: " . $item->id_barang_keluar . "\n";

// Now check it exists
$check1 = \App\Models\BarangKeluar::find($item->id_barang_keluar);
echo "Before delete - exists: " . ($check1 ? 'YES' : 'NO') . "\n";

// Try to delete
$result = $item->delete();
echo "Delete returned: " . ($result ? 'TRUE' : 'FALSE') . "\n";

// Check if still exists
$check2 = \App\Models\BarangKeluar::find($item->id_barang_keluar);
echo "After delete - exists: " . ($check2 ? 'YES' : 'NO') . "\n";
