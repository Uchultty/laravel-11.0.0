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

               <div>
    <label for="id_barang" class="ui-label">Nama Produk</label>

    <select id="id_barang" name="id_barang" class="ui-input" required onchange="updateUkuranAndSatuan()">
        <option value="" disabled {{ old('id_barang') ? '' : 'selected' }}>
            Pilih Produk
        </option>

        @foreach ($barangs as $barang)

    <option
        value="{{ $barang->id_product }}"
        data-satuan="{{ $barang->satuan ?? '' }}"
        data-ukuran="{{ $barang->ukuran ?? '' }}"
    >
        {{ $barang->nama }} ({{ $barang->ukuran ?? '-' }})
    </option>
@endforeach
    </select>

    @error('id_barang')
        <p class="ui-error">{{ $message }}</p>
    @enderror
</div>

                <div>
                    <label for="no_gambar" class="ui-label">No Gambar</label>
                    <x-ui.input type="text" name="no_gambar" id="no_gambar" value="{{ old('no_gambar') }}" placeholder="Masukkan No Gambar" />
                    @error('no_gambar') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="satuan" class="ui-label">Satuan</label>
                    <input type="hidden" id="satuan" name="satuan" value="mm">
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

                <div>
                    <label for="id_barang_mentah" class="ui-label">Material/Bahan Baku</label>
                    <select id="id_barang_mentah" name="id_barang_mentah" class="ui-input" required>
                        <option value="" disabled {{ old('id_barang_mentah') ? '' : 'selected' }}>Pilih Material</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id_material }}" data-stock="{{ (int) $material->quantity }}" {{ old('id_barang_mentah') == $material->id_material ? 'selected' : '' }}>
                                {{ filled($material->ukuran) ? $material->nama . ' - ' . $material->ukuran : $material->nama }} (Stok: {{ $material->quantity }})
                            </option>
                        @endforeach
                    </select>
                    <p id="materialStockWarning" class="ui-error hidden">Bahan tidak cukup untuk membuat barang.</p>
                    @error('id_barang_mentah') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="quantity" class="ui-label">QTY</label>
                    <x-ui.input type="number" min="1" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" required />
                    @error('quantity') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="id_customer" class="ui-label">Pelanggan</label>
                        <select id="id_customer" name="id_customer" class="ui-input" required>
                            <option value="" disabled {{ old('id_customer') ? '' : 'selected' }}>Pilih Pelanggan</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id_customer }}" {{ old('id_customer') == $customer->id_customer ? 'selected' : '' }}>
                                    {{ $customer->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_customer') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="no_po" class="ui-label">No PO</label>
                        <x-ui.input type="text" name="no_po" id="no_po" value="{{ old('no_po') }}" placeholder="Masukkan No PO" required />
                        @error('no_po') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="tanggal_buat" class="ui-label">Tanggal Buat</label>
                        <x-ui.input type="date" name="tanggal_buat" id="tanggal_buat" value="{{ old('tanggal_buat', now()->format('Y-m-d')) }}" required />
                        @error('tanggal_buat') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="ui-label">Tanggal Selesai</label>
                        <x-ui.input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}" />
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
            const ukuran = selectedOption.getAttribute('data-ukuran');

            const ukuranInput = document.getElementById('ukuran');
            if (ukuran) {
                ukuranInput.value = ukuran;
            }
            filterMaterialsForSelectedProduct();
        }

        function validateMaterialStock() {
            const materialSelect = document.getElementById('id_barang_mentah');
            const quantityInput = document.getElementById('quantity');
            const warningEl = document.getElementById('materialStockWarning');
            const submitBtn = document.getElementById('submitBtn');

            const selectedOption = materialSelect.options[materialSelect.selectedIndex];
            const stock = selectedOption ? Number(selectedOption.getAttribute('data-stock') || 0) : 0;
            const qty = Number(quantityInput.value || 0);
            const isInvalid = Boolean(materialSelect.value) && qty > stock;

            if (isInvalid) {
                warningEl.classList.remove('hidden');
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.classList.add('opacity-60', 'cursor-not-allowed');
            } else {
                warningEl.classList.add('hidden');
                submitBtn.removeAttribute('disabled');
                submitBtn.classList.remove('opacity-60', 'cursor-not-allowed');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateUkuranAndSatuan();

            const materialSelect = document.getElementById('id_barang_mentah');
            const quantityInput = document.getElementById('quantity');

            materialSelect.addEventListener('change', validateMaterialStock);
            quantityInput.addEventListener('input', validateMaterialStock);

            validateMaterialStock();
        });

        // Build a mapping product_id -> [material_ids] from server data
        (function () {
            const productMaterials = @json(\App\Models\Product::with('materials:id_material')->get()->map(function($p) {
                return [
                    'id' => $p->id_product,
                    'materials' => $p->materials->pluck('id_material')->all(),
                ];
            }));

            // Convert to lookup object for JS
            window.__productMaterialsMap = {};
            productMaterials.forEach(function(item) {
                window.__productMaterialsMap[item.id] = item.materials;
            });

            // Save original material options by id for reconstruction
            const materialSelect = document.getElementById('id_barang_mentah');
            window.__originalMaterialOptions = {};
            Array.from(materialSelect.options).forEach(function(opt) {
                if (!opt.value) return; // skip placeholder
                window.__originalMaterialOptions[opt.value] = opt.outerHTML;
            });

            // Filtering function
            window.filterMaterialsForSelectedProduct = function() {
                const prodSelect = document.getElementById('id_barang');
                const matSelect = document.getElementById('id_barang_mentah');
                const selectedProduct = prodSelect.value;

                const placeholder = Array.from(matSelect.options).find(o => !o.value);
                const placeholderHtml = placeholder ? placeholder.outerHTML : '<option value="" disabled selected>Pilih Material</option>';

                // If no product selected, keep all options
                if (!selectedProduct) {
                    let html = placeholderHtml + Object.values(window.__originalMaterialOptions).join('');
                    matSelect.innerHTML = html;
                    validateMaterialStock();
                    return;
                }

                const allowed = window.__productMaterialsMap[selectedProduct] || [];
                let html = placeholderHtml;

                allowed.forEach(function(mid) {
                    const optHtml = window.__originalMaterialOptions[mid];
                    if (optHtml) html += optHtml;
                });

                matSelect.innerHTML = html;
                validateMaterialStock();
            };

            // Initial filter on load
            document.addEventListener('DOMContentLoaded', function() {
                filterMaterialsForSelectedProduct();
            });
        })();
    </script>
</x-app-layout>
