<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangDalamProses;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Material;
use App\Models\MaterialOrder;
use App\Models\ProductionItem;
use App\Models\Shipment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        $totalMaterials = Material::count();

        $materialLowStock = Material::whereColumn('quantity', '<=', 'stok_minimum')->count();

        $deadlineAlerts = ProductionItem::with(['produk', 'pelanggan'])
            ->where('status_kirim', false)
            ->whereNotNull('tgl_selesai')
            ->whereDate('tgl_selesai', '<', today())
            ->orderBy('tgl_selesai')
            ->limit(5)
            ->get();

        $deadlineAlertsCount = ProductionItem::where('status_kirim', false)
            ->whereNotNull('tgl_selesai')
            ->whereDate('tgl_selesai', '<', today())
            ->count();

        $barangDalamProsesCount = ProductionItem::where('status_kirim', false)->count();

        $barangDalamProsesLatest = ProductionItem::with(['produk', 'pelanggan'])
            ->where('status_kirim', false)
            ->latest()
            ->limit(5)
            ->get();

        $pengirimanTerkirim = Shipment::where('status_pengiriman', 'Sedang Dikirim')->count();

        $pengirimanSelesai = Shipment::where('status_pengiriman', 'Selesai')->count();

        $pengirimanActive = Shipment::with(['barang', 'customer'])
            ->whereIn('status_pengiriman', ['Siap Dikirim', 'Sedang Dikirim'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        $activities = collect();

        // Material orders
        $activities = $activities->merge(
            MaterialOrder::with(['material', 'user'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($m) {
                    return [
                        'time' => $m->created_at,
                        'text' => 'Material ' . optional($m->material)->nama . ' dipesan (qty: ' . $m->qty . ')',
                    ];
                })
        );

        // Production items
        $activities = $activities->merge(
            ProductionItem::with('produk')
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get()
                ->map(function ($p) {
                    $namaBarang = optional($p->produk)->nama ?? 'Produk';
                    $statusText = $p->status_kirim ? 'siap kirim' : 'dalam proses';

                    return [
                        'time' => $p->updated_at,
                        'text' => $namaBarang . ' - ' . $statusText,
                    ];
                })
        );

        // Shipments
        $activities = $activities->merge(
            Shipment::with('barang')
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get()
                ->map(function ($k) {
                    $namaBarang = optional($k->barang)->nama ?? 'Produk';

                    return [
                        'time' => $k->updated_at,
                        'text' => $namaBarang . ' - pengiriman: ' . $k->status_pengiriman,
                    ];
                })
        );

        $activities = $activities
            ->sortByDesc('time')
            ->values()
            ->take(10);

        return view('admin.dashboard', compact(
            'totalMaterials',
            'materialLowStock',
            'deadlineAlerts',
            'deadlineAlertsCount',
            'barangDalamProsesCount',
            'barangDalamProsesLatest',
            'pengirimanTerkirim',
            'pengirimanSelesai',
            'pengirimanActive',
            'activities'
        ));
    }
}
