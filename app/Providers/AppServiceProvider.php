<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\Product;
use App\Models\Material;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        // Explicit route model binding for data-material and data-produk resources
        Route::model('data_material', Material::class);
        Route::model('data_produk', Product::class);
    }
}
