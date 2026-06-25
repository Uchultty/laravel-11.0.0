<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            Edit Barang Dalam Proses
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('barang-dalam-proses.update', $barangDalamProses) }}" method="POST" class="space-y-4" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <label for="id_barang" class="ui-label">Nama Produk</label>
                    <select id="id_barang" name="id_barang" class="ui-input" required onchange="updateUkuranAndSatuan()">
                        <option value="" disabled>Pilih Produk</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id_product }}" data-satuan="{{ strtolower($barang->satuan ?? 'mm') }}" data-ukuran="{{ $barang->ukuran ?? '' }}" {{ old('id_barang', $barangDalamProses->id_produk) == $barang->id_product ? 'selected' : '' }}>
                                {{ $barang->nama }} ({{ $barang->ukuran ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_barang') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_gambar" class="ui-label">No Gambar</label>
                    <x-ui.input type="text" name="no_gambar" id="no_gambar" value="{{ old('no_gambar', $barangDalamProses->no_gambar ?? '') }}" placeholder="Masukkan No Gambar" />
                    @error('no_gambar') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="satuan" class="ui-label">Satuan</label>
                    <input type="hidden" id="satuan" name="satuan" value="{{ old('satuan', strtolower($barangDalamProses->satuan ?? 'mm')) }}">
                    <div id="satuanDisplay" class="mt-2 rounded-lg border border-sand-200 bg-sand-50 px-4 py-3 text-sm font-semibold uppercase text-ink-800">
                        {{ strtoupper(old('satuan', $barangDalamProses->satuan ?? 'mm')) }}
                    </div>
                    @error('satuan') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="ukuran" class="ui-label">Ukuran</label>
                    <x-ui.input type="text" name="ukuran" id="ukuran" placeholder="Contoh: 5 mm / 5x5 mm / 244x122x1.2 mm" value="{{ old('ukuran', $barangDalamProses->ukuran ?? '') }}" required />
                    @error('ukuran') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="id_barang_mentah" class="ui-label">Material/Bahan Baku</label>
                    <select id="id_barang_mentah" name="id_barang_mentah" class="ui-input" required>
                        <option value="" disabled>Pilih Material</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id_material }}" data-stock="{{ (int) $material->quantity }}" {{ old('id_barang_mentah', $barangDalamProses->id_material) == $material->id_material ? 'selected' : '' }}>
                                {{ filled($material->ukuran) ? $material->nama . ' - ' . $material->ukuran : $material->nama }} (Stok: {{ $material->quantity }})
                            </option>
                        @endforeach
                    </select>
                    <p id="materialStockWarning" class="ui-error hidden">Bahan tidak cukup untuk membuat barang.</p>
                    @error('id_barang_mentah') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="quantity" class="ui-label">QTY</label>
                    <x-ui.input type="number" min="1" name="quantity" id="quantity" value="{{ old('quantity', $barangDalamProses->qty) }}" required />
                    @error('quantity') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="id_customer" class="ui-label">Pelanggan</label>
                    <select id="id_customer" name="id_customer" class="ui-input" required>
                        <option value="" disabled {{ old('id_customer', $barangDalamProses->id_pelanggan) ? '' : 'selected' }}>Pilih Pelanggan</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id_pelanggan }}" {{ old('id_customer', $barangDalamProses->id_pelanggan) == $customer->id_pelanggan ? 'selected' : '' }}>
                                {{ $customer->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_customer') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_po" class="ui-label">No PO</label>
                    <x-ui.input type="text" name="no_po" id="no_po" value="{{ old('no_po', $barangDalamProses->no_po) }}" placeholder="Masukkan No PO" required />
                    @error('no_po') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="tanggal_buat" class="ui-label">Tanggal Buat</label>
                        <x-ui.input type="date" name="tanggal_buat" id="tanggal_buat" value="{{ old('tanggal_buat', optional($barangDalamProses->tgl_dibuat)->format('Y-m-d')) }}" required />
                        @error('tanggal_buat') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="ui-label">Tanggal Selesai</label>
                        <x-ui.input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai', optional($barangDalamProses->tgl_selesai)->format('Y-m-d')) }}" />
                        @error('tanggal_selesai') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="ui-actions">
                    <x-ui.button type="submit" id="submitBtn">Simpan</x-ui.button>
                    <x-ui.button :href="route('barang-dalam-proses.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    <script>
        function updateUkuranAndSatuan() {
            const select = document.getElementById('id_barang');
            const selectedOption = select.options[select.selectedIndex];
            const ukuranInput = document.getElementById('ukuran');
            const satuanInput = document.getElementById('satuan');
            const satuanDisplay = document.getElementById('satuanDisplay');

            if (!selectedOption) {
                return;
            }

            const ukuran = selectedOption.getAttribute('data-ukuran') || '';
            const satuan = (selectedOption.getAttribute('data-satuan') || 'mm').toLowerCase();

            if (ukuran) {
                ukuranInput.value = ukuran;
            }

            satuanInput.value = satuan;
            satuanDisplay.textContent = satuan.toUpperCase();

            filterMaterialsForSelectedProduct();
        }

        function validateMaterialStock() {
            const materialSelect = document.getElementById('id_barang_mentah');
            const quantityInput = document.getElementById('quantity');
            const warningEl = document.getElementById('materialStockWarning');
            const submitBtn = document.getElementById('submitBtn');

            const oldMaterialId = String(@json((string) $barangDalamProses->id_material));
            const oldQty = Number(@json((int) $barangDalamProses->qty));

            const selectedOption = materialSelect.options[materialSelect.selectedIndex];
            const selectedMaterialId = materialSelect.value;
            const stock = selectedOption ? Number(selectedOption.getAttribute('data-stock') || 0) : 0;
            const qty = Number(quantityInput.value || 0);
            const availableStock = selectedMaterialId === oldMaterialId ? stock + oldQty : stock;
            const isInvalid = Boolean(selectedMaterialId) && qty > availableStock;

            warningEl.classList.toggle('hidden', !isInvalid);

            if (isInvalid) {
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
            } else {
                submitBtn.removeAttribute('disabled');
                submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }

        (function () {
            const productMaterials = @json(\App\Models\Product::with('materials:id_material')->get()->map(function ($p) {
                return [
                    'id' => $p->id_product,
                    'materials' => $p->materials->pluck('id_material')->map(fn($id) => (string) $id)->all(),
                ];
            }));

            const productMaterialsMap = {};
            productMaterials.forEach(function (item) {
                productMaterialsMap[item.id] = item.materials;
            });

            const materialSelect = document.getElementById('id_barang_mentah');
            const originalMaterialOptions = {};
            Array.from(materialSelect.options).forEach(function (opt) {
                if (!opt.value) {
                    return;
                }
                originalMaterialOptions[String(opt.value)] = opt.outerHTML;
            });

            window.filterMaterialsForSelectedProduct = function () {
                const prodSelect = document.getElementById('id_barang');
                const matSelect = document.getElementById('id_barang_mentah');
                const selectedProduct = prodSelect.value;
                const previousValue = matSelect.value;

                const placeholderHtml = '<option value="" selected>Pilih Material</option>';

                if (!selectedProduct) {
                    matSelect.innerHTML = placeholderHtml + Object.values(originalMaterialOptions).join('');
                    if (previousValue && originalMaterialOptions[String(previousValue)]) {
                        matSelect.value = previousValue;
                    }
                    validateMaterialStock();
                    return;
                }

                const allowed = productMaterialsMap[selectedProduct] || [];
                let html = placeholderHtml;

                allowed.forEach(function (id) {
                    if (originalMaterialOptions[String(id)]) {
                        html += originalMaterialOptions[String(id)];
                    }
                });

                matSelect.innerHTML = html;
                if (allowed.includes(String(previousValue))) {
                    matSelect.value = previousValue;
                }

                validateMaterialStock();
            };

            document.addEventListener('DOMContentLoaded', function () {
                updateUkuranAndSatuan();
                document.getElementById('id_barang_mentah').addEventListener('change', validateMaterialStock);
                document.getElementById('quantity').addEventListener('input', validateMaterialStock);
                filterMaterialsForSelectedProduct();
            });
        })();
    </script>
</x-app-layout>
