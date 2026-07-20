<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Models\ProductionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangDalamProsesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\LaporanStokProdukController;
use App\Http\Controllers\LaporanPengirimanProdukController;
use App\Http\Controllers\UserManagementController;

// Route model binding: resolve legacy barangDalamProses IDs to ProductionItem
Route::bind('barangDalamProses', function ($id) {
    return ProductionItem::query()
        ->where('legacy_barang_proses_id', (string) $id)
        ->orWhere('id_barang_proses', (int) $id)
        ->firstOrFail();
});

Route::get('/', function (): RedirectResponse {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function (): RedirectResponse {
    $user = auth()->user();
    // Single-role app (admin only): any authenticated user is redirected to admin dashboard
    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');

use App\Http\Controllers\DashboardController;

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Read-only operational menus and reports (admin-only removed; app single-role)
    {
        Route::get('pengiriman-produk', [BarangKeluarController::class, 'index'])->name('pengiriman-produk.index');
        Route::get('pengiriman-produk/{pengiriman_produk}', [BarangKeluarController::class, 'show'])
            ->where('pengiriman_produk', '[0-9]+|BKL[0-9]+')
            ->name('pengiriman-produk.show');
        Route::get('pengiriman-produk/{pengiriman_produk}/surat-jalan', [BarangKeluarController::class, 'suratJalan'])
            ->where('pengiriman_produk', '[0-9]+|BKL[0-9]+')
            ->name('pengiriman-produk.surat-jalan');
        Route::get('pengiriman-produk/{pengiriman_produk}/surat-jalan/pdf', [BarangKeluarController::class, 'downloadSuratJalanPdf'])
            ->where('pengiriman_produk', '[0-9]+|BKL[0-9]+')
            ->name('pengiriman-produk.surat-jalan.pdf');
        Route::get('pengiriman-produk/{barangKeluar}/download-file/{type}', [BarangKeluarController::class, 'downloadFile'])
            ->name('pengiriman-produk.download-file');

        Route::get('barang-dalam-proses', [BarangDalamProsesController::class, 'index'])->name('barang-dalam-proses.index');

Route::get('laporan/stok-produk', [LaporanStokProdukController::class, 'index'])->name('laporan-stok-produk.index');
        Route::get('laporan/pengiriman-produk/export-pdf', [LaporanPengirimanProdukController::class, 'exportPdf'])->name('laporan-pengiriman-produk.export-pdf');
        Route::get('laporan/pengiriman-produk', [LaporanPengirimanProdukController::class, 'index'])->name('laporan-pengiriman-produk.index');
    }

    // All write operations and master data (admin-only removed; app single-role)
    {
        Route::resource('barang-masuk', BarangMasukController::class);

        Route::get('pemesanan-material', [BarangMasukController::class, 'persediaanMaterialIndex'])->name('persediaan-material.index');
        Route::get('pemesanan-material/create', [BarangMasukController::class, 'persediaanMaterialCreate'])->name('persediaan-material.create');
        Route::post('pemesanan-material', [BarangMasukController::class, 'persediaanMaterialStore'])->name('persediaan-material.store');
        Route::get('pemesanan-material/{barangMasuk}', [BarangMasukController::class, 'persediaanMaterialShow'])->name('persediaan-material.show');
        Route::get('pemesanan-material/{barangMasuk}/edit', [BarangMasukController::class, 'persediaanMaterialEdit'])->name('persediaan-material.edit');
        Route::put('pemesanan-material/{barangMasuk}', [BarangMasukController::class, 'persediaanMaterialUpdate'])->name('persediaan-material.update');
        Route::delete('pemesanan-material/{barangMasuk}', [BarangMasukController::class, 'persediaanMaterialDestroy'])->name('persediaan-material.destroy');

        Route::resource('pengiriman-produk', BarangKeluarController::class)->except(['index', 'show']);

        Route::get('barang-dalam-proses/create', [BarangDalamProsesController::class, 'create'])->name('barang-dalam-proses.create');
        Route::post('barang-dalam-proses', [BarangDalamProsesController::class, 'storeProses'])->name('barang-dalam-proses.store');
        Route::get('barang-dalam-proses/{barangDalamProses}/edit', [BarangDalamProsesController::class, 'editProses'])->name('barang-dalam-proses.edit');
        Route::put('barang-dalam-proses/{barangDalamProses}', [BarangDalamProsesController::class, 'updateProses'])->name('barang-dalam-proses.update');
        Route::delete('barang-dalam-proses/{barangDalamProses}', [BarangDalamProsesController::class, 'destroyProses'])->name('barang-dalam-proses.destroy');
        // Backward-compatible GET fallback to avoid "GET not supported" errors from old JS/links.
        Route::get('barang-dalam-proses/{barangDalamProses}/status-siap-dikirim', function () {
            return redirect()->route('barang-dalam-proses.index')->with('error', 'Permintaan tidak diperbolehkan via GET. Gunakan tombol pada halaman untuk menandai Siap Dikirim.');
        })->name('barang-dalam-proses.status-siap-dikirim.get');

        Route::patch('barang-dalam-proses/{barangDalamProses}/status-siap-dikirim', [BarangDalamProsesController::class, 'markSiapDikirim'])->name('barang-dalam-proses.status-siap-dikirim');
        Route::get('barang-dalam-proses/{barangDalamProses}/prepare-pengiriman', [BarangDalamProsesController::class, 'preparePengiriman'])->name('barang-dalam-proses.prepare-pengiriman');
        Route::post('barang-dalam-proses/prepare-pengiriman-group', [BarangDalamProsesController::class, 'preparePengirimanGroup'])->name('barang-dalam-proses.prepare-pengiriman-group');
        Route::post('barang-dalam-proses/{barangDalamProses}/cancel-reserve', [BarangDalamProsesController::class, 'cancelReserve'])->name('barang-dalam-proses.cancel-reserve');
        Route::post('barang-dalam-proses/cancel-reserve-group', [BarangDalamProsesController::class, 'cancelReserveGroup'])->name('barang-dalam-proses.cancel-reserve-group');

        Route::get('produk/create', [BarangController::class, 'create'])
            ->name('produk.create')
            ->defaults('form_mode', 'produk');
        Route::get('data-material/create', [BarangController::class, 'create'])
            ->name('data-material.create')
            ->defaults('form_mode', 'material');

        Route::resource('suppliers', SupplierController::class);
        Route::resource('data-material', BarangController::class)
            ->parameters(['data-material' => 'barang'])
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::resource('data-produk', BarangController::class)
            ->parameters(['data-produk' => 'barang'])
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::delete('data-material/{barang}/destroy-barang', [BarangController::class, 'destroy'])->name('data-material.destroy-barang');
        Route::resource('pelanggan', CustomerController::class);
        Route::resource('users', UserManagementController::class);
    }
});

require __DIR__.'/auth.php';
