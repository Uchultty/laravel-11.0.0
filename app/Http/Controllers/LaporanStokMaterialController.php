<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanStokMaterialController extends Controller
{
    public function index(Request $request): View
    {
        $stokMinimumBatas = 10;
        $search = trim((string) $request->query('q', ''));

        // Query dasar laporan stok material.
        $query = Material::query()
            ->with(['jenisBarang:id_jenis_barang,nama'])
            ->select('materials.*')
            ->latest('created_at');

        if ($search !== '') {
            // Pencarian dibuat uppercase supaya hasilnya konsisten dengan input filter.
            $searchLike = '%' . mb_strtoupper($search) . '%';

            $query->whereRaw('UPPER(nama) LIKE ?', [$searchLike]);
        }

        // Ringkasan card selalu mengikuti hasil filter yang sedang aktif.
        $summaryTotalItem = (clone $query)->count();
        $summaryTotalStok = (int) (clone $query)->sum('quantity');
        $summaryStokMenipis = (clone $query)
            ->whereColumn('quantity', '<=', 'stok_minimum')
            ->count();

        $barangs = $query->get();

        return view('laporan.stok-material.index', [
            'barangs' => $barangs,
            'summaryTotalItem' => $summaryTotalItem,
            'summaryTotalStok' => $summaryTotalStok,
            'summaryStokMenipis' => $summaryStokMenipis,
            'stokMinimumBatas' => $stokMinimumBatas,
            'search' => $search,
        ]);
    }
}
