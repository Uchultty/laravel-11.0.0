<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            Tambah Barang Dalam Proses
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <style>
                .process-detail-scroll {
                    max-height: 360px;
                    overflow-y: auto;
                    padding-right: 6px;
                }

                .process-detail-scroll::-webkit-scrollbar {
                    width: 10px;
                    height: 10px;
                }

                .process-detail-scroll::-webkit-scrollbar-track {
                    background: #e2e8f0;
                    border-radius: 999px;
                }

                .process-detail-scroll::-webkit-scrollbar-thumb {
                    background: #64748b;
                    border-radius: 999px;
                    border: 2px solid #e2e8f0;
                }

                .process-detail-scroll::-webkit-scrollbar-thumb:hover {
                    background: #475569;
                }
            </style>

            <form action="{{ route('barang-dalam-proses.store') }}" method="POST" class="space-y-6" novalidate>
                @csrf

                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
                    <h3 class="text-sm font-bold text-slate-900">Informasi Pesanan</h3>
                    <p class="mt-1 text-xs text-slate-500">Data ini berlaku untuk semua baris barang dalam tabel.</p>

                    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="id_customer" class="ui-label">Pelanggan</label>
                            <select id="id_customer" name="id_customer" class="ui-input" required>
                                <option value="" disabled {{ old('id_customer') ? '' : 'selected' }}>Pilih Pelanggan</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id_pelanggan }}" {{ old('id_customer') == $customer->id_pelanggan ? 'selected' : '' }}>{{ $customer->nama }}</option>
                                @endforeach
                            </select>
                            @error('id_customer') <p class="ui-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="no_po" class="ui-label">No PO</label>
                            <x-ui.input type="text" id="no_po" name="no_po" value="{{ old('no_po') }}" placeholder="Masukkan No PO" required />
                            @error('no_po') <p class="ui-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="tanggal_buat" class="ui-label">Tanggal Buat</label>
                            <x-ui.input type="date" id="tanggal_buat" name="tanggal_buat" value="{{ old('tanggal_buat', now()->format('Y-m-d')) }}" required />
                            @error('tanggal_buat') <p class="ui-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="tanggal_selesai" class="ui-label">Tanggal Selesai</label>
                            <x-ui.input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" />
                            @error('tanggal_selesai') <p class="ui-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Detail Barang</h3>
                            <p class="mt-1 text-xs text-slate-500">Tambah barang untuk beberapa barang dalam satu pesanan proses.</p>
                        </div>
                        <button type="button" id="addRowBtn" class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                            + Tambah Barang
                        </button>
                    </div>

                    <div class="process-detail-scroll rounded-xl border border-slate-200">
                        <table class="min-w-[1080px] w-full border-collapse text-sm">
                            <thead class="bg-slate-100 text-slate-700">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">No</th>
                                    <th class="px-3 py-2 text-left font-semibold">Produk</th>
                                    <th class="px-3 py-2 text-left font-semibold">No Gambar</th>
                                    <th class="px-3 py-2 text-left font-semibold">Material</th>
                                    <th class="px-3 py-2 text-left font-semibold">QTY</th>
                                    <th class="px-3 py-2 text-left font-semibold">Satuan</th>
                                    <th class="px-3 py-2 text-left font-semibold">Ukuran</th>
                                    <th class="px-3 py-2 text-center font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>

                    @if ($errors->has('items') || $errors->has('items.*'))
                        <p class="ui-error mt-2">Periksa detail barang. Pastikan semua kolom terisi dan stok material mencukupi.</p>
                    @endif
                </div>

                <template id="itemRowTemplate">
                    <tr class="item-row bg-white align-top">
                        <td class="px-3 py-2 text-slate-500 row-number"></td>
                        <td class="px-3 py-2 min-w-[260px]">
                            <select data-field="id_barang" class="ui-input product-select" required>
                                <option value="" selected>Pilih Produk</option>
                                @foreach ($barangs as $barang)
                                    <option value="{{ $barang->id_product }}" data-satuan="{{ strtolower($barang->satuan ?? 'mm') }}" data-ukuran="{{ $barang->ukuran ?? '' }}">
                                        {{ $barang->nama }} ({{ $barang->ukuran ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-3 py-2 min-w-[170px]">
                            <x-ui.input type="text" data-field="no_gambar" placeholder="No gambar" />
                        </td>
                        <td class="px-3 py-2 min-w-[260px]">
                            <select data-field="id_barang_mentah" class="ui-input material-select" required>
                                <option value="" selected>Pilih Material</option>
                                @foreach ($materials as $material)
                                    <option value="{{ $material->id_material }}" data-stock="{{ (int) $material->quantity }}">
                                        {{ filled($material->ukuran) ? $material->nama . ' - ' . $material->ukuran : $material->nama }} (Stok: {{ $material->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="material-warning mt-1 text-xs font-medium text-red-600 hidden">Stok material tidak cukup.</p>
                        </td>
                        <td class="px-3 py-2 min-w-[120px]">
                            <x-ui.input type="number" min="1" data-field="quantity" value="1" required />
                        </td>
                        <td class="px-3 py-2 min-w-[110px]">
                            <input type="hidden" data-field="satuan" value="mm">
                            <div class="unit-display rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-center text-xs font-semibold uppercase text-slate-700">MM</div>
                        </td>
                        <td class="px-3 py-2 min-w-[180px]">
                            <x-ui.input type="text" data-field="ukuran" placeholder="Ukuran" required />
                        </td>
                        <td class="px-3 py-2 text-center min-w-[90px]">
                            <button type="button" class="remove-row rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus</button>
                        </td>
                    </tr>
                </template>

                <div class="ui-actions">
                    <x-ui.button type="submit" id="submitBtn">Simpan</x-ui.button>
                    <x-ui.button :href="route('barang-dalam-proses.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemsBody = document.getElementById('itemsBody');
            const template = document.getElementById('itemRowTemplate');
            const addRowBtn = document.getElementById('addRowBtn');
            const submitBtn = document.getElementById('submitBtn');
            const oldItems = @json(old('items', []));

            const productMaterials = @json(\App\Models\Product::with('materials:id_material')->get()->map(function ($p) {
                return [
                    'id' => $p->id_product,
                    'materials' => $p->materials->pluck('id_material')->all(),
                ];
            }));

            const productMaterialsMap = {};
            productMaterials.forEach(function (item) {
                productMaterialsMap[item.id] = item.materials;
            });

            function collectMaterialOptions(materialSelect) {
                if (!materialSelect.dataset.originalOptions) {
                    const options = Array.from(materialSelect.options)
                        .filter(function (option) {
                            return option.value;
                        })
                        .map(function (option) {
                            return option.outerHTML;
                        });

                    materialSelect.dataset.originalOptions = JSON.stringify(options);
                }

                return JSON.parse(materialSelect.dataset.originalOptions || '[]');
            }

            function setMaterialOptions(row, preferredValue) {
                const productSelect = row.querySelector('[data-field="id_barang"]');
                const materialSelect = row.querySelector('[data-field="id_barang_mentah"]');
                const selectedProduct = productSelect.value;
                const savedOptions = collectMaterialOptions(materialSelect);
                const placeholder = '<option value="" selected>Pilih Material</option>';

                if (!selectedProduct) {
                    materialSelect.innerHTML = placeholder + savedOptions.join('');
                    materialSelect.value = preferredValue || '';
                    return;
                }

                const allowed = productMaterialsMap[selectedProduct] || [];
                let html = placeholder;

                allowed.forEach(function (materialId) {
                    const optionHtml = savedOptions.find(function (saved) {
                        return saved.includes('value="' + materialId + '"');
                    });

                    if (optionHtml) {
                        html += optionHtml;
                    }
                });

                materialSelect.innerHTML = html;

                if (preferredValue && allowed.includes(preferredValue)) {
                    materialSelect.value = preferredValue;
                }
            }

            function validateRow(row) {
                const materialSelect = row.querySelector('[data-field="id_barang_mentah"]');
                const quantityInput = row.querySelector('[data-field="quantity"]');
                const warningEl = row.querySelector('.material-warning');
                const selectedOption = materialSelect.options[materialSelect.selectedIndex];
                const stock = selectedOption ? Number(selectedOption.getAttribute('data-stock') || 0) : 0;
                const qty = Number(quantityInput.value || 0);
                const invalid = Boolean(materialSelect.value) && qty > stock;

                warningEl.classList.toggle('hidden', !invalid);
                quantityInput.classList.toggle('border-red-400', invalid);

                return !invalid;
            }

            function refreshSubmitState() {
                const rows = Array.from(itemsBody.querySelectorAll('.item-row'));
                const valid = rows.length > 0 && rows.every(function (row) {
                    return validateRow(row);
                });

                if (valid) {
                    submitBtn.removeAttribute('disabled');
                    submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                } else {
                    submitBtn.setAttribute('disabled', 'disabled');
                    submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                }
            }

            function renumberRows() {
                Array.from(itemsBody.querySelectorAll('.item-row')).forEach(function (row, index) {
                    row.querySelector('.row-number').textContent = String(index + 1);
                    row.querySelector('[data-field="id_barang"]').name = 'items[' + index + '][id_barang]';
                    row.querySelector('[data-field="no_gambar"]').name = 'items[' + index + '][no_gambar]';
                    row.querySelector('[data-field="id_barang_mentah"]').name = 'items[' + index + '][id_barang_mentah]';
                    row.querySelector('[data-field="quantity"]').name = 'items[' + index + '][quantity]';
                    row.querySelector('[data-field="satuan"]').name = 'items[' + index + '][satuan]';
                    row.querySelector('[data-field="ukuran"]').name = 'items[' + index + '][ukuran]';
                });
            }

            function bindRow(row) {
                const productSelect = row.querySelector('[data-field="id_barang"]');
                const materialSelect = row.querySelector('[data-field="id_barang_mentah"]');
                const quantityInput = row.querySelector('[data-field="quantity"]');
                const ukuranInput = row.querySelector('[data-field="ukuran"]');
                const satuanInput = row.querySelector('[data-field="satuan"]');
                const unitDisplay = row.querySelector('.unit-display');
                const removeBtn = row.querySelector('.remove-row');

                productSelect.addEventListener('change', function () {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    const ukuran = selectedOption ? selectedOption.getAttribute('data-ukuran') : '';
                    const satuan = selectedOption ? selectedOption.getAttribute('data-satuan') : 'mm';

                    if (ukuran) {
                        ukuranInput.value = ukuran;
                    }

                    satuanInput.value = satuan || 'mm';
                    unitDisplay.textContent = String(satuanInput.value || 'mm').toUpperCase();

                    setMaterialOptions(row, '');
                    refreshSubmitState();
                });

                materialSelect.addEventListener('change', refreshSubmitState);
                quantityInput.addEventListener('input', refreshSubmitState);

                removeBtn.addEventListener('click', function () {
                    const totalRows = itemsBody.querySelectorAll('.item-row').length;

                    if (totalRows === 1) {
                        return;
                    }

                    row.remove();
                    renumberRows();
                    refreshSubmitState();
                });
            }

            function fillRow(row, item) {
                const productSelect = row.querySelector('[data-field="id_barang"]');
                const noGambarInput = row.querySelector('[data-field="no_gambar"]');
                const materialSelect = row.querySelector('[data-field="id_barang_mentah"]');
                const quantityInput = row.querySelector('[data-field="quantity"]');
                const satuanInput = row.querySelector('[data-field="satuan"]');
                const unitDisplay = row.querySelector('.unit-display');
                const ukuranInput = row.querySelector('[data-field="ukuran"]');

                if (item.id_barang) {
                    productSelect.value = item.id_barang;
                    productSelect.dispatchEvent(new Event('change', { bubbles: true }));
                } else {
                    setMaterialOptions(row, '');
                }

                if (item.no_gambar) {
                    noGambarInput.value = item.no_gambar;
                }

                if (item.id_barang_mentah) {
                    setMaterialOptions(row, item.id_barang_mentah);
                    materialSelect.value = item.id_barang_mentah;
                }

                if (item.quantity) {
                    quantityInput.value = item.quantity;
                }

                if (item.satuan) {
                    satuanInput.value = item.satuan;
                    unitDisplay.textContent = String(item.satuan).toUpperCase();
                }

                if (item.ukuran) {
                    ukuranInput.value = item.ukuran;
                }
            }

            function addRow(item) {
                const row = template.content.firstElementChild.cloneNode(true);
                itemsBody.appendChild(row);
                bindRow(row);
                fillRow(row, item || {});
                renumberRows();
                refreshSubmitState();
            }

            addRowBtn.addEventListener('click', function () {
                addRow({});
            });

            if (Array.isArray(oldItems) && oldItems.length > 0) {
                oldItems.forEach(function (item) {
                    addRow(item || {});
                });
            } else {
                addRow({});
            }

            document.querySelector('form').addEventListener('submit', function (event) {
                const rows = Array.from(itemsBody.querySelectorAll('.item-row'));
                const valid = rows.length > 0 && rows.every(function (row) {
                    return validateRow(row);
                });

                if (!valid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</x-app-layout>
