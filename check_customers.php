<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$customers = \App\Models\Customer::limit(10)->get(['id_pelanggan', 'nama']);

foreach ($customers as $customer) {
    echo $customer->id_pelanggan . ': ' . $customer->nama . PHP_EOL;
}
