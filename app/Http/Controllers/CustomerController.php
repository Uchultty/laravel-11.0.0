<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $search = trim((string) request()->query('q', ''));

        $customersQuery = Customer::query();

        if ($search !== '') {
            $customersQuery->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($search) . '%']);
        }

        $customers = $customersQuery->orderByDesc('updated_at')->paginate(10)->withQueryString();

        return view('pelanggan.index', compact('customers', 'search'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:pelanggan,nama',
            'pic' => 'nullable|string|max:100',
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
            'email' => 'nullable|email|max:100',
        ]);

        if (!empty($validated['pic'])) {
            $validated['pic'] = mb_strtoupper($validated['pic']);
        }

        if (!empty($validated['kontak']) && str_starts_with($validated['kontak'], '08')) {
            $validated['kontak'] = '62' . substr($validated['kontak'], 1);
        }

        Customer::create($validated);

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil ditambahkan');
    }

    public function show(Customer $pelanggan)
    {
        return view('pelanggan.show', compact('pelanggan'));
    }

    public function edit(Customer $pelanggan)
    {
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, Customer $pelanggan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:pelanggan,nama,' . $pelanggan->id_pelanggan . ',id_pelanggan',
            'pic' => 'nullable|string|max:100',
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
            'email' => 'nullable|email|max:100',
        ]);

        if (!empty($validated['pic'])) {
            $validated['pic'] = mb_strtoupper($validated['pic']);
        }

        if (!empty($validated['kontak']) && str_starts_with($validated['kontak'], '08')) {
            $validated['kontak'] = '62' . substr($validated['kontak'], 1);
        }

        $pelanggan->update($validated);

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diubah');
    }

    public function destroy(Customer $pelanggan)
    {
        if (
            $pelanggan->productionItems()->exists() ||
            $pelanggan->shipments()->exists()
        ) {
            return redirect()
                ->back()
                ->with('error', 'Pelanggan tidak dapat dihapus karena masih dipakai pada transaksi.');
        }

        try {
            $pelanggan->delete();
        } catch (QueryException $exception) {
            if (($exception->errorInfo[0] ?? null) === '23503') {
                return redirect()
                    ->back()
                    ->with('error', 'Pelanggan tidak dapat dihapus karena masih dipakai pada transaksi.');
            }

            throw $exception;
        }

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil dihapus');
    }
}
