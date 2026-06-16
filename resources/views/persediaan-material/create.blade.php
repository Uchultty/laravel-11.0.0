<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Tambah Pemesanan Material
            </h2>
        </div>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <style>
                .detail-rows-scroll {
                    max-height: 320px;
                    overflow-y: auto;
                    padding-right: 6px;
                }

                .detail-rows-scroll::-webkit-scrollbar {
                    width: 10px;
                }

                .detail-rows-scroll::-webkit-scrollbar-track {
                    background: #e2e8f0;
                    border-radius: 999px;
                }

                .detail-rows-scroll::-webkit-scrollbar-thumb {
                    background: #94a3b8;
                    border-radius: 999px;
                    border: 2px solid #e2e8f0;
                }

                .detail-rows-scroll::-webkit-scrollbar-thumb:hover {
                    background: #64748b;
                }
            </style>
            <form action="{{ route('persediaan-material.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Section: Informasi Pemesanan -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Pemesanan</h3>

                    @php
                        $detailRows = old('detail_materials', [['source_barang_id' => '', 'quantity' => '']]);
                    @endphp

                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Daftar Material</p>
                                <p class="text-xs text-slate-500">Tambahkan material lebih dari satu, lalu hapus baris yang tidak jadi dipakai.</p>
                            </div>
                            <button type="button" id="addDetailRow" class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                + Tambah Material
                            </button>
                        </div>

                        <div id="detailRows" class="detail-rows-scroll space-y-3">
                            @foreach ($detailRows as $index => $row)
                                <div class="detail-row grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-white p-3 md:grid-cols-[minmax(0,1fr)_160px_auto] md:items-end">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1" for="detail_materials_{{ $index }}_source_barang_id">Nama Material *</label>
                                        <select id="detail_materials_{{ $index }}_source_barang_id" name="detail_materials[{{ $index }}][source_barang_id]" required class="detail-material-select w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('detail_materials.' . $index . '.source_barang_id') border-red-500 @enderror">
                                            <option value="">Pilih Material</option>
                                            @foreach($materials as $material)
                                                <option value="{{ $material->source_barang_id }}" {{ (string) ($row['source_barang_id'] ?? '') === (string) $material->source_barang_id ? 'selected' : '' }}>
                                                    {{ filled($material->ukuran) ? $material->nama . ' - ' . $material->ukuran : $material->nama . ' (' . $material->kode . ')' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('detail_materials.' . $index . '.source_barang_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1" for="detail_materials_{{ $index }}_quantity">QTY *</label>
                                        <input type="number" id="detail_materials_{{ $index }}_quantity" name="detail_materials[{{ $index }}][quantity]" value="{{ $row['quantity'] ?? '' }}" min="1" required class="detail-qty w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('detail_materials.' . $index . '.quantity') border-red-500 @enderror">
                                        @error('detail_materials.' . $index . '.quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <button type="button" class="remove-detail-row inline-flex h-11 items-center justify-center rounded-lg border border-red-200 px-3 text-xs font-semibold text-red-600 hover:bg-red-50 md:self-end">
                                        ✕
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <p id="duplicateDetailAlert" class="hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                            Material yang dipilih sudah ada. Ganti manual supaya tidak dobel.
                        </p>

                        @error('detail_materials') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <template id="detailRowTemplate">
                        <div class="detail-row grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-white p-3 md:grid-cols-[minmax(0,1fr)_160px_auto] md:items-end">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Material *</label>
                                <select required class="detail-material-select w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                                    <option value="">Pilih Material</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->source_barang_id }}">
                                            {{ filled($material->ukuran) ? $material->nama . ' - ' . $material->ukuran : $material->nama . ' (' . $material->kode . ')' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">QTY *</label>
                                <input type="number" min="1" required class="detail-qty w-full h-11 rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
                            </div>

                            <button type="button" class="remove-detail-row inline-flex h-11 items-center justify-center rounded-lg border border-red-200 px-3 text-xs font-semibold text-red-600 hover:bg-red-50 md:self-end">
                                ✕
                            </button>
                        </div>
                    </template>

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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rowsContainer = document.getElementById('detailRows');
            const addButton = document.getElementById('addDetailRow');
            const template = document.getElementById('detailRowTemplate');
            const duplicateAlert = document.getElementById('duplicateDetailAlert');

            if (!rowsContainer || !addButton || !template || !duplicateAlert) {
                return;
            }

            const updateDuplicateState = () => {
                const selects = Array.from(rowsContainer.querySelectorAll('.detail-material-select'));
                const counts = new Map();

                selects.forEach((select) => {
                    if (!select.value) {
                        return;
                    }

                    counts.set(select.value, (counts.get(select.value) ?? 0) + 1);
                });

                let hasDuplicate = false;

                selects.forEach((select) => {
                    const isDuplicate = Boolean(select.value) && (counts.get(select.value) ?? 0) > 1;

                    select.classList.toggle('border-red-500', isDuplicate);
                    select.classList.toggle('ring-2', isDuplicate);
                    select.classList.toggle('ring-red-500/20', isDuplicate);
                    select.setCustomValidity(isDuplicate ? 'Material yang dipilih sudah ada.' : '');

                    if (isDuplicate) {
                        hasDuplicate = true;
                    }
                });

                duplicateAlert.classList.toggle('hidden', !hasDuplicate);

                return hasDuplicate;
            };

            const syncRows = () => {
                const rows = rowsContainer.querySelectorAll('.detail-row');

                rows.forEach((row, index) => {
                    const select = row.querySelector('.detail-material-select');
                    const qty = row.querySelector('.detail-qty');
                    const label = row.querySelector('label[for]');

                    if (select) {
                        select.name = `detail_materials[${index}][source_barang_id]`;
                        select.id = `detail_materials_${index}_source_barang_id`;
                    }

                    if (qty) {
                        qty.name = `detail_materials[${index}][quantity]`;
                        qty.id = `detail_materials_${index}_quantity`;
                    }

                    if (label && select) {
                        label.setAttribute('for', select.id);
                    }
                });
            };

            addButton.addEventListener('click', () => {
                const row = template.content.firstElementChild.cloneNode(true);
                rowsContainer.appendChild(row);
                syncRows();
                updateDuplicateState();
            });

            rowsContainer.addEventListener('change', (event) => {
                if (!event.target.closest('.detail-material-select')) {
                    return;
                }

                updateDuplicateState();
            });

            rowsContainer.addEventListener('click', (event) => {
                const removeButton = event.target.closest('.remove-detail-row');

                if (!removeButton) {
                    return;
                }

                const rowCount = rowsContainer.querySelectorAll('.detail-row').length;

                if (rowCount === 1) {
                    const firstRow = rowsContainer.querySelector('.detail-row');

                    if (firstRow) {
                        const select = firstRow.querySelector('.detail-material-select');
                        const qty = firstRow.querySelector('.detail-qty');

                        if (select) {
                            select.value = '';
                        }

                        if (qty) {
                            qty.value = '';
                        }
                    }

                    return;
                }

                removeButton.closest('.detail-row')?.remove();
                syncRows();
                updateDuplicateState();
            });

            rowsContainer.closest('form')?.addEventListener('submit', (event) => {
                if (updateDuplicateState()) {
                    event.preventDefault();
                }
            });

            syncRows();
            updateDuplicateState();
        });
    </script>
</x-app-layout>
