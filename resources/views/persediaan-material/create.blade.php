<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Tambah Persediaan Material
            </h2>
        </div>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('persediaan-material.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-2xl">
                @csrf

                <!-- Section: Informasi Persediaan -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Persediaan</h3>

                    <!-- Nama Material -->
                    <div>
                        <label for="source_barang_id" class="block text-sm font-medium text-slate-700 mb-1">Nama Material *</label>
                        <select id="source_barang_id" name="source_barang_id" required class="w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('source_barang_id') border-red-500 @enderror">
                            <option value="">Pilih Material</option>
                            @foreach($materials as $material)
                                <option value="{{ $material->source_barang_id }}" {{ old('source_barang_id') == $material->source_barang_id ? 'selected' : '' }}>
                                    {{ filled($material->ukuran) ? $material->nama . ' - ' . $material->ukuran : $material->nama . ' (' . $material->kode . ')' }}
                                </option>
                            @endforeach
                        </select>
                        @error('source_barang_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- QTY -->
                    <div class="mt-4">
                        <label for="quantity" class="block text-sm font-medium text-slate-700 mb-1">QTY *</label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required class="w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('quantity') border-red-500 @enderror">
                        @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-4">
                        <label for="no_po" class="block text-sm font-medium text-slate-700 mb-1">No PO</label>
                        <input type="text" id="no_po" name="no_po" value="{{ old('no_po', $generatedNoPo) }}" readonly class="w-full h-11 rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 text-slate-700 focus:outline-none">
                        <p class="mt-1 text-xs text-slate-500">No PO digenerate otomatis oleh sistem.</p>
                    </div>

                    <!-- Satuan -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Satuan *</label>
                        <input type="hidden" name="satuan" value="MM">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-800">
                            MM
                        </div>
                        @error('satuan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Pemesanan -->
                    <div class="mt-4">
                        <label for="tanggal_masuk" class="block text-sm font-medium text-slate-700 mb-1">Waktu Pemesanan *</label>
                        <input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', now()->format('Y-m-d')) }}" required class="w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('tanggal_masuk') border-red-500 @enderror">
                        @error('tanggal_masuk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Estimasi Tiba -->
                    <div class="mt-4">
                        <label for="estimasi_tiba_display" class="block text-sm font-medium text-slate-700 mb-1">Estimasi Tiba</label>
                        <input type="date" id="estimasi_tiba_display" name="estimasi_tiba_display" value="{{ old('estimasi_tiba_display', $defaultEstimasiTiba ?? now()->addDay()->format('Y-m-d')) }}" class="w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('estimasi_tiba_display') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-slate-500">Otomatis mengikuti WIB: sebelum jam 12:00 H+1, setelah jam 12:00 H+2.</p>
                        @error('estimasi_tiba_display') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Supplier -->
                    <div class="mt-4">
                        <label for="id_supplier" class="block text-sm font-medium text-slate-700 mb-1">Supplier *</label>
                        <select id="id_supplier" name="id_supplier" required class="w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('id_supplier') border-red-500 @enderror">
                            <option value="">Pilih Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id_supplier }}" {{ old('id_supplier') == $supplier->id_supplier ? 'selected' : '' }}>
                                    {{ $supplier->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_supplier') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Section: File Pendukung -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">File Pendukung</h3>

                    <!-- Surat Jalan -->
                    <div class="mb-4">
                        <label for="surat_jalan_path" class="block text-sm font-medium text-slate-700 mb-1">Surat Jalan</label>
                        <input type="file" id="surat_jalan_path" name="surat_jalan_path" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('surat_jalan_path') border-red-500 @enderror">
                        <p class="text-xs text-slate-500 mt-1">Format: PDF, JPG, PNG (Max 5MB)</p>
                        @error('surat_jalan_path') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Invoice Gambar -->
                    <div>
                        <label for="invoice_gambar" class="block text-sm font-medium text-slate-700 mb-1">Gambar Invoice</label>
                        <input type="file" id="invoice_gambar" name="invoice_gambar" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('invoice_gambar') border-red-500 @enderror">
                        <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, PDF (Max 5MB)</p>
                        @error('invoice_gambar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="ui-actions">
                    <x-ui.button type="submit">Simpan</x-ui.button>
                    <x-ui.button :href="route('persediaan-material.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
