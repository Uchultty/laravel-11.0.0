<?php

require __DIR__ . '/..//vendor/autoload.php';

$app = require_once __DIR__ . '/..//bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Barang;
use App\Models\Customer;
use App\Models\User;
use App\Models\BarangKeluar;

try {
    $barang = Barang::first();
    $customer = Customer::first();

    if (! $barang) {
        echo "No Barang found in DB. Aborting.\n";
        exit(1);
    }

    // For test: ensure barang has a material_type so snapshot can be verified
    if (empty($barang->material_type)) {
        $barang->update(['material_type' => 'Contoh Material']);
        $barang->refresh();
        echo "Set material_type on sample Barang to: " . ($barang->material_type ?? '(null)') . "\n";
    }

    $user = User::first();

    $payload = [
        'id_barang' => $barang->id_barang,
        'id_customer' => $customer?->id_customer ?? null,
        'quantity' => 1,
        'tanggal_keluar' => now()->format('Y-m-d'),
        'status_pengiriman' => 'Menunggu Pengiriman',
        'material_type' => $barang->material_type ?? null,
        'id_user' => $user?->id_user ?? null,
    ];

    $created = BarangKeluar::create($payload);

    echo "Created BarangKeluar id: " . ($created->id_barang_keluar ?? '(none)') . "\n";
    echo "material_type: " . ($created->material_type ?? '(null)') . "\n";
    echo "status_pengiriman: " . ($created->status_pengiriman ?? '(null)') . "\n";

} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
