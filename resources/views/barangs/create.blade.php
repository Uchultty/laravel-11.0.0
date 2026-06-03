<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ ($formMode ?? 'produk') === 'material' ? 'Tambah Material' : 'Tambah Produk' }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <style>
                .material-rows-scroll {
                    max-height: 320px;
                    overflow-y: auto;
                    padding-right: 6px;
                }

                .material-rows-scroll::-webkit-scrollbar {
                    width: 10px;
                }

                .material-rows-scroll::-webkit-scrollbar-track {
                    background: #e2e8f0;
                    border-radius: 999px;
                }

                .material-rows-scroll::-webkit-scrollbar-thumb {
                    background: #94a3b8;
                    border-radius: 999px;
                    border: 2px solid #e2e8f0;
                }

                .material-rows-scroll::-webkit-scrollbar-thumb:hover {
                    background: #64748b;
                }
            </style>
            <form action="{{ ($formMode ?? 'produk') === 'material' ? route('data-material.store') : route('data-produk.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="form_mode" value="{{ $formMode ?? 'produk' }}">

                @if(session('error'))
                    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                <div>
                    <label for="nama" class="ui-label">{{ ($formMode ?? 'produk') === 'material' ? 'Nama Material' : 'Nama Produk' }}</label>
                    <x-ui.input name="nama" id="nama" value="{{ old('nama') }}" required style="text-transform: uppercase" />
                    @error('nama') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="satuan" class="ui-label">Satuan</label>
                    <input type="hidden" name="satuan" value="mm">
                    <div class="mt-2 rounded-lg border border-sand-200 bg-sand-50 px-4 py-3 text-sm font-semibold text-ink-800">
                        MM
                    </div>
                    @error('satuan') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ukuran" class="ui-label">Ukuran</label>
                    <x-ui.input type="text" name="ukuran" id="ukuran" placeholder="Contoh: 5 mm / 5x5 mm / 244x122x1.2 mm" value="{{ old('ukuran', '') }}" required />
                    @error('ukuran') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                @if (($formMode ?? 'produk') === 'produk')
                    @php
                        $materialRows = old('materials', isset($productMaterials) && $productMaterials->count()
                            ? $productMaterials->map(fn ($material) => [
                                'id_material' => $material->id_material,
                            ])->values()->all()
                            : [['id_material' => '']]);
                    @endphp

                    <div class="rounded-2xl border border-sand-200 bg-sand-50/80 p-4 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-ink-900">Material Penyusun</p>
                                <p class="text-xs text-ink-500">Pilih material yang dipakai untuk produk ini dan jumlah pemakaiannya.</p>
                            </div>
                            <button type="button" id="addMaterialRow" class="rounded-lg bg-brand-600 px-3 py-2 text-xs font-semibold text-white hover:bg-brand-700">+ Tambah Material</button>
                        </div>

                        <x-modal name="duplicate-material-modal">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-ink-900">Material Duplikat</h3>
                                <p class="mt-2 text-sm text-ink-700">Material yang Anda pilih sudah ada di baris lain. Pilih material lain atau hapus salah satu.</p>

                                <div class="mt-4 flex justify-end gap-2">
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'duplicate-material-modal' }))" class="rounded-lg bg-brand-600 px-3 py-2 text-xs font-semibold text-white">OK</button>
                                </div>
                            </div>
                        </x-modal>

                        @if ($materials->isEmpty())
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                Belum ada material. Tambahkan material dulu sebelum menyimpan produk.
                            </div>
                        @endif

                        <div id="materialRows" class="material-rows-scroll space-y-3">
                            @foreach ($materialRows as $index => $row)
                                <div class="material-row grid grid-cols-1 gap-3 rounded-xl border border-sand-200 bg-white p-3 md:grid-cols-[minmax(0,1fr)_160px_auto] md:items-end">
                                    <div>
                                        <label class="ui-label material-select-label" for="materials_{{ $index }}_id_material">Material</label>
                                        <select name="materials[{{ $index }}][id_material]" id="materials_{{ $index }}_id_material" class="material-select w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-sm text-ink-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20" required>
                                            <option value="">Pilih material</option>
                                            @foreach ($materials as $material)
                                                <option value="{{ $material->id_material }}" @selected((string) ($row['id_material'] ?? '') === (string) $material->id_material)>
                                                    {{ $material->nama }} @if (filled($material->ukuran)) ({{ $material->ukuran }}) @elseif ($material->kode) ({{ $material->kode }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-1 md:flex md:items-end md:justify-end">
                                        <button type="button" class="remove-material-row rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('materials') <p class="ui-error">{{ $message }}</p> @enderror
                        @error('materials.*.id_material') <p class="ui-error">{{ $message }}</p> @enderror
                        @error('materials.*.quantity') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <template id="materialRowTemplate">
                        <div class="material-row grid grid-cols-1 gap-3 rounded-xl border border-sand-200 bg-white p-3 md:grid-cols-[minmax(0,1fr)_auto] md:items-end">
                            <div>
                                <label class="ui-label material-select-label">Material</label>
                                <select class="material-select w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-sm text-ink-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20" required>
                                    <option value="">Pilih material</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id_material }}">{{ $material->nama }} @if (filled($material->ukuran)) ({{ $material->ukuran }}) @elseif ($material->kode) ({{ $material->kode }}) @endif</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-1 md:flex md:items-end md:justify-end">
                                <button type="button" class="remove-material-row rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                            </div>
                        </div>
                    </template>
                @endif

                @if (($formMode ?? 'produk') === 'material')
                    <div>
                        <label for="stok_minimum" class="ui-label">Stok Minimum</label>
                        <x-ui.input type="number" name="stok_minimum" id="stok_minimum" value="{{ old('stok_minimum', 10) }}" min="0" required />
                        @error('stok_minimum') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>
                @endif

                <div class="ui-actions">
                    <x-ui.button type="submit">Simpan</x-ui.button>
                    <x-ui.button :href="($formMode ?? 'produk') === 'material' ? route('data-material.index') : route('data-produk.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    @if (($formMode ?? 'produk') === 'produk')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const duplicateMessage = 'Material yang dipilih tidak boleh sama.';
                const rowsContainer = document.getElementById('materialRows');
                const addButton = document.getElementById('addMaterialRow');
                const template = document.getElementById('materialRowTemplate');
                const form = rowsContainer?.closest('form');

                if (!rowsContainer || !addButton || !template || !form) {
                    return;
                }

                const updateDuplicateState = (shouldAlert = false) => {
                    const selects = Array.from(rowsContainer.querySelectorAll('.material-select'));
                    const counts = new Map();

                    selects.forEach((select) => {
                        const value = select.value;

                        if (!value) {
                            return;
                        }

                        counts.set(value, (counts.get(value) ?? 0) + 1);
                    });

                    let hasDuplicate = false;

                    selects.forEach((select) => {
                        const isDuplicate = Boolean(select.value) && (counts.get(select.value) ?? 0) > 1;

                        select.classList.toggle('border-red-500', isDuplicate);
                        select.classList.toggle('ring-2', isDuplicate);
                        select.classList.toggle('ring-red-500/20', isDuplicate);
                        select.setCustomValidity(isDuplicate ? duplicateMessage : '');

                        if (isDuplicate) {
                            hasDuplicate = true;
                        }
                    });

                    if (shouldAlert && hasDuplicate) {
                        window._lastDuplicateSelect = null; // ensure cleared; we'll set in change handler
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'duplicate-material-modal' }));
                    }

                    return hasDuplicate;
                };

                const syncRows = () => {
                    const rows = rowsContainer.querySelectorAll('.material-row');

                    rows.forEach((row, index) => {
                        const select = row.querySelector('.material-select');
                        const selectLabel = row.querySelector('.material-select-label');

                        if (select) {
                            select.name = `materials[${index}][id_material]`;
                            select.id = `materials_${index}_id_material`;
                        }

                        if (selectLabel && select) {
                            selectLabel.setAttribute('for', select.id);
                        }
                    });
                };

                addButton.addEventListener('click', () => {
                    const row = template.content.firstElementChild.cloneNode(true);
                    rowsContainer.appendChild(row);
                    syncRows();
                    updateDuplicateState(false);
                });

                rowsContainer.addEventListener('change', (event) => {
                    const select = event.target.closest('.material-select');

                    if (!select) {
                        return;
                    }

                    const selectedValue = select.value;
                    const sameValueCount = selectedValue
                        ? Array.from(rowsContainer.querySelectorAll('.material-select'))
                            .filter((item) => item.value === selectedValue).length
                        : 0;

                    if (sameValueCount > 1) {
                        // show modal and remember offending select so we can clear it when modal closes
                        window._lastDuplicateSelect = select;
                        window.dispatchEvent(new CustomEvent('open-modal', { detail: 'duplicate-material-modal' }));
                        return;
                    }

                    updateDuplicateState(false);
                });

                rowsContainer.addEventListener('click', (event) => {
                    const removeButton = event.target.closest('.remove-material-row');

                    if (!removeButton) {
                        return;
                    }

                    const rowCount = rowsContainer.querySelectorAll('.material-row').length;

                    if (rowCount === 1) {
                        const firstRow = rowsContainer.querySelector('.material-row');

                        if (firstRow) {
                            const select = firstRow.querySelector('.material-select');
                            const quantity = firstRow.querySelector('.material-quantity');

                            if (select) {
                                select.value = '';
                            }

                            if (quantity) {
                                quantity.value = '1';
                            }
                        }

                        return;
                    }

                    removeButton.closest('.material-row')?.remove();
                    syncRows();
                    updateDuplicateState(false);
                });

                form.addEventListener('submit', (event) => {
                    if (updateDuplicateState(false)) {
                        event.preventDefault();
                        updateDuplicateState(true);
                    }
                });
                // when modal is closed, clear and focus last offending select if any
                window.addEventListener('close-modal', (e) => {
                    if ((e?.detail ?? null) !== 'duplicate-material-modal') return;

                    if (window._lastDuplicateSelect) {
                        try {
                            window._lastDuplicateSelect.value = '';
                            window._lastDuplicateSelect.focus();
                        } catch (err) {
                            // ignore
                        }

                        window._lastDuplicateSelect = null;
                        updateDuplicateState(false);
                    }
                });

                syncRows();
                updateDuplicateState(false);
            });
        </script>

    @endif
</x-app-layout>
