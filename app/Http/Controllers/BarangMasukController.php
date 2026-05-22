<?php

namespace App\Http\Controllers;

use App\Models\JenisBarang;
use App\Models\Material;
use App\Models\MaterialOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class BarangMasukController extends Controller
{
    private const AUTO_STATUS_MASUK = 'material';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangMasuks = MaterialOrder::with(['material', 'supplier', 'user'])
            ->latest('created_at')
            ->paginate(10);
        return view('barang-masuk.index', compact('barangMasuks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $barangSuggestions = Material::query()
            ->select(['id_material', 'kode', 'nama'])
            ->orderBy('nama')
            ->get();
        $suppliers = Supplier::all();

        return view('barang-masuk.create', compact('barangSuggestions', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'quantity' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'estimasi_tiba_display' => 'nullable|date',
            'invoice' => 'nullable|file|max:5120',
        ]);

        $materialJenis = JenisBarang::query()->firstOrCreate(
            ['nama' => 'Material'],
            ['deskripsi' => 'Jenis barang material']
        );

        $namaBarang = preg_replace('/\s+/', ' ', trim($validated['nama_barang']));
        $lowerNamaBarang = mb_strtolower($namaBarang);

        // Find or create material in modular table
        $material = Material::query()
            ->whereRaw("REGEXP_REPLACE(LOWER(nama), '\\s+', ' ', 'g') = ?", [$lowerNamaBarang])
            ->first();

        if (! $material) {
            $material = Material::create([
                'kode' => $this->generateKodeBarang(),
                'nama' => $namaBarang,
                'id_jenis_barang' => $materialJenis->id_jenis_barang,
                'quantity' => 0,
            ]);
        }

        $data = [
            'id_material' => $material->id_material,
            'id_supplier' => $validated['id_supplier'],
            'qty' => $validated['quantity'],
            'tgl_pemesanan' => $validated['tanggal_masuk'],
            'tgl_tiba_perkiraan' => $validated['estimasi_tiba_display'] ?? null,
            'id_user' => auth()->id(),
        ];

        if ($request->hasFile('invoice')) {
            $data['invoice_path'] = $request->file('invoice')->store('barang-masuk/invoice', 'public');
        }

        MaterialOrder::create($data);

        // Update material stock quantity
        $material->increment('quantity', $validated['quantity']);

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(MaterialOrder $barangMasuk)
    {
        $barangMasuk->load(['material', 'supplier', 'user']);
        return view('barang-masuk.show', compact('barangMasuk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MaterialOrder $barangMasuk)
    {
        $barangs = Material::query()->orderBy('nama')->get();
        $suppliers = Supplier::all();
        return view('barang-masuk.edit', compact('barangMasuk', 'barangs', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MaterialOrder $barangMasuk)
    {
        $validated = $request->validate([
            'id_barang' => 'required|exists:materials,id_material',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'tanggal_masuk' => 'required|date',
            'estimasi_tiba_display' => 'nullable|date',
            'invoice' => 'nullable|file|max:5120',
        ]);

        // Simpan id_material lama untuk update quantity
        $oldIdMaterial = $barangMasuk->id_material;
        $oldQty = $barangMasuk->qty;

        $data = [
            'id_material' => $validated['id_barang'],
            'id_supplier' => $validated['id_supplier'],
            'tgl_pemesanan' => $validated['tanggal_masuk'],
            'tgl_tiba_perkiraan' => $validated['estimasi_tiba_display'] ?? null,
        ];

        if ($request->hasFile('invoice')) {
            if ($barangMasuk->invoice_path) {
                Storage::disk('public')->delete($barangMasuk->invoice_path);
            }
            $data['invoice_path'] = $request->file('invoice')->store('barang-masuk/invoice', 'public');
        }

        $barangMasuk->update($data);

        // Update quantity di tabel material jika id_material berubah
        if ($oldIdMaterial !== $validated['id_barang']) {
            // Kurangi quantity dari material lama
            $oldMaterial = Material::findOrFail($oldIdMaterial);
            $oldMaterial->quantity = max(0, ($oldMaterial->quantity ?? 0) - $oldQty);
            $oldMaterial->save();

            // Tambah quantity ke material baru
            $newMaterial = Material::findOrFail($validated['id_barang']);
            $newMaterial->quantity = ($newMaterial->quantity ?? 0) + $oldQty;
            $newMaterial->save();
        }

        return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MaterialOrder $barangMasuk)
    {
        if ($barangMasuk->surat_jalan_path) {
            Storage::disk('public')->delete($barangMasuk->surat_jalan_path);
        }
        if ($barangMasuk->invoice_path) {
            Storage::disk('public')->delete($barangMasuk->invoice_path);
        }

        // Kurangi quantity dari material sebelum menghapus
        $material = Material::findOrFail($barangMasuk->id_material);
        $material->quantity = max(0, ($material->quantity ?? 0) - $barangMasuk->qty);
        $material->save();

        $barangMasuk->delete();

        return redirect()->route('persediaan-material.index')->with('success', 'Persediaan material berhasil dihapus');
    }

    // Persediaan Material Methods
    public function persediaanMaterialIndex()
    {
        $search = request('search');
        $tanggalDari = request('tanggal_dari');
        $tanggalSampai = request('tanggal_sampai');

        $query = MaterialOrder::query()
            ->with(['material', 'supplier', 'user'])
            ->latest('created_at');

        if ($search) {
    $search = strtolower($search);

    $query->where(function ($q) use ($search) {
        $q->whereHas('material', function ($q2) use ($search) {
            $q2->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"]);
        })
        ->orWhereHas('supplier', function ($q3) use ($search) {
            $q3->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"]);
        });
    });
}

        if ($tanggalDari) {
            $query->whereDate('tgl_pemesanan', '>=', $tanggalDari);
        }

        if ($tanggalSampai) {
            $query->whereDate('tgl_pemesanan', '<=', $tanggalSampai);
        }

        $persediaanMaterials = $query->paginate(10);

        return view('persediaan-material.index', compact('persediaanMaterials'));
    }

    public function persediaanMaterialCreate()
    {
        $materials = Material::query()->orderBy('nama')->get(['id_material', 'source_barang_id', 'kode', 'nama', 'ukuran']);
        $suppliers = Supplier::orderBy('nama')->get();
        $defaultEstimasiTiba = $this->resolveDefaultEstimasiTiba();
        $generatedNoPo = $this->generateNextNoPo(now()->toDateString());

        return view('persediaan-material.create', compact('materials', 'suppliers', 'defaultEstimasiTiba', 'generatedNoPo'));
    }

    public function persediaanMaterialStore(Request $request)
    {
        $validated = $request->validate([
            'source_barang_id' => 'required|exists:materials,source_barang_id',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|in:INCH,CM',
            'tanggal_masuk' => 'required|date',
            'estimasi_tiba_display' => 'nullable|date',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'surat_jalan_path' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
            'invoice_gambar' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        $material = Material::query()
            ->where('source_barang_id', $validated['source_barang_id'])
            ->firstOrFail();

        $data = [
            'id_material' => $material->id_material,
            'qty' => $validated['quantity'],
            'satuan' => $validated['satuan'],
            'tgl_pemesanan' => $validated['tanggal_masuk'],
            'estimasi_tiba' => $validated['estimasi_tiba_display'] ?? null,
            'id_supplier' => $validated['id_supplier'],
            'id_user' => auth()->id(),
            'status' => self::AUTO_STATUS_MASUK,
        ];

        if ($request->hasFile('surat_jalan_path')) {
            $data['surat_jalan_path'] = $request->file('surat_jalan_path')->store('persediaan-material/surat-jalan', 'public');
        }

        if ($request->hasFile('invoice_gambar')) {
            $data['invoice_path'] = $request->file('invoice_gambar')->store('persediaan-material/invoice', 'public');
        }

        $created = false;
        $attempt = 0;

        while (! $created && $attempt < 5) {
            $attempt++;
            $data['no_po'] = $this->generateNextNoPo($validated['tanggal_masuk']);

            try {
                MaterialOrder::create($data);
                $created = true;
            } catch (QueryException $exception) {
                if (! $this->isNoPoUniqueViolation($exception) || $attempt >= 5) {
                    throw $exception;
                }
            }
        }

        // Update quantity di data material
        $material->increment('quantity', $validated['quantity']);
        $material->update(['satuan' => $validated['satuan']]);

        return redirect()->route('persediaan-material.index')->with('success', 'Persediaan material berhasil ditambahkan');
    }

    public function persediaanMaterialShow(MaterialOrder $barangMasuk)
    {
        $barangMasuk->load(['material', 'supplier', 'user']);

        return view('persediaan-material.show', compact('barangMasuk'));
    }

    public function persediaanMaterialEdit(MaterialOrder $barangMasuk)
    {
        $materials = Material::query()->orderBy('nama')->get(['id_material', 'source_barang_id', 'kode', 'nama']);
        $suppliers = Supplier::orderBy('nama')->get();

        $barangMasuk->load('material');

        return view('persediaan-material.edit', compact('barangMasuk', 'materials', 'suppliers'));
    }

    public function persediaanMaterialUpdate(Request $request, MaterialOrder $barangMasuk)
    {
        $validated = $request->validate([
            'source_barang_id' => 'required|exists:materials,source_barang_id',
            'quantity' => 'required|integer|min:1',
            'satuan' => 'required|in:INCH,CM',
            'tanggal_masuk' => 'required|date',
            'estimasi_tiba_display' => 'nullable|date',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'surat_jalan_path' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
            'invoice_gambar' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        $material = Material::query()
            ->where('source_barang_id', $validated['source_barang_id'])
            ->firstOrFail();

        // Simpan file lama
        $oldSuratJalan = $barangMasuk->surat_jalan_path;
        $oldInvoice = $barangMasuk->invoice_path;

        // Upload file baru jika ada
        if ($request->hasFile('surat_jalan_path')) {
            $newSuratJalan = $request->file('surat_jalan_path')->store('persediaan-material/surat-jalan', 'public');
        }

        if ($request->hasFile('invoice_gambar')) {
            $newInvoice = $request->file('invoice_gambar')->store('persediaan-material/invoice', 'public');
        }

        // Hitung perubahan quantity untuk update di tabel barang
        $quantityDifference = $validated['quantity'] - $barangMasuk->qty;
        $oldMaterialId = $barangMasuk->id_material;

        // Update data - gunakan approach paling simple
        $barangMasuk->id_material = $material->id_material;
        $barangMasuk->qty = $validated['quantity'];
        $barangMasuk->satuan = $validated['satuan'];
        $barangMasuk->tgl_pemesanan = $validated['tanggal_masuk'];
        $barangMasuk->estimasi_tiba = $validated['estimasi_tiba_display'] ?? null;
        $barangMasuk->id_supplier = $validated['id_supplier'];

        if ($request->hasFile('surat_jalan_path')) {
            $barangMasuk->surat_jalan_path = $newSuratJalan;
        }

        if ($request->hasFile('invoice_gambar')) {
            $barangMasuk->invoice_path = $newInvoice;
        }

        // Save - ini HARUS berhasil atau throw error
        $barangMasuk->save();

        // Update quantity di tabel barang
        if ($oldMaterialId === $material->id_material) {
            // ID barang tidak berubah, hanya update quantity
            $material->quantity = ($material->quantity ?? 0) + $quantityDifference;
            $material->satuan = $validated['satuan'];
            $material->save();
        } else {
            // ID barang berubah, kurangi dari yang lama, tambah ke yang baru
            $oldMaterial = Material::findOrFail($oldMaterialId);
            $oldMaterial->quantity = max(0, ($oldMaterial->quantity ?? 0) - $barangMasuk->getOriginal('qty'));
            $oldMaterial->save();

            $material->quantity = ($material->quantity ?? 0) + $validated['quantity'];
            $material->satuan = $validated['satuan'];
            $material->save();
        }

        // Baru hapus file lama SETELAH save berhasil
        if ($request->hasFile('surat_jalan_path') && $oldSuratJalan) {
            Storage::disk('public')->delete($oldSuratJalan);
        }

        if ($request->hasFile('invoice_gambar') && $oldInvoice) {
            Storage::disk('public')->delete($oldInvoice);
        }

        return redirect()->route('persediaan-material.index')
            ->with('success', 'Persediaan material berhasil diperbarui');
    }

    private function resolveDefaultEstimasiTiba(): string
    {
        $hour = (int) now()->format('H');

        if ($hour < 12) {
            $daysToAdd = 1;
        } else {
            $daysToAdd = 2;
        }

        return now()->addDays($daysToAdd)->toDateString();
    }

    public function persediaanMaterialDestroy(MaterialOrder $barangMasuk)
    {
        \Log::warning('DESTROY CALLED', ['id' => $barangMasuk->id_pemesanan, 'data' => $barangMasuk->toArray()]);

        if ($barangMasuk->surat_jalan_path) {
            Storage::disk('public')->delete($barangMasuk->surat_jalan_path);
        }

        if ($barangMasuk->invoice_path) {
            Storage::disk('public')->delete($barangMasuk->invoice_path);
        }

        // Kurangi quantity dari tabel barang sebelum menghapus
        $material = Material::findOrFail($barangMasuk->id_material);
        $material->quantity = max(0, ($material->quantity ?? 0) - $barangMasuk->qty);
        $material->save();

        $barangMasuk->delete();

        \Log::warning('DESTROY COMPLETED', ['id' => $barangMasuk->id_pemesanan]);

        return redirect()->route('persediaan-material.index')->with('success', 'Persediaan material berhasil dihapus');
    }

    private function generateKodeBarang(): string
    {
        do {
            $kode = 'BRG-' . now()->format('ymd') . '-' . str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
        } while (Material::query()->where('kode', $kode)->exists());

        return $kode;
    }

    private function generateNextNoPo(string $tanggalPemesanan): string
    {
        $date = \Carbon\Carbon::parse($tanggalPemesanan);
        $month = $date->format('m');
        $year = $date->format('Y');

        $maxSeq = MaterialOrder::query()
            ->where('no_po', 'like', '%/MAB-PO/' . $month . '/' . $year)
            ->selectRaw("MAX(CAST(SPLIT_PART(no_po, '/', 1) AS INTEGER)) as max_seq")
            ->value('max_seq');

        $nextSeq = ((int) $maxSeq) + 1;

        return sprintf('%03d/MAB-PO/%s/%s', $nextSeq, $month, $year);
    }

    private function isNoPoUniqueViolation(QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return ($exception->getCode() === '23505' || str_contains($message, 'unique'))
            && str_contains($message, 'no_po');
    }

}
