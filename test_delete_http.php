<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Delete all existing logs
$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    file_put_contents($logPath, '');
}

// Create test BarangKeluar
$lastId = \App\Models\BarangKeluar::query()->orderByDesc('id_barang_keluar')->value('id_barang_keluar');
$nextNum = 1;
if ($lastId && preg_match('/^BKL(\d+)$/', $lastId, $m)) {
    $nextNum = ((int)$m[1]) + 1;
}
$newId = 'BKL' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);

$item = \App\Models\BarangKeluar::create([
    'id_barang_keluar' => $newId,
    'id_barang' => 'BRG00001',
    'id_customer' => 1,
    'id_user' => 1,
    'quantity' => 5,
    'tanggal_keluar' => now()->toDateString(),
]);

echo "Created: " . $item->id_barang_keluar . "\n\n";

// Now simulate a POST request with _method=DELETE to the destroy endpoint
echo "Simulating DELETE request to: /pengiriman-produk/{$item->id_barang_keluar}\n";

// Use Laravel's test client
$response = \Illuminate\Support\Facades\Http::asForm()
    ->acceptJson()
    ->post(
        "http://localhost:8000/pengiriman-produk/{$item->id_barang_keluar}",
        ['_method' => 'DELETE', '_token' => csrf_token()]
    );

echo "Response status: " . $response->status() . "\n";
echo "Response body: " . $response->body() . "\n\n";

// Check if still exists
sleep(1);
$check = \App\Models\BarangKeluar::find($item->id_barang_keluar);
echo "Record exists after delete: " . ($check ? 'YES' : 'NO') . "\n";

// Show logs
echo "\n=== LOGS ===\n";
if (file_exists($logPath)) {
    $contents = file_get_contents($logPath);
    echo $contents ?: "(empty)\n";
}
