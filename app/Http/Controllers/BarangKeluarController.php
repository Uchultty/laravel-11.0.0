<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductionItem;
use App\Models\Shipment as BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

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
        $prefillData = session('pengiriman_dari_proses');

        // If prefill data exists but one-time token is gone, user refreshed the page → cancel reserve and go back
        if ($prefillData) {
            if (session()->has('pengiriman_create_once')) {
                session()->forget('pengiriman_create_once');
            } else {
                $isGroup = isset($prefillData['mode']) && $prefillData['mode'] === 'group';
                if ($isGroup) {
                    $itemIds = collect($prefillData['items'] ?? [])->pluck('id_pemesanan_produk')->filter()->all();
                    if (! empty($itemIds)) {
                        ProductionItem::query()->whereIn('id_pemesanan_produk', $itemIds)->update([
                            'processing' => false,
                            'reserve_token' => null,
                            'processing_started_at' => null,
                            'processing_by' => null,
                        ]);
                    }
                } else {
                    $id = $prefillData['id_pemesanan_produk'] ?? null;
                    if ($id) {
                        ProductionItem::where('id_pemesanan_produk', $id)->update([
                            'processing' => false,
                            'reserve_token' => null,
                            'processing_started_at' => null,
                            'processing_by' => null,
                        ]);
                    }
                }
                session()->forget('pengiriman_dari_proses');

                return redirect()->route('barang-dalam-proses.index');
            }
        }

        $barangs = Product::query()->orderBy('nama')->get();
        $customers = Customer::all();

        // Production items that are marked ready to ship and have a PO number
        $poItems = ProductionItem::query()
            ->where('status_kirim', true)
            ->whereNotNull('no_po')
            ->get(['id_pemesanan_produk', 'id_produk', 'no_po']);

        $isGroupPrefill = isset($prefillData['mode']) && $prefillData['mode'] === 'group' && ! empty($prefillData['items']);

        return view('barang-keluar.create', compact('barangs', 'customers', 'prefillData', 'poItems', 'isGroupPrefill'));
    }

    public function store(Request $request)
    {
        // Check if coming from barang dalam proses
        $prefillData = session('pengiriman_dari_proses');
        $isGroupPrefill = isset($prefillData['mode']) && $prefillData['mode'] === 'group' && ! empty($prefillData['items']);

        if ($isGroupPrefill) {
            $validated = $request->validate([
                'tanggal_keluar' => 'required|date',
                'status_pengiriman' => 'nullable|string|in:Siap Dikirim,Sedang Dikirim,Selesai',
                'surat_jalan' => 'nullable|file|max:5120',
                'invoice' => 'nullable|file|max:5120',
            ]);

            $suratJalanPath = $request->hasFile('surat_jalan')
                ? $request->file('surat_jalan')->store('barang-keluar/surat-jalan', 'public')
                : null;
            $invoicePath = $request->hasFile('invoice')
                ? $request->file('invoice')->store('barang-keluar/invoice', 'public')
                : null;

            $items = collect($prefillData['items'] ?? []);

            DB::transaction(function () use ($items, $prefillData, $validated, $suratJalanPath, $invoicePath): void {
                $itemsData = [];
                $totalQty = 0;
                $firstIdBarangProses = null;
                $firstProductionItem = null;

                foreach ($items as $prefillItem) {
                    $idBarangProses = $prefillItem['id_pemesanan_produk'] ?? null;

                    if (! $idBarangProses) {
                        continue;
                    }

                    $productionItem = ProductionItem::query()->with(['material.jenisBarang'])->find($idBarangProses);
                    if (! $productionItem) {
                        continue;
                    }

                    if (! $firstIdBarangProses) {
                        $firstIdBarangProses = $idBarangProses;
                        $firstProductionItem = $productionItem;
                    }

                    $qty = (int) ($prefillItem['quantity'] ?? $productionItem->qty);
                    $totalQty += $qty;

                    $itemsData[] = [
                        'id_pemesanan_produk' => $idBarangProses,
                        'id_produk' => $prefillItem['id_barang'] ?? $productionItem->id_produk,
                        'no_gambar' => $prefillItem['no_gambar'] ?? $productionItem->no_gambar,
                        'qty' => $qty,
                        'material_type' => $productionItem->material?->nama ?? optional($productionItem->material?->jenisBarang)->nama,
                    ];
                }

                if (empty($itemsData)) {
                    return;
                }

                $materialNama = $firstProductionItem->material?->nama;
                $materialKategoriNama = optional($firstProductionItem->material?->jenisBarang)->nama;

                $shipment = BarangKeluar::create([
                    'id_produk' => $itemsData[0]['id_produk'],
                    'id_pelanggan' => $prefillData['id_customer'] ?? $firstProductionItem->id_pelanggan,
                    'id_pemesanan_produk' => $firstIdBarangProses,
                    'no_po' => $prefillData['no_po'] ?? $firstProductionItem->no_po,
                    'no_gambar' => $itemsData[0]['no_gambar'],
                    'qty' => $totalQty,
                    'tanggal_pengiriman' => $validated['tanggal_keluar'],
                    'status_pengiriman' => $validated['status_pengiriman'] ?? 'Siap Dikirim',
                    'id_user' => auth()->id(),
                    'material_type' => $materialNama ?? $materialKategoriNama,
                    'surat_jalan_path' => $suratJalanPath,
                    'invoice_path' => $invoicePath,
                    'items' => $itemsData,
                ]);

                foreach ($items as $prefillItem) {
                    $idBarangProses = $prefillItem['id_pemesanan_produk'] ?? null;
                    if ($idBarangProses) {
                        ProductionItem::query()
                            ->where('id_pemesanan_produk', $idBarangProses)
                            ->update([
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

            session()->forget('pengiriman_dari_proses');

            return redirect()->route('pengiriman-produk.index')->with('success', 'Pengiriman grup produk berhasil ditambahkan');
        }

        // Validation - no stock check because produk langsung dikirim tanpa masuk gudang
        $validated = $request->validate([
            'id_barang' => 'required|exists:products,id_product',
            'id_customer' => 'nullable|exists:pelanggan,id_pelanggan',
            'quantity' => 'required|integer|min:1',
            'no_po' => 'nullable|string|max:100',
            'no_gambar' => 'nullable|string|max:100',
            'tanggal_keluar' => 'required|date',
            'status_pengiriman' => 'nullable|string|in:Siap Dikirim,Sedang Dikirim,Selesai',
            'surat_jalan' => 'nullable|file|max:5120',
            'invoice' => 'nullable|file|max:5120',
            'id_pemesanan_produk' => 'nullable',
            'material_nama_prefill' => 'nullable|string',
        ]);

        $idBarangProses = $validated['id_pemesanan_produk'] ?? ($prefillData['id_pemesanan_produk'] ?? null);

        // CRITICAL: Verify that id_pemesanan_produk exists before setting it
        // If it doesn't exist (was deleted), set to NULL to avoid FK violation
        if (! empty($idBarangProses)) {
            $prosesExists = ProductionItem::where('id_pemesanan_produk', $idBarangProses)->exists();
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
            'id_pemesanan_produk' => $idBarangProses,
            'no_po' => $validated['no_po'] ?? ($prefillData['no_po'] ?? null),
            'no_gambar' => $validated['no_gambar'] ?? ($prefillData['no_gambar'] ?? null),
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

        DB::transaction(function () use ($data, $idBarangProses): void {
            BarangKeluar::create($data);

            if (! empty($idBarangProses)) {
                $proses = ProductionItem::query()->where('id_pemesanan_produk', $idBarangProses)->first();
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
        $pengiriman_produk->load(['barang', 'customer', 'user', 'productionItem.material']);

        $productionItems = collect();
        if (!empty($pengiriman_produk->items) && is_array($pengiriman_produk->items)) {
            $ids = collect($pengiriman_produk->items)->pluck('id_pemesanan_produk')->filter()->values();
            if ($ids->isNotEmpty()) {
                $productionItems = ProductionItem::whereIn('id_pemesanan_produk', $ids)
                    ->with('produk')
                    ->get()
                    ->keyBy('id_pemesanan_produk');
            }
        }

        return view('barang-keluar.show', compact('pengiriman_produk', 'productionItems'));
    }

    public function suratJalan(BarangKeluar $pengiriman_produk)
    {
        $pengiriman_produk->loadMissing(['barang', 'customer', 'productionItem.material']);

        return view('pengiriman-produk.surat-jalan', $this->suratJalanViewData($pengiriman_produk));
    }

    public function downloadSuratJalanPdf(BarangKeluar $pengiriman_produk)
    {
        $pengiriman_produk->loadMissing(['barang', 'customer', 'productionItem.material']);

        $data = $this->suratJalanViewData($pengiriman_produk, true);
        $html = view('pengiriman-produk.surat-jalan', $data)->render();

        $mpdf = new Mpdf([
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'tempDir' => storage_path('app'),
        ]);

        $mpdf->SetTitle('Surat Jalan ' . $data['nomor_surat_jalan']);
        $mpdf->WriteHTML($html);

        $filename = 'surat-jalan-' . str_replace(['/', ' '], '-', $data['nomor_surat_jalan']) . '.pdf';

        return response(
            $mpdf->Output($filename, 'S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
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

    public function downloadFile(BarangKeluar $barangKeluar, string $type)
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

    private function suratJalanViewData(BarangKeluar $pengiriman_produk, bool $pdfMode = false): array
    {
        $tanggal = $pengiriman_produk->tanggal_keluar ?? $pengiriman_produk->tanggal_pengiriman ?? now();
        $nomorSuratJalan = $this->generateNomorSuratJalan($pengiriman_produk, $tanggal);

        $logoPath = public_path('images/logo-mab.png');
        $logoSrc = $pdfMode && is_file($logoPath)
            ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath))
            : asset('images/logo-mab.png');

        $customerName = optional($pengiriman_produk->customer)->nama ?? '-';

        $items = [];
        $itemNo = 1;

        if (! empty($pengiriman_produk->items) && is_array($pengiriman_produk->items)) {
            foreach ($pengiriman_produk->items as $item) {
                $items[] = [
                    'no' => $itemNo++,
                    'nama_barang' => optional(Product::find($item['id_produk']))->nama ?? '-',
                    'material' => $item['material_type'] ?? '-',
                    'quantity' => $item['qty'] ?? 0,
                    'no_gambar' => $item['no_gambar'] ?? '-',
                    'keterangan' => 'No PO: ' . ($pengiriman_produk->no_po ?? '-'),
                ];
            }
        } else {
            $productName = optional($pengiriman_produk->barang)->nama ?? '-';
            $materialName = data_get($pengiriman_produk, 'productionItem.material.nama')
                ?? $pengiriman_produk->material_type
                ?? '-';

            $items[] = [
                'no' => 1,
                'nama_barang' => $productName,
                'material' => $materialName,
                'quantity' => $pengiriman_produk->qty,
                'no_gambar' => $pengiriman_produk->no_gambar ?? '-',
                'keterangan' => 'No PO: ' . ($pengiriman_produk->no_po ?? '-'),
            ];
        }

        return [
            'pengiriman_produk' => $pengiriman_produk,
            'nomor_surat_jalan' => $nomorSuratJalan,
            'tanggal_kirim' => $tanggal,
            'customer_name' => $customerName,
            'no_po' => $pengiriman_produk->no_po ?? '-',
            'no_gambar' => $pengiriman_produk->no_gambar ?? '-',
            'logo_src' => $logoSrc,
            'items' => $items,
            'pdf_mode' => $pdfMode,
        ];
    }

    private function generateNomorSuratJalan(BarangKeluar $pengiriman_produk, $tanggal): string
    {
        $rawKey = (string) $pengiriman_produk->getKey();
        preg_match('/(\d+)/', $rawKey, $matches);
        $sequence = isset($matches[1]) ? (int) $matches[1] : 1;

        $date = $tanggal instanceof \Illuminate\Support\Carbon
            ? $tanggal
            : \Illuminate\Support\Carbon::parse($tanggal);

        return sprintf('SJ-%03d/MAB/%s', max(1, $sequence), $date->format('d-m-Y'));
    }

}
