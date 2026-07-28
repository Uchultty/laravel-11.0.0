<?php
# This controller manages the CRUD operations for JenisBarang (Material Categories) and also includes a method to delete a Barang (Material) if it belongs to the 'material' category. The index method provides a paginated list of materials with search functionality and summary statistics about the materials in stock.
namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\JenisBarang;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class JenisBarangController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $stokMinimumBatas = 10;

        $query = Material::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('materials.nama', 'ilike', '%' . $search . '%')
                        ->orWhere('materials.ukuran', 'ilike', '%' . $search . '%')
                        ->orWhere('materials.satuan', 'ilike', '%' . $search . '%');
                });
            })
            ->latest('created_at');

        $materialItems = (clone $query)->get();
        $summaryTotalItem = $materialItems->count();
        $summaryTotalStok = $materialItems->sum(function ($material): int {
            return (int) ($material->quantity ?? 0);
        });
        $summaryStokMinimumItem = $materialItems->filter(function ($material) use ($stokMinimumBatas): bool {
            $stok = (int) ($material->quantity ?? 0);
            $stokMinimum = (int) ($material->stok_minimum ?? $stokMinimumBatas);

            return $stok <= $stokMinimum;
        })->count();
        $lowStockBarangs = $materialItems->filter(function ($material) use ($stokMinimumBatas): bool {
            $stok = (int) ($material->quantity ?? 0);

            return $stok <= $stokMinimumBatas;
        })->values();

        $barangs = $query->paginate(10)->withQueryString();

        $barangs->getCollection()->transform(function ($material) {
            $currentQuantity = (int) ($material->quantity ?? 0);

            $material->total_quantity = $currentQuantity;
            $material->latest_satuan = $material->satuan;
            $material->stok_minimum = (int) ($material->stok_minimum ?? 10);
            $material->current_quantity = $currentQuantity;
            $material->display_quantity = $currentQuantity;

            return $material;
        });

        return view('data-material.index', compact(
            'barangs',
            'search',
            'summaryTotalItem',
            'summaryTotalStok',
            'summaryStokMinimumItem',
            'stokMinimumBatas',
            'lowStockBarangs'
        ));
    }

    public function create()
    {
        return view('data-material.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:jenis_barang,nama',
            'deskripsi' => 'nullable|string',
        ]);

        JenisBarang::create($validated);

        return redirect()->route('data-material.index')->with('success', 'Kategori material berhasil ditambahkan');
    }

    public function show(JenisBarang $jenisBarang)
    {
        return view('data-material.show', compact('jenisBarang'));
    }

    public function edit(JenisBarang $jenisBarang)
    {
        return view('data-material.edit', compact('jenisBarang'));
    }

    public function update(Request $request, JenisBarang $jenisBarang)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:jenis_barang,nama,' . $jenisBarang->id_jenis_barang . ',id_jenis_barang',
            'deskripsi' => 'nullable|string',
        ]);

        $jenisBarang->update($validated);

        return redirect()->route('data-material.index')->with('success', 'Kategori material berhasil diperbarui');
    }

    public function destroy(JenisBarang $jenisBarang)
    {
        $jenisBarang->delete();

        return redirect()->route('data-material.index')->with('success', 'Kategori material berhasil dihapus');
    }

    public function destroyBarang(Material $barang)
    {
        try {
            $barang->delete();
        } catch (QueryException $exception) {
            if (($exception->errorInfo[0] ?? null) === '23503') {
                return redirect()
                    ->back()
                    ->with('error', 'Material tidak dapat dihapus karena masih dipakai pada transaksi (mis. pemesanan/proses).');
            }

            throw $exception;
        }

        return redirect()->route('data-material.index')->with('success', 'Material berhasil dihapus');
    }
}
