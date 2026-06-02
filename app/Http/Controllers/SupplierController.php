<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $search = trim((string) request()->query('q', ''));

        $suppliersQuery = Supplier::query();

        if ($search !== '') {
            $suppliersQuery->whereRaw('LOWER(nama) LIKE ?', ['%' . mb_strtolower($search) . '%']);
        }

        $suppliers = $suppliersQuery->orderByDesc('updated_at')->get();
        return view('suppliers.index', compact('suppliers', 'search'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kontak' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $value = (string) $value;

                    if (!ctype_digit($value) || strlen($value) < 8 || strlen($value) > 13) {
                        $fail('Kontak harus berupa angka dengan minimal 8 digit dan maksimal 13 digit');
                    }
                },
            ],
            'email' => 'nullable|email',
        ]);

        if (!empty($validated['jabatan'])) {
            $validated['jabatan'] = mb_strtoupper($validated['jabatan']);
        }

        if (!empty($validated['kontak'])) {
            if (str_starts_with($validated['kontak'], '08')) {
                $validated['kontak'] = '62' . substr($validated['kontak'], 1);
            }
        }

        $validated['nama'] = mb_strtoupper($validated['nama']);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data supplier berhasil ditambahkan');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'kontak' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === null || $value === '') {
                        return;
                    }

                    $value = (string) $value;

                    if (!ctype_digit($value) || strlen($value) < 8 || strlen($value) > 13) {
                        $fail('Kontak harus berupa angka dengan minimal 8 digit dan maksimal 13 digit');
                    }
                },
            ],
            'email' => 'nullable|email',
        ]);

        if (!empty($validated['jabatan'])) {
            $validated['jabatan'] = mb_strtoupper($validated['jabatan']);
        }

        if (!empty($validated['kontak'])) {
            if (str_starts_with($validated['kontak'], '08')) {
                $validated['kontak'] = '62' . substr($validated['kontak'], 1);
            }
        }

        $validated['nama'] = mb_strtoupper($validated['nama']);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data supplier berhasil diubah');
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
        } catch (QueryException $exception) {
            if (($exception->errorInfo[0] ?? null) === '23503') {
                return redirect()
                    ->back()
                    ->with('error', 'Supplier tidak dapat dihapus karena masih dipakai pada transaksi persediaan material.');
            }

            throw $exception;
        }

        return redirect()->route('suppliers.index')->with('success', 'Data supplier berhasil dihapus');
    }
}
