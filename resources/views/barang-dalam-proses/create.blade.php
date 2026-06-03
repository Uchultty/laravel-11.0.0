<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            Tambah Barang Dalam Proses
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('barang-dalam-proses.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar item proses</p>
                        <p class="text-xs text-slate-500">Tambahkan sebanyak yang diperlukan dalam satu submit.</p>
                    </div>
                    <button type="button" id="addRowBtn" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">+ Tambah Baris</button>
                </div>

                <div id="itemsContainer" class="space-y-4"></div>

                <template id="itemRowTemplate">
                    <div class="item-row rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <p class="row-title text-sm font-bold text-slate-900">Item 1</p>
                                <p class="text-xs text-slate-500">Isi data produk untuk baris ini.</p>
                            </div>
                            <button type="button" class="remove-row rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">Hapus Baris</button>
                        </div>

                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div>
                                <label class="ui-label">Nama Produk</label>
                                <select data-field="id_barang" class="ui-input product-select" required>
                                    <option value="" disabled selected>Pilih Produk</option>
                                    @foreach ($barangs as $barang)
                                        <option value="{{ $barang->id_product }}" data-satuan="{{ $barang->satuan ?? '' }}" data-ukuran="{{ $barang->ukuran ?? '' }}">{{ $barang->nama }} ({{ $barang->ukuran ?? '-' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="ui-label">No Gambar</label>
                                <x-ui.input type="text" data-field="no_gambar" placeholder="Masukkan No Gambar" />
                            </div>

                            <div>
                                <label class="ui-label">Satuan</label>
                                <input type="hidden" data-field="satuan" value="mm">
                                <div class="unit-display mt-2 rounded-lg border border-sand-200 bg-sand-50 px-4 py-3 text-sm font-semibold text-ink-800">MM</div>
                            </div>

                            <div>
                                <label class="ui-label">Ukuran</label>
                                <x-ui.input type="text" data-field="ukuran" placeholder="Contoh: 5 mm / 5x5 mm / 244x122x1.2 mm" required />
                            </div>

                            <div>
                                <label class="ui-label">Material/Bahan Baku</label>
                                <select data-field="id_barang_mentah" class="ui-input material-select" required>
                                    <option value="" disabled selected>Pilih Material</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id_material }}" data-stock="{{ (int) $material->quantity }}">{{ filled($material->ukuran) ? $material->nama . ' - ' . $material->ukuran : $material->nama }} (Stok: {{ $material->quantity }})</option>
                                    @endforeach
                                </select>
                                <p class="material-warning ui-error hidden">Bahan tidak cukup untuk membuat barang.</p>
                            </div>

                            <div>
                                <label class="ui-label">QTY</label>
                                <x-ui.input type="number" min="1" data-field="quantity" value="1" required />
                            </div>

                            <div>
                                <label class="ui-label">Pelanggan</label>
                                <select data-field="id_customer" class="ui-input" required>
                                    <option value="" disabled selected>Pilih Pelanggan</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id_customer }}">{{ $customer->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="ui-label">No PO</label>
                                <x-ui.input type="text" data-field="no_po" placeholder="Masukkan No PO" required />
                            </div>

                            <div>
                                <label class="ui-label">Tanggal Buat</label>
                                <x-ui.input type="date" data-field="tanggal_buat" value="{{ now()->format('Y-m-d') }}" required />
                            </div>

                            <div>
                                <label class="ui-label">Tanggal Selesai</label>
                                <x-ui.input type="date" data-field="tanggal_selesai" />
                            </div>
                        </div>
                    </div>
                </template>

                @error('items') <p class="ui-error">{{ $message }}</p> @enderror

                <div class="ui-actions">
                    <x-ui.button type="submit" id="submitBtn">Simpan</x-ui.button>
                    <x-ui.button :href="route('barang-dalam-proses.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemsContainer = document.getElementById('itemsContainer');
            const template = document.getElementById('itemRowTemplate');
            const addRowBtn = document.getElementById('addRowBtn');
            const submitBtn = document.getElementById('submitBtn');
            const oldItems = @json(old('items', []));

            const productMaterials = @json(\App\Models\Product::with('materials:id_material')->get()->map(function($p) {
                return [
                    'id' => $p->id_product,
                    'materials' => $p->materials->pluck('id_material')->all(),
                ];
            }));

            const productMaterialsMap = {};
            productMaterials.forEach(function(item) {
                productMaterialsMap[item.id] = item.materials;
            });

            function setMaterialOptions(row) {
                const productSelect = row.querySelector('[data-field="id_barang"]');
                const materialSelect = row.querySelector('[data-field="id_barang_mentah"]');
                const selectedProduct = productSelect.value;
                const originalOptions = Array.from(materialSelect.querySelectorAll('option')).filter(function(option) {
                    return option.value;
                }).map(function(option) {
                    return option.outerHTML;
                });

                if (!materialSelect.dataset.originalOptions) {
                    materialSelect.dataset.originalOptions = JSON.stringify(originalOptions);
                }

                const savedOptions = JSON.parse(materialSelect.dataset.originalOptions || '[]');
                const placeholder = '<option value="" disabled selected>Pilih Material</option>';

                if (!selectedProduct) {
                    materialSelect.innerHTML = placeholder + savedOptions.join('');
                    return;
                }

                const allowed = productMaterialsMap[selectedProduct] || [];
                let html = placeholder;

                allowed.forEach(function(materialId) {
                    const optionHtml = savedOptions.find(function(optionHtmlValue) {
                        return optionHtmlValue.includes('value="' + materialId + '"');
                    });

                    if (optionHtml) {
                        html += optionHtml;
                    }
                });

                materialSelect.innerHTML = html;
            }

            function validateRow(row) {
                const materialSelect = row.querySelector('[data-field="id_barang_mentah"]');
                const quantityInput = row.querySelector('[data-field="quantity"]');
                const warningEl = row.querySelector('.material-warning');
                const selectedOption = materialSelect.options[materialSelect.selectedIndex];
                const stock = selectedOption ? Number(selectedOption.getAttribute('data-stock') || 0) : 0;
                const qty = Number(quantityInput.value || 0);
                const isInvalid = Boolean(materialSelect.value) && qty > stock;

                warningEl.classList.toggle('hidden', !isInvalid);
                return !isInvalid;
            }

            function refreshSubmitState() {
                const rows = Array.from(itemsContainer.querySelectorAll('.item-row'));
                const allValid = rows.every(function(row) {
                    return validateRow(row);
                });

                if (allValid) {
                    submitBtn.removeAttribute('disabled');
                    submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                } else {
                    submitBtn.setAttribute('disabled', 'disabled');
                    submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
                }
            }

            function renumberRows() {
                Array.from(itemsContainer.querySelectorAll('.item-row')).forEach(function(row, index) {
                    row.querySelector('[data-field="id_barang"]').name = 'items[' + index + '][id_barang]';
                    row.querySelector('[data-field="no_gambar"]').name = 'items[' + index + '][no_gambar]';
                    row.querySelector('[data-field="satuan"]').name = 'items[' + index + '][satuan]';
                    row.querySelector('[data-field="ukuran"]').name = 'items[' + index + '][ukuran]';
                    row.querySelector('[data-field="id_barang_mentah"]').name = 'items[' + index + '][id_barang_mentah]';
                    row.querySelector('[data-field="quantity"]').name = 'items[' + index + '][quantity]';
                    row.querySelector('[data-field="id_customer"]').name = 'items[' + index + '][id_customer]';
                    row.querySelector('[data-field="no_po"]').name = 'items[' + index + '][no_po]';
                    row.querySelector('[data-field="tanggal_buat"]').name = 'items[' + index + '][tanggal_buat]';
                    row.querySelector('[data-field="tanggal_selesai"]').name = 'items[' + index + '][tanggal_selesai]';
                    row.querySelector('.row-title').textContent = 'Item ' + (index + 1);
                });
            }

            function populateRow(row, item) {
                const productSelect = row.querySelector('[data-field="id_barang"]');
                const noGambarInput = row.querySelector('[data-field="no_gambar"]');
                const satuanInput = row.querySelector('[data-field="satuan"]');
                const ukuranInput = row.querySelector('[data-field="ukuran"]');
                const materialSelect = row.querySelector('[data-field="id_barang_mentah"]');
                const quantityInput = row.querySelector('[data-field="quantity"]');
                const customerSelect = row.querySelector('[data-field="id_customer"]');
                const noPoInput = row.querySelector('[data-field="no_po"]');
                const tanggalBuatInput = row.querySelector('[data-field="tanggal_buat"]');
                const tanggalSelesaiInput = row.querySelector('[data-field="tanggal_selesai"]');

                if (item.id_barang) productSelect.value = item.id_barang;
                if (item.no_gambar) noGambarInput.value = item.no_gambar;
                if (item.satuan) satuanInput.value = item.satuan;
                if (item.ukuran) ukuranInput.value = item.ukuran;
                if (item.id_barang_mentah) materialSelect.value = item.id_barang_mentah;
                if (item.quantity) quantityInput.value = item.quantity;
                if (item.id_customer) customerSelect.value = item.id_customer;
                if (item.no_po) noPoInput.value = item.no_po;
                if (item.tanggal_buat) tanggalBuatInput.value = item.tanggal_buat;
                if (item.tanggal_selesai) tanggalSelesaiInput.value = item.tanggal_selesai;

                if (productSelect.value) {
                    productSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    materialSelect.value = item.id_barang_mentah || '';
                }

                validateRow(row);
            }

            function bindRow(row) {
                const productSelect = row.querySelector('[data-field="id_barang"]');
                const materialSelect = row.querySelector('[data-field="id_barang_mentah"]');
                const quantityInput = row.querySelector('[data-field="quantity"]');
                const removeBtn = row.querySelector('.remove-row');
                const ukuranInput = row.querySelector('[data-field="ukuran"]');
                const satuanInput = row.querySelector('[data-field="satuan"]');
                const unitDisplay = row.querySelector('.unit-display');

                if (productSelect && !productSelect.dataset.searchEnhanced) {
                    const searchInput = document.createElement('input');
                    searchInput.type = 'text';
                    searchInput.placeholder = 'Cari produk...';
                    searchInput.className = 'ui-input mb-2';
                    productSelect.parentElement.insertBefore(searchInput, productSelect);

                    searchInput.addEventListener('input', function() {
                        const query = searchInput.value.trim().toLowerCase();
                        const selectedValue = productSelect.value;

                        Array.from(productSelect.options).forEach(function(option, index) {
                            if (index === 0 || !option.value) {
                                option.hidden = false;
                                return;
                            }

                            const matches = option.text.toLowerCase().includes(query);
                            option.hidden = !matches && option.value !== selectedValue;
                        });
                    });

                    productSelect.dataset.searchEnhanced = '1';
                }

                productSelect.addEventListener('change', function() {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    const ukuran = selectedOption ? selectedOption.getAttribute('data-ukuran') : '';
                    const satuan = selectedOption ? selectedOption.getAttribute('data-satuan') : '';

                    if (ukuran) {
                        ukuranInput.value = ukuran;
                    }

                    if (satuan) {
                        satuanInput.value = satuan;
                        unitDisplay.textContent = String(satuan).toUpperCase();
                    }

                    setMaterialOptions(row);
                    refreshSubmitState();
                });

                materialSelect.addEventListener('change', refreshSubmitState);
                quantityInput.addEventListener('input', refreshSubmitState);

                removeBtn.addEventListener('click', function() {
                    if (itemsContainer.querySelectorAll('.item-row').length === 1) {
                        return;
                    }

                    row.remove();
                    renumberRows();
                    refreshSubmitState();
                });

                setMaterialOptions(row);
                validateRow(row);
            }

            function addRow(item = {}) {
                const row = template.content.firstElementChild.cloneNode(true);
                itemsContainer.appendChild(row);
                bindRow(row);
                renumberRows();
                populateRow(row, item);
                refreshSubmitState();
            }

            addRowBtn.addEventListener('click', addRow);

            if (oldItems.length > 0) {
                oldItems.forEach(function(item) {
                    addRow(item || {});
                });
            } else {
                addRow();
            }

            itemsContainer.addEventListener('input', refreshSubmitState);
            itemsContainer.addEventListener('change', refreshSubmitState);

            document.querySelector('form').addEventListener('submit', function(event) {
                const rows = Array.from(itemsContainer.querySelectorAll('.item-row'));
                const allValid = rows.every(function(row) {
                    return validateRow(row);
                });

                if (!allValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</x-app-layout>
