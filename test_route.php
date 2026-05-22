<?php

// Test script to verify routes
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Get routes
$routeCollection = $app->make('router')->getRoutes();

foreach ($routeCollection as $route) {
    if (str_contains($route->getName() ?? '', 'pelanggan')) {
        echo $route->getName() . " => " . implode('|', $route->methods) . " " . $route->uri() . "\n";
    }
}
