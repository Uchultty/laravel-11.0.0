<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\JenisBarang;
use App\Models\Material;
use App\Models\Product;
use App\Models\ProductionItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BarangDalamProsesController extends Controller
{
    public function index()
    {
        $prosesItems = ProductionItem::query()
            ->with([
                'produk:id_product,nama',
                'material:id_material,nama',
                'pelanggan:id_pelanggan,nama',
                'shipment:id_pengiriman,id_barang_proses',
            ])
            ->where('status_kirim', false)
            ->latest('created_at')
            ->paginate(10);

        return view('barang-dalam-proses.index', compact('prosesItems'));
    }

    public function create()
    {
        $barangs = Product::query()
            ->orderBy('nama')
            ->get(['id_product', 'kode', 'nama', 'ukuran', 'satuan']);
        $materials = Material::query()
            ->orderBy('nama')
            ->get(['id_material', 'nama', 'ukuran', 'quantity']);
        $customers = Customer::query()->orderBy('nama')->get(['id_pelanggan', 'nama']);

        return view('barang-dalam-proses.create', compact('barangs', 'materials', 'customers'));
    }

    public function storeProses(Request $request)
    {
        $validated = $request->validate([
            'id_customer' => 'required|exists:pelanggan,id_pelanggan',
            'no_po' => 'required|string|max:100',
            'tanggal_buat' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_buat',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:products,id_product',
            'items.*.no_gambar' => 'nullable|string|max:100',
            'items.*.id_barang_mentah' => 'required|exists:materials,id_material',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.satuan' => 'required|in:mm,inch',
            'items.*.ukuran' => 'required|string|max:100',
        ]);

        try {
            DB::transaction(function () use ($validated): void {
                foreach ($validated['items'] as $item) {
                    $qty = (int) $item['quantity'];

                    $material = Material::query()
                        ->whereKey($item['id_barang_mentah'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ((int) $material->quantity < $qty) {
                        throw ValidationException::withMessages([
                            'items' => 'Stok material tidak cukup untuk salah satu item yang diminta.',
                        ]);
                    }

                    $material->decrement('quantity', $qty);

                    ProductionItem::create([
                        'id_produk' => $item['id_barang'],
                        'no_gambar' => $item['no_gambar'] ?? null,
                        'id_material' => $item['id_barang_mentah'],
                        'id_user' => auth()->id(),
                        'id_pelanggan' => $validated['id_customer'],
                        'no_po' => $validated['no_po'],
                        'status_kirim' => false,
                        'qty' => $qty,
                        'satuan' => $item['satuan'],
                        'ukuran' => $item['ukuran'],
                        'tgl_dibuat' => $validated['tanggal_buat'],
                        'tgl_selesai' => $validated['tanggal_selesai'] ?? null,
                    ]);
                }
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()->route('barang-dalam-proses.index')->with('success', 'Barang dalam proses berhasil ditambahkan');
    }

    public function editProses(ProductionItem $barangDalamProses)
    {
        $barangs = Product::query()
            ->orderBy('nama')
            ->get(['id_product', 'kode', 'nama', 'ukuran', 'satuan']);
        $materials = Material::query()
            ->orderBy('nama')
            ->get(['id_material', 'nama', 'ukuran', 'quantity']);
        $customers = Customer::query()->orderBy('nama')->get(['id_pelanggan', 'nama']);

        return view('barang-dalam-proses.edit', compact('barangDalamProses', 'barangs', 'materials', 'customers'));
    }

    public function updateProses(Request $request, ProductionItem $barangDalamProses)
    {
        $validated = $request->validate([
            'id_barang' => 'required|exists:products,id_product',
            'no_gambar' => 'nullable|string|max:100',
            'id_barang_mentah' => 'required|exists:materials,id_material',
            'quantity' => 'required|integer|min:1',
            'id_customer' => 'required|exists:pelanggan,id_pelanggan',
            'no_po' => 'required|string|max:100',
            'satuan' => 'required|in:mm,inch',
            'ukuran' => 'required|string|max:100',
            'tanggal_buat' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_buat',
        ]);

        try {
            DB::transaction(function () use ($barangDalamProses, $validated): void {
                $oldMaterialId = $barangDalamProses->id_material;
                $oldQty = (int) $barangDalamProses->qty;
                $newMaterialId = $validated['id_barang_mentah'];
                $newQty = (int) $validated['quantity'];

                if ($oldMaterialId === $newMaterialId) {
                    $material = Material::query()
                        ->whereKey($newMaterialId)
                        ->lockForUpdate()
                        ->firstOrFail();

                    $availableStock = (int) $material->quantity + $oldQty;

                    if ($newQty > $availableStock) {
                        throw ValidationException::withMessages([
                            'id_barang_mentah' => 'Stok material tidak cukup untuk quantity yang diminta.',
                        ]);
                    }

                    $material->quantity = $availableStock - $newQty;
                    $material->save();
                } else {
                    $oldMaterial = Material::query()
                        ->whereKey($oldMaterialId)
                        ->lockForUpdate()
                        ->firstOrFail();
                    $oldMaterial->increment('quantity', $oldQty);

                    $newMaterial = Material::query()
                        ->whereKey($newMaterialId)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ((int) $newMaterial->quantity < $newQty) {
                        throw ValidationException::withMessages([
                            'id_barang_mentah' => 'Stok material tidak cukup untuk quantity yang diminta.',
                        ]);
                    }

                    $newMaterial->decrement('quantity', $newQty);
                }

                $barangDalamProses->update([
                    'id_produk' => $validated['id_barang'],
                    'no_gambar' => $validated['no_gambar'] ?? null,
                    'id_material' => $validated['id_barang_mentah'],
                    'id_pelanggan' => $validated['id_customer'] ?? null,
                    'no_po' => $validated['no_po'],
                    'qty' => $newQty,
                    'satuan' => $validated['satuan'],
                    'ukuran' => $validated['ukuran'],
                    'tgl_dibuat' => $validated['tanggal_buat'],
                    'tgl_selesai' => $validated['tanggal_selesai'] ?? null,
                ]);
            });
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()->route('barang-dalam-proses.index')->with('success', 'Barang dalam proses berhasil diperbarui');
    }

    public function destroyProses(ProductionItem $barangDalamProses)
    {
        DB::transaction(function () use ($barangDalamProses): void {
            if ($barangDalamProses->id_material && (int) $barangDalamProses->qty > 0) {
                $material = Material::query()
                    ->whereKey($barangDalamProses->id_material)
                    ->lockForUpdate()
                    ->first();

                if ($material) {
                    $material->increment('quantity', (int) $barangDalamProses->qty);
                }
            }

            // Delete related shipment if exists
            $barangDalamProses->shipment()?->delete();

            // Delete production item
            $barangDalamProses->delete();
        });

        return redirect()->route('barang-dalam-proses.index')->with('success', 'Barang dalam proses berhasil dihapus');
    }

    public function markSiapDikirim(ProductionItem $barangDalamProses)
    {
        $barangDalamProses->update([
            'status_kirim' => true,
            'tgl_selesai' => null,
            'processing' => false,
            'reserve_token' => null,
            'processing_started_at' => null,
            'processing_by' => null,
        ]);

        return redirect()->route('barang-dalam-proses.index')->with('success', 'Status barang diubah menjadi Siap Dikirim');
    }

    private function generateKodeBarang(): string
    {
        do {
            $kode = 'BRG-' . now()->format('ymd') . '-' . str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
        } while (Product::query()->where('kode', $kode)->exists());

        return $kode;
    }

    public function preparePengiriman(ProductionItem $barangDalamProses)
    {
        // barangDalamProses is now a ProductionItem
        $productionItem = $barangDalamProses;

        $product = $productionItem->produk;
        $material = $productionItem->material()->with('jenisBarang')->first();

        if (! $product || ! $material) {
            return redirect()->route('barang-dalam-proses.index')->with('error', 'Data produk atau material tidak ditemukan.');
        }

        // Get material names
        $materialNama = (string) $material->nama;
        $materialCategoryNama = optional($material->jenisBarang)->nama ?? null;

        // Prepare a plain array (no objects/Closures) for session to avoid offset errors
        $payload = [
            'id_barang_proses' => $productionItem->id_barang_proses,
            'id_barang' => $product->id_product,
            'no_gambar' => $productionItem->no_gambar ?? null,
            'id_material' => $material->id_material,
            'quantity' => (int) $productionItem->qty,
            'id_customer' => $productionItem->id_pelanggan,
            'barang_nama' => (string) $product->nama,
            'material_kategori_nama' => $materialCategoryNama,
            'material_nama' => $materialNama,
            'customer_nama' => $productionItem->pelanggan ? (string) $productionItem->pelanggan->nama : null,
            'tanggal_pengiriman_default' => optional($productionItem->tgl_selesai)->format('Y-m-d') ?? now()->addDay()->toDateString(),
            'no_po' => $productionItem->no_po ?? null,
        ];

        session()->put('pengiriman_dari_proses', $payload);

        return redirect()->route('pengiriman-produk.create')
            ->with('success', 'Reservasi berhasil dibuat. Silahkan lengkapi data pengiriman.');
    }

    public function preparePengirimanGroup(Request $request)
    {
        $validated = $request->validate([
            'id_pelanggan' => 'required|integer',
            'no_po' => 'required|string|max:100',
            'tgl_dibuat' => 'required|date',
            'tgl_selesai' => 'nullable|date',
        ]);

        $query = ProductionItem::query()
            ->with(['produk:id_product,nama', 'material:id_material,nama', 'pelanggan:id_pelanggan,nama'])
            ->where('status_kirim', false)
            ->where('id_pelanggan', $validated['id_pelanggan'])
            ->where('no_po', $validated['no_po'])
            ->whereDate('tgl_dibuat', $validated['tgl_dibuat']);

        if (! empty($validated['tgl_selesai'])) {
            $query->whereDate('tgl_selesai', $validated['tgl_selesai']);
        } else {
            $query->whereNull('tgl_selesai');
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return redirect()->route('barang-dalam-proses.index')->with('error', 'Data grup barang tidak ditemukan.');
        }

        $freshItems = $items->map(function (ProductionItem $item) {
            return [
                'id_barang_proses' => $item->id_barang_proses,
                'id_barang' => $item->id_produk,
                'id_material' => $item->id_material,
                'barang_nama' => (string) optional($item->produk)->nama,
                'material_nama' => (string) optional($item->material)->nama,
                'no_gambar' => $item->no_gambar ?? null,
                'quantity' => (int) $item->qty,
            ];
        })->values()->all();

        $header = $items->first();

        session()->put('pengiriman_dari_proses', [
            'mode' => 'group',
            'id_customer' => $header->id_pelanggan,
            'customer_nama' => $header->pelanggan ? (string) $header->pelanggan->nama : null,
            'no_po' => $header->no_po,
            'tanggal_pengiriman_default' => optional($header->tgl_selesai)->format('Y-m-d') ?? now()->addDay()->toDateString(),
            'items' => $freshItems,
        ]);

        return redirect()->route('pengiriman-produk.create')
            ->with('success', 'Reservasi grup berhasil dibuat. Silakan lengkapi data pengiriman.');
    }

    public function cancelReserve(ProductionItem $barangDalamProses)
    {
        // barangDalamProses is now a ProductionItem
        $productionItem = $barangDalamProses;

        // Single-role app: allow cancellation by any authenticated user

        $productionItem->update([
            'processing' => false,
            'reserve_token' => null,
            'processing_started_at' => null,
            'processing_by' => null,
        ]);

        $prefill = session('pengiriman_dari_proses');
        if (is_array($prefill) && (($prefill['id_barang_proses'] ?? null) == $productionItem->id_barang_proses)) {
            session()->forget('pengiriman_dari_proses');
        }

        return redirect()->route('barang-dalam-proses.index')->with('success', 'Reservasi dibatalkan');
    }

    public function cancelReserveGroup()
    {
        $prefill = session('pengiriman_dari_proses');

        $itemIds = collect($prefill['items'] ?? [])->pluck('id_barang_proses')->filter()->all();

        if (! empty($itemIds)) {
            ProductionItem::query()
                ->whereIn('id_barang_proses', $itemIds)
                ->update([
                    'processing' => false,
                    'reserve_token' => null,
                    'processing_started_at' => null,
                    'processing_by' => null,
                ]);
        }

        session()->forget('pengiriman_dari_proses');

        return redirect()->route('barang-dalam-proses.index')->with('success', 'Reservasi grup dibatalkan');
    }
}
