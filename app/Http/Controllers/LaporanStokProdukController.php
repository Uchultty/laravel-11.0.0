<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class LaporanStokProdukController extends Controller
{
    public function index(): View
    {
        $query = Product::query()
            ->with(['jenisBarang:id_jenis_barang,nama'])
            ->select('products.*')
            ->latest('created_at');

        $summaryTotalItem = (clone $query)->count();

        $barangs = $query->paginate(10)->withQueryString();

        $barangs->getCollection()->transform(function ($barang) {
            $barang->total_masuk = 0;
            $barang->total_keluar = 0;
            $barang->current_quantity = 0;

            return $barang;
        });

        return view('laporan.stok-produk.index', [
            'barangs' => $barangs,
            'summaryTotalItem' => $summaryTotalItem,
        ]);
    }
}
