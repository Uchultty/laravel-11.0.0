<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Edit Persediaan Material
            </h2>
        </div>
    </x-slot>

    <div class="ui-page">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm font-medium text-red-800 mb-2">Error:</p>
                <ul class="text-sm text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <x-ui.card>
            <form action="{{ route('persediaan-material.update', $barangMasuk) }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-2xl">
                @csrf
                @method('PUT')

                <div class="border-b border-ink-100 pb-6">
                    <h3 class="mb-4 text-lg font-semibold text-ink-900">Informasi Persediaan</h3>

                    <div>
                        <label for="source_barang_id" class="block text-sm font-medium text-ink-700 mb-1">Nama Material *</label>
                        <select id="source_barang_id" name="source_barang_id" required class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 @error('source_barang_id') border-red-500 @enderror">
                            <option value="">Pilih Material</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->source_barang_id }}" {{ old('source_barang_id', $barangMasuk->material?->source_barang_id) == $material->source_barang_id ? 'selected' : '' }}>
                                    {{ $material->nama }} ({{ $material->kode }})
                                </option>
                            @endforeach
                        </select>
                        @error('source_barang_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label for="quantity" class="block text-sm font-medium text-ink-700 mb-1">QTY *</label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', $barangMasuk->qty) }}" min="1" required class="w-full rounded-lg border border-ink-200 px-3 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 @error('quantity') border-red-500 @enderror">
                        @error('quantity') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label for="no_po" class="block text-sm font-medium text-ink-700 mb-1">No PO</label>
                        <input type="text" id="no_po" name="no_po" value="{{ $barangMasuk->no_po ?? '-' }}" readonly class="w-full rounded-lg border border-ink-200 bg-ink-50 px-3 py-2.5 text-sm text-ink-700 focus:outline-none">
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-ink-700 mb-2">Satuan *</label>
                        <input type="hidden" name="satuan" value="CM">
                        <div class="rounded-lg border border-ink-200 bg-ink-50 px-4 py-3 text-sm font-semibold text-ink-800">
                            CM
                        </div>
                        @error('satuan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label for="tanggal_masuk" class="block text-sm font-medium text-ink-700 mb-1">Waktu Pemesanan *</label>
                        <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', $barangMasuk->tgl_pemesanan?->format('Y-m-d')) }}" required class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 @error('tanggal_masuk') border-red-500 @enderror">
                        @error('tanggal_masuk') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label for="estimasi_tiba_display" class="block text-sm font-medium text-ink-700 mb-1">Estimasi Tiba</label>
                        <input type="date" id="estimasi_tiba_display" name="estimasi_tiba_display" value="{{ old('estimasi_tiba_display', $barangMasuk->estimasi_tiba?->format('Y-m-d')) }}" class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 @error('estimasi_tiba_display') border-red-500 @enderror">
                        @error('estimasi_tiba_display') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label for="id_supplier" class="block text-sm font-medium text-ink-700 mb-1">Supplier *</label>
                        <select id="id_supplier" name="id_supplier" required class="w-full rounded-lg border border-ink-200 bg-white px-3 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 @error('id_supplier') border-red-500 @enderror">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id_supplier }}" {{ old('id_supplier', $barangMasuk->id_supplier) == $supplier->id_supplier ? 'selected' : '' }}>
                                    {{ $supplier->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_supplier') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="border-b border-ink-100 pb-6">
                    <h3 class="mb-4 text-lg font-semibold text-ink-900">File Pendukung</h3>

                    <div class="mb-4">
                        <label for="surat_jalan_path" class="block text-sm font-medium text-ink-700 mb-1">Surat Jalan</label>
                        @if($barangMasuk->surat_jalan_path)
                            <div class="mb-3 rounded-lg bg-blue-50 p-3">
                                <p class="text-sm text-blue-700">
                                    File saat ini:
                                    <a href="{{ \Storage::url($barangMasuk->surat_jalan_path) }}" target="_blank" class="font-medium underline">Lihat File</a>
                                </p>
                            </div>
                        @endif
                        <input type="file" id="surat_jalan_path" name="surat_jalan_path" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-ink-200 px-3 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 @error('surat_jalan_path') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-ink-500">Format: PDF, JPG, PNG (Max 5MB) - Kosongkan jika tidak ingin mengubah</p>
                        @error('surat_jalan_path') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="invoice_gambar" class="block text-sm font-medium text-ink-700 mb-1">Gambar Invoice</label>
                        @if($barangMasuk->invoice_path)
                            <div class="mb-3 rounded-lg bg-blue-50 p-3">
                                <p class="text-sm text-blue-700">
                                    File saat ini:
                                    <a href="{{ \Storage::url($barangMasuk->invoice_path) }}" target="_blank" class="font-medium underline">Lihat File</a>
                                </p>
                            </div>
                        @endif
                        <input type="file" id="invoice_gambar" name="invoice_gambar" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-ink-200 px-3 py-2.5 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 @error('invoice_gambar') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-ink-500">Format: JPG, PNG, PDF (Max 5MB) - Kosongkan jika tidak ingin mengubah</p>
                        @error('invoice_gambar') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" name="action" value="update" class="inline-flex items-center gap-2 rounded-lg bg-blue-500 px-4 py-2.5 text-white font-medium hover:bg-blue-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update
                    </button>
                    <a href="{{ route('persediaan-material.index') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-500 px-4 py-2.5 text-white font-medium hover:bg-slate-600 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
