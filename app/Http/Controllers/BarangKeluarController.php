<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductionItem;
use App\Models\Shipment as BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarangKeluarController extends Controller
{
    private const STATUS_OPTIONS = [
        'Siap Dikirim',
        'Sedang Dikirim',
        'Selesai',
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', ''));
        $tanggal = trim((string) $request->query('tanggal', ''));

        $statusFilterMap = [
            'Dalam Proses' => ['Dalam Proses', 'Menunggu Pengiriman'],
            'Siap Dikirim' => ['Siap Dikirim', 'Dalam Pengiriman'],
            'Sedang Dikirim' => ['Sedang Dikirim'],
            'Selesai' => ['Selesai'],
        ];

        $siapDikirimItems = ProductionItem::query()
            ->with(['produk:id_product,kode,nama', 'pelanggan:id_pelanggan,nama'])
            ->where('status_kirim', true)
            ->whereDoesntHave('shipment')
            ->latest('created_at')
            ->get();

        $barangKeluars = BarangKeluar::query()
            ->active()
            ->with(['barang', 'customer', 'user', 'productionItem'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('barang', function ($barangQuery) use ($search) {
                        $barangQuery->where('nama', 'ilike', '%' . $search . '%');
                    })->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('nama', 'ilike', '%' . $search . '%');
                    });
                });
            })
            ->when($status !== '' && isset($statusFilterMap[$status]), function ($query) use ($status, $statusFilterMap) {
                $query->whereIn('status_pengiriman', $statusFilterMap[$status]);
            })
            ->when($tanggal !== '', function ($query) use ($tanggal) {
                $query->whereDate('tanggal_pengiriman', $tanggal);
            })
            ->latest('created_at')
            ->paginate(10)
            ->appends($request->query());

        return view('barang-keluar.index', compact('barangKeluars', 'siapDikirimItems', 'search', 'status', 'tanggal'));
    }

    public function create()
    {
        $barangs = Product::query()->orderBy('nama')->get();
        $customers = Customer::all();

        // Keep prefill in session until submission/cancel so id_barang_proses is still available on store.
        $prefillData = session('pengiriman_dari_proses');

        // Production items that are marked ready to ship and have a PO number
        $poItems = ProductionItem::query()
            ->where('status_kirim', true)
            ->whereNotNull('no_po')
            ->get(['id_barang_proses', 'id_produk', 'no_po']);

        return view('barang-keluar.create', compact('barangs', 'customers', 'prefillData', 'poItems'));
    }

    public function store(Request $request)
    {
        // Check if coming from barang dalam proses
        $prefillData = session('pengiriman_dari_proses');

        // Validation - no stock check because produk langsung dikirim tanpa masuk gudang
        $validated = $request->validate([
            'id_barang' => 'required|exists:products,id_product',
            'id_customer' => 'nullable|exists:pelanggan,id_pelanggan',
            'quantity' => 'required|integer|min:1',
            'no_po' => 'nullable|string|max:100',
            'tanggal_keluar' => 'required|date',
            'status_pengiriman' => 'nullable|string|in:Siap Dikirim,Sedang Dikirim,Selesai',
            'surat_jalan' => 'nullable|file|max:5120',
            'invoice' => 'nullable|file|max:5120',
            'id_barang_proses' => 'nullable',
            'reserve_token' => 'nullable|string',
            'material_nama_prefill' => 'nullable|string',
        ]);

        $idBarangProses = $validated['id_barang_proses'] ?? ($prefillData['id_barang_proses'] ?? null);
        $reserveToken = $validated['reserve_token'] ?? ($prefillData['reserve_token'] ?? null);

        // CRITICAL: Verify that id_barang_proses exists before setting it
        // If it doesn't exist (was deleted), set to NULL to avoid FK violation
        if (! empty($idBarangProses)) {
            $prosesExists = ProductionItem::where('id_barang_proses', $idBarangProses)->exists();
            if (! $prosesExists) {
                $idBarangProses = null;
            }
        }

        $productionItem = ! empty($idBarangProses)
            ? ProductionItem::query()->with(['material.jenisBarang'])->find($idBarangProses)
            : null;

        $data = [
            'id_produk' => $validated['id_barang'],
            'id_pelanggan' => $validated['id_customer'] ?? $prefillData['id_customer'] ?? null,
            'id_barang_proses' => $idBarangProses,
            'no_po' => $validated['no_po'] ?? ($prefillData['no_po'] ?? null),
            'qty' => $validated['quantity'],
            'tanggal_pengiriman' => $validated['tanggal_keluar'],
            'status_pengiriman' => $validated['status_pengiriman'] ?? 'Siap Dikirim',
            'id_user' => auth()->id(),
        ];

        // Capture material category name for shipment snapshot
        if ($productionItem?->material) {
            $materialNama = $productionItem->material->nama ?? null;
            $jenisBarangNama = optional($productionItem->material->jenisBarang)->nama ?? null;
            // Snapshot the specific material name first (not category)
            $data['material_type'] = $materialNama ?? $jenisBarangNama ?? null;
        } elseif (! empty($validated['material_nama_prefill'])) {
            $data['material_type'] = $validated['material_nama_prefill'];
        } elseif (isset($prefillData['material_nama'])) {
            $data['material_type'] = $prefillData['material_nama'];
        } elseif (isset($prefillData['material_kategori_nama'])) {
            $data['material_type'] = $prefillData['material_kategori_nama'];
        }

        if ($request->hasFile('surat_jalan')) {
            $data['surat_jalan_path'] = $request->file('surat_jalan')->store('barang-keluar/surat-jalan', 'public');
        }

        if ($request->hasFile('invoice')) {
            $data['invoice_path'] = $request->file('invoice')->store('barang-keluar/invoice', 'public');
        }

        DB::transaction(function () use ($data, $idBarangProses, $reserveToken): void {
            BarangKeluar::create($data);

            if (! empty($idBarangProses)) {
                $prosesQuery = ProductionItem::query()->where('id_barang_proses', $idBarangProses);
                if (! empty($reserveToken)) {
                    $prosesQuery->where('reserve_token', $reserveToken);
                }

                $proses = $prosesQuery->first();
                if ($proses) {
                    $proses->update([
                        'status_kirim' => true,
                        'processing' => false,
                        'reserve_token' => null,
                        'processing_started_at' => null,
                        'processing_by' => null,
                        'tgl_selesai' => now()->toDateString(),
                    ]);
                }
            }
        });

        // Clear prefill only after successful store transaction.
        session()->forget('pengiriman_dari_proses');

        return redirect()->route('pengiriman-produk.index')->with('success', 'Pengiriman produk berhasil ditambahkan');
    }

    public function show(BarangKeluar $pengiriman_produk)
    {
        $pengiriman_produk->load(['barang', 'customer', 'user']);

        return view('barang-keluar.show', compact('pengiriman_produk'));
    }

    public function generateSuratJalan(BarangKeluar $pengiriman_produk)
    {
        $pengiriman_produk->load(['barang', 'customer', 'user']);

        $data = [
            'nomor_surat_jalan' => $pengiriman_produk->surat_jalan_number ?? $pengiriman_produk->id_pengiriman,
            'tanggal_kirim' => optional($pengiriman_produk->tanggal_pengiriman)->format('d/m/Y') ?? '-',
            'customer' => optional($pengiriman_produk->customer)->nama ?? '-',
            'quantity' => $pengiriman_produk->qty ?? '-',
            'product_name' => optional($pengiriman_produk->barang)->nama ?? '-',
            'product_index' => optional($pengiriman_produk->barang)->kode ?? optional($pengiriman_produk->barang)->id ?? '-',
            'material_name' => $pengiriman_produk->material_type ?? '-',
            'no_po' => $pengiriman_produk->no_po ?? '-',
            'background' => public_path('images/surat-jalan-template.jpeg'),
            'logo' => public_path('images/logo-mab.jpeg'),
        ];

        $html = view('pengiriman-produk.surat-jalan', $data)->render();

        try {
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);

            // Some templates expect default fonts; ensure UTF-8
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->WriteHTML($html);

            $filename = 'surat-jalan-' . ($data['surat_jalan_number'] ?? $pengiriman_produk->id_pengiriman) . '.pdf';
            return $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
        } catch (\Throwable $e) {
            \Log::error('Surat jalan generation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat Surat Jalan: ' . $e->getMessage());
        }
    }

    public function edit(BarangKeluar $pengiriman_produk)
    {
        $barangs = Product::query()->orderBy('nama')->get();
        $customers = Customer::all();

        return view('barang-keluar.edit', compact('pengiriman_produk', 'barangs', 'customers'));
    }

    public function update(Request $request, BarangKeluar $pengiriman_produk)
    {
        $validated = $request->validate([
            'id_barang' => 'required|exists:products,id_product',
            'id_customer' => 'required|exists:pelanggan,id_pelanggan',
            'quantity' => 'required|integer|min:1',
            'no_po' => 'nullable|string|max:100',
            'tanggal_keluar' => 'required|date',
            'status_pengiriman' => 'nullable|string|in:Siap Dikirim,Sedang Dikirim,Selesai',
            'surat_jalan' => 'nullable|file|max:5120',
            'invoice' => 'nullable|file|max:5120',
        ]);

        // Removed stock availability check: make-to-order flow doesn't validate against warehouse stock.

        $data = [
            'id_produk' => $validated['id_barang'],
            'id_pelanggan' => $validated['id_customer'],
            'no_po' => $validated['no_po'] ?? $pengiriman_produk->no_po ?? null,
            'qty' => $validated['quantity'],
            'tanggal_pengiriman' => $validated['tanggal_keluar'],
            'status_pengiriman' => $validated['status_pengiriman'] ?? $pengiriman_produk->status_pengiriman ?? 'Siap Dikirim',
        ];

        // keep material snapshot in update if barang selected
        $product = Product::find($validated['id_barang']);
        if ($product) {
            $data['material_type'] = $pengiriman_produk->material_type ?? null;
        }

        if ($request->hasFile('surat_jalan')) {
            if ($pengiriman_produk->surat_jalan_path) {
                Storage::disk('public')->delete($pengiriman_produk->surat_jalan_path);
            }

            $data['surat_jalan_path'] = $request->file('surat_jalan')->store('barang-keluar/surat-jalan', 'public');
        }

        if ($request->hasFile('invoice')) {
            if ($pengiriman_produk->invoice_path) {
                Storage::disk('public')->delete($pengiriman_produk->invoice_path);
            }

            $data['invoice_path'] = $request->file('invoice')->store('barang-keluar/invoice', 'public');
        }

        $pengiriman_produk->update($data);

        return redirect()->route('pengiriman-produk.index')->with('success', 'Pengiriman produk berhasil diperbarui');
    }

    public function destroy(BarangKeluar $pengiriman_produk)
    {
        \Log::warning('=== DESTROY CALLED ===');
        \Log::warning('ID: ' . $pengiriman_produk->id_pengiriman);
        \Log::warning('Before delete - exists: ' . (BarangKeluar::find($pengiriman_produk->id_pengiriman) ? 'YES' : 'NO'));

        if ($pengiriman_produk->surat_jalan_path) {
            Storage::disk('public')->delete($pengiriman_produk->surat_jalan_path);
        }

        if ($pengiriman_produk->invoice_path) {
            Storage::disk('public')->delete($pengiriman_produk->invoice_path);
        }

        if ($pengiriman_produk->productionItem) {
            $pengiriman_produk->productionItem->update([
                'status_kirim' => false,
                'tgl_selesai' => null,
                'processing' => false,
                'reserve_token' => null,
                'processing_started_at' => null,
                'processing_by' => null,
            ]);
        }

        $result = $pengiriman_produk->delete();
        \Log::warning('Delete returned: ' . ($result ? 'TRUE' : 'FALSE'));
        \Log::warning('After delete - exists: ' . (BarangKeluar::find($pengiriman_produk->id_pengiriman) ? 'YES' : 'NO'));

        return redirect()->route('pengiriman-produk.index')->with('success', 'Pengiriman produk berhasil dihapus');
    }

    public function downloadFile(Shipmenthopipmeeopar $barangKeluar, string $type)
    {
        if ($type === 'surat_jalan') {
            $path = $barangKeluar->surat_jalan_path;
            $filename = 'surat-jalan-' . $barangKeluar->id_pengiriman . '.' . pathinfo((string) $path, PATHINFO_EXTENSION);
        } elseif ($type === 'invoice') {
            $path = $barangKeluar->invoice_path;
            $filename = 'invoice-' . $barangKeluar->id_pengiriman . '.' . pathinfo((string) $path, PATHINFO_EXTENSION);
        } else {
            abort(404);
        }

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, $filename);
    }

    public static function statusOptions(): array
    {
        return self::STATUS_OPTIONS;
    }

}
