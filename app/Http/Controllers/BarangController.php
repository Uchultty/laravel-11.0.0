<?php
# This controller manages the CRUD operations for Barang (Data Produk) and also includes methods to list, create, update, and delete Barang. The index method provides a paginated list of barang with search and filter functionality, as well as summary statistics about the stock of the barang.
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Material;
use App\Models\JenisBarang;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $jenisBarangFilter = (string) $request->query('id_jenis_barang', 'all');
        $jenisBarangs = JenisBarang::query()->orderBy('nama')->get(['id_jenis_barang', 'nama']);
        $stokMinimumBatas = 10;

        // Determine if this is data-produk or data-material route
        $isProduk = request()->routeIs('data-produk.*');
        $pageTitle = $isProduk ? 'Master Data Produk' : 'Master Data Material';

        if ($jenisBarangFilter !== 'all' && ! $jenisBarangs->contains('id_jenis_barang', (int) $jenisBarangFilter)) {
            $jenisBarangFilter = 'all';
        }

        if ($isProduk) {
            $barangsQuery = Product::query()
                ->with(['jenisBarang:id_jenis_barang,nama'])
                ->select('products.*')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('products.kode', 'ilike', '%' . $search . '%')
                            ->orWhere('products.nama', 'ilike', '%' . $search . '%');
                    });
                })
                ->when($jenisBarangFilter !== 'all', function ($query) use ($jenisBarangFilter) {
                    $query->where('products.id_jenis_barang', (int) $jenisBarangFilter);
                })
                ->latest('products.created_at');
        } else {
            $barangsQuery = Material::query()
                ->with(['jenisBarang:id_jenis_barang,nama'])
                ->select('materials.*')
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('materials.kode', 'ilike', '%' . $search . '%')
                            ->orWhere('materials.nama', 'ilike', '%' . $search . '%');
                    });
                })
                ->when($jenisBarangFilter !== 'all', function ($query) use ($jenisBarangFilter) {
                    $query->where('materials.id_jenis_barang', (int) $jenisBarangFilter);
                })
                ->latest('materials.created_at');
        }

        $barangs = $barangsQuery->paginate(10)->withQueryString();

        $barangs->getCollection()->transform(function ($barang) use ($isProduk) {
            if ($isProduk) {
                $barang->total_masuk = 0;
                $barang->total_keluar = 0;
                $barang->current_quantity = 0;
                $barang->display_quantity = 0;
            } else {
                $barang->current_quantity = (int) $barang->quantity;
                $barang->display_quantity = $barang->current_quantity;
            }

            return $barang;
        });

        $materialItems = $barangsQuery->get();
        $summaryTotalItem = $materialItems->count();
        if ($isProduk) {
            $summaryTotalStok = 0;
            $summaryStokMinimum = 0;
        } else {
            $summaryTotalStok = $materialItems->sum(function ($barang): int {
                return (int) ($barang->quantity ?? 0);
            });
            $summaryStokMinimum = $materialItems->filter(function ($barang) use ($stokMinimumBatas): bool {
                $stokAkhir = (int) ($barang->quantity ?? 0);
                return $stokAkhir <= $stokMinimumBatas;
            })->count();
        }

        return view('barangs.index', compact(
            'barangs',
            'search',
            'jenisBarangFilter',
            'jenisBarangs',
            'summaryTotalItem',
            'summaryTotalStok',
            'summaryStokMinimum',
            'stokMinimumBatas',
            'pageTitle',
            'isProduk'
        ));
    }

    public function create(Request $request)
    {
        $formMode = $this->resolveFormMode($request);

        return view('barangs.create', compact('formMode'));
    }

    public function store(Request $request)
    {
        $formMode = $this->resolveFormMode($request);
        $targetTable = $formMode === 'material' ? 'materials' : 'products';

        if ($formMode === 'material') {
            $validated = $request->validate([
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique($targetTable, 'nama'),
                ],
                'satuan' => 'required|in:mm,inch',
                'stok_minimum' => 'required|integer|min:0',
                'ukuran' => [
                    'required',
                    'string',
                    'max:100',
                ],
            ]);
        } else {
            $validated = $request->validate([
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique($targetTable, 'nama'),
                ],
                'satuan' => 'required|in:mm,inch',
                'ukuran' => [
                    'required',
                    'string',
                    'max:100',
                ],
            ]);
        }

        $data = [
            'kode' => $this->generateKodeByMode($formMode),
            'nama' => $validated['nama'],
            'id_jenis_barang' => $this->resolveJenisBarangId($formMode),
            'satuan' => $validated['satuan'],
            'ukuran' => $validated['ukuran'],
        ];

        $data['source_barang_id'] = $data['kode'];

        if ($formMode === 'material') {
            $data['stok_minimum'] = (int) $validated['stok_minimum'];
            Material::create($data);
        } else {
            Product::create($data);
        }

        if ($formMode === 'material') {
            return redirect()
                ->route('data-material.index')
                ->with('success', 'Material berhasil ditambah');
        } else {
            return redirect()
                ->route('data-produk.index')
                ->with('success', 'Produk berhasil ditambahkan');
        }
    }

    public function show(Request $request, string $barang)
    {
        $formMode = $this->resolveFormMode($request);
        $barang = $this->findBarangByMode($formMode, $barang);

        return view('barangs.show', compact('barang'));
    }

    public function edit(Request $request, string $barang)
    {
        $formMode = $this->resolveFormMode($request, $request->routeIs('data-material.*') ? 'material' : 'produk');
        $barang = $this->findBarangByMode($formMode, $barang);
        return view('barangs.edit', compact('barang', 'formMode'));
    }

    public function update(Request $request, string $barang)
    {
        $formMode = $this->resolveFormMode($request, $request->routeIs('data-material.*') ? 'material' : 'produk');
        $barangModel = $this->findBarangByMode($formMode, $barang);
        $targetTable = $formMode === 'material' ? 'materials' : 'products';

        if ($formMode === 'material') {
            $validated = $request->validate([
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique($targetTable, 'nama')->ignore($barangModel->getKey(), $barangModel->getKeyName()),
                ],
                'satuan' => 'required|in:mm,inch',
                'stok_minimum' => 'required|integer|min:0',
                'ukuran' => [
                    'required',
                    'string',
                    'max:100',
                ],
            ]);
        } else {
            $validated = $request->validate([
                'nama' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique($targetTable, 'nama')->ignore($barangModel->getKey(), $barangModel->getKeyName()),
                ],
                'satuan' => 'required|in:mm,inch',
                'ukuran' => [
                    'required',
                    'string',
                    'max:100',
                ],
            ]);
        }

        $data = [
            'nama' => $validated['nama'],
            'id_jenis_barang' => $this->resolveJenisBarangId($formMode),
            'satuan' => $validated['satuan'],
            'ukuran' => $validated['ukuran'],
        ];

        if ($formMode === 'material') {
            $data['stok_minimum'] = (int) $validated['stok_minimum'];
        }

        $barangModel->update($data);

        if ($formMode === 'material') {
            return redirect()
                ->route('data-material.index')
                ->with('success', 'Material berhasil diperbarui');
        } else {
            return redirect()
                ->route('data-produk.index')
                ->with('success', 'Produk berhasil diperbarui');
        }
    }

    public function destroy(Request $request, string $barang)
    {
        $formMode = $this->resolveFormMode($request, $request->routeIs('data-material.*') ? 'material' : 'produk');
        $barangModel = $this->findBarangByMode($formMode, $barang);

        try {
            $barangModel->delete();
        } catch (QueryException $exception) {
            if (($exception->errorInfo[0] ?? null) === '23503') {
                return redirect()
                    ->back()
                    ->with('error', $formMode === 'material'
                        ? 'Material tidak dapat dihapus karena masih dipakai pada transaksi (mis. pemesanan/proses).'
                        : 'Produk tidak dapat dihapus karena masih dipakai pada transaksi (mis. proses/pengiriman).');
            }

            throw $exception;
        }

        if ($formMode === 'material') {
            return redirect()
                ->route('data-material.index')
                ->with('success', 'Material berhasil dihapus');
        } else {
            return redirect()
                ->route('data-produk.index')
                ->with('success', 'Produk berhasil dihapus');
        }
    }

    private function generateKodeByMode(string $formMode): string
    {
        $prefix = $formMode === 'material' ? 'MAT-' : 'PRD-';
        $query = $formMode === 'material' ? Material::query() : Product::query();

        do {
            $kode = $prefix . strtoupper(Str::random(8));
        } while ($query->where('kode', $kode)->exists());

        return $kode;
    }

    private function resolveJenisBarangId(string $formMode): int
    {
        $jenisBarangName = $formMode === 'material' ? 'MATERIAL' : 'PRODUK';

        return (int) JenisBarang::query()->firstOrCreate(
            ['nama' => $jenisBarangName],
            ['deskripsi' => $formMode === 'material' ? 'Jenis barang material' : 'Jenis barang produk']
        )->id_jenis_barang;
    }

    private function findBarangByMode(string $formMode, string $id): Material|Product
    {
        return $formMode === 'material'
            ? Material::query()->findOrFail($id)
            : Product::query()->findOrFail($id);
    }

    private function resolveFormMode(Request $request, ?string $fallback = null): string
    {
        $routeMode = (string) $request->route('form_mode', '');
        $mode = strtolower((string) $request->input('form_mode', $request->query('form_mode', $routeMode !== '' ? $routeMode : ($fallback ?? 'produk'))));

        return $mode === 'material' ? 'material' : 'produk';
    }
}
