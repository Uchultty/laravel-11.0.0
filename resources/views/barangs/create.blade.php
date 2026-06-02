<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ ($formMode ?? 'produk') === 'material' ? 'Tambah Material' : 'Tambah Produk' }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ ($formMode ?? 'produk') === 'material' ? route('data-material.store') : route('data-produk.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="form_mode" value="{{ $formMode ?? 'produk' }}">

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

                        @if ($materials->isEmpty())
                            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                                Belum ada material. Tambahkan material dulu sebelum menyimpan produk.
                            </div>
                        @endif

                        <div id="materialRows" class="space-y-3">
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
                const rowsContainer = document.getElementById('materialRows');
                const addButton = document.getElementById('addMaterialRow');
                const template = document.getElementById('materialRowTemplate');

                if (!rowsContainer || !addButton || !template) {
                    return;
                }

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
                });

                syncRows();
            });
        </script>
    @endif
</x-app-layout>
