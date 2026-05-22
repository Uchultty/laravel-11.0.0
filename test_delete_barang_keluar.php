<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test delete
$barangKeluar = \App\Models\BarangKeluar::first();

if (!$barangKeluar) {
    echo "No BarangKeluar found\n";
    exit(1);
}

echo "Found: " . $barangKeluar->id_barang_keluar . "\n";

try {
    $result = $barangKeluar->delete();
    echo "Delete result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Check if still exists
    $check = \App\Models\BarangKeluar::find($barangKeluar->id_barang_keluar);
    if ($check) {
        echo "ERROR: Record still exists after delete!\n";
    } else {
        echo "Record successfully deleted from database\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
