<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\View\View;

class LaporanStokMaterialController extends Controller
{
    public function index(): View
    {
        $stokMinimumBatas = 10;

        $query = Material::query()
            ->with(['jenisBarang:id_jenis_barang,nama'])
            ->select('materials.*')
            ->latest('created_at');

        $summaryTotalItem = (clone $query)->count();
        $summaryTotalStok = (int) (clone $query)->sum('quantity');
        $summaryStokMenipis = (clone $query)
            ->whereColumn('quantity', '<=', 'stok_minimum')
            ->count();

        $barangs = $query->paginate(10)->withQueryString();

        return view('laporan.stok-material.index', [
            'barangs' => $barangs,
            'summaryTotalItem' => $summaryTotalItem,
            'summaryTotalStok' => $summaryTotalStok,
            'summaryStokMenipis' => $summaryStokMenipis,
            'stokMinimumBatas' => $stokMinimumBatas,
        ]);
    }
}
