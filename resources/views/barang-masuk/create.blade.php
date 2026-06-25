<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Tambah Direct Material') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form
                action="{{ route('barang-masuk.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-4"
                novalidate
                x-data='{
                    namaBarang: @json(old("nama_barang", "")),
                    barangSuggestions: @json($barangSuggestions),
                    isOpen: false,
                    activeIndex: -1,
                    filteredSuggestions() {
                        const query = this.namaBarang.trim().toLowerCase();

                        if (!query) {
                            return this.barangSuggestions.slice(0, 6);
                        }

                        return this.barangSuggestions
                            .filter(item => item.nama.toLowerCase().includes(query))
                            .slice(0, 6);
                    },
                    openSuggestions() {
                        this.isOpen = true;
                        this.activeIndex = -1;
                    },
                    closeSuggestions() {
                        this.isOpen = false;
                        this.activeIndex = -1;
                    },
                    moveDown() {
                        const total = this.filteredSuggestions().length;

                        if (total === 0) {
                            return;
                        }

                        if (!this.isOpen) {
                            this.isOpen = true;
                        }

                        this.activeIndex = (this.activeIndex + 1) % total;
                    },
                    moveUp() {
                        const total = this.filteredSuggestions().length;

                        if (total === 0) {
                            return;
                        }

                        if (!this.isOpen) {
                            this.isOpen = true;
                        }

                        this.activeIndex = this.activeIndex <= 0 ? total - 1 : this.activeIndex - 1;
                    },
                    chooseActive() {
                        const item = this.filteredSuggestions()[this.activeIndex];

                        if (item) {
                            this.selectSuggestion(item);
                        }
                    },
                    hasExactMatch() {
                        const query = this.namaBarang.trim().toLowerCase();

                        if (!query) {
                            return false;
                        }

                        return this.barangSuggestions.some(item => item.nama.toLowerCase() === query);
                    },
                    selectSuggestion(item) {
                        this.namaBarang = item.nama;
                        this.closeSuggestions();
                    }
                }'
            >
                @csrf

                <div class="relative" @click.away="closeSuggestions()">
                    <label for="nama_barang" class="ui-label">NAMA MATERIAL</label>
                    <x-ui.input
                        id="nama_barang"
                        name="nama_barang"
                        value="{{ old('nama_barang') }}"
                        placeholder="Tulis nama barang secara manual"
                        x-model="namaBarang"
                        @focus="openSuggestions()"
                        @input="openSuggestions()"
                        @keydown.arrow-down.prevent="moveDown()"
                        @keydown.arrow-up.prevent="moveUp()"
                        @keydown.enter.prevent="chooseActive()"
                        @keydown.escape.prevent="closeSuggestions()"
                        autocomplete="off"
                        required
                    />
                    @error('nama_barang') <p class="ui-error">{{ $message }}</p> @enderror

                    <div
                        class="absolute z-40 mt-2 w-full overflow-hidden rounded-xl border border-ink-200 bg-white shadow-lg"
                        x-show="isOpen && filteredSuggestions().length > 0"
                        x-transition
                        x-cloak
                    >
                        <div class="max-h-72 overflow-y-auto py-1">
                            <template x-for="(item, index) in filteredSuggestions()" :key="item.id_barang">
                                <button
                                    type="button"
                                    class="flex w-full items-center justify-between px-4 py-2.5 text-left text-sm transition"
                                    :class="activeIndex === index ? 'bg-ink-100' : 'hover:bg-ink-50'"
                                    @click="selectSuggestion(item)"
                                    @mouseenter="activeIndex = index"
                                >
                                    <span class="font-medium text-ink-800" x-text="item.nama"></span>
                                    <span class="text-xs text-ink-500" x-text="item.kode ? item.kode : '-'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <p class="mt-2 text-xs text-ink-500" x-show="hasExactMatch()">Nama ini sudah ada di master barang.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="quantity" class="ui-label">QTY</label>
                        <x-ui.input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" required />
                        @error('quantity') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="satuan_display" class="ui-label">SATUAN</label>
                        @php
                            $oldSatuan = old('satuan_display', '');
                        @endphp
                        <select id="satuan_display" name="satuan_display" class="ui-input">
                            <option value="" {{ $oldSatuan === '' ? 'selected' : '' }}>Pilih Satuan</option>
                            <option value="kg" {{ $oldSatuan === 'kg' ? 'selected' : '' }}>KG</option>
                            <option value="pcs" {{ $oldSatuan === 'pcs' ? 'selected' : '' }}>PCS</option>
                            <option value="liter" {{ $oldSatuan === 'liter' ? 'selected' : '' }}>LITER</option>
                            <option value="meter" {{ $oldSatuan === 'meter' ? 'selected' : '' }}>METER</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="tanggal_masuk" class="ui-label">WAKTU PEMESANAN</label>
                        <x-ui.input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ old('tanggal_masuk', now()->format('Y-m-d')) }}" required />
                        @error('tanggal_masuk') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="estimasi_tiba_display" class="ui-label">ESTIMASI TIBA</label>
                        <x-ui.input type="date" id="estimasi_tiba_display" name="estimasi_tiba_display" value="{{ old('estimasi_tiba_display') }}" />
                    </div>
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-3">
                        <label for="id_supplier" class="ui-label mb-0">SUPPLIER</label>
                        <a href="{{ route('suppliers.create') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">+ Tambah Supplier</a>
                    </div>
                    <select id="id_supplier" name="id_supplier" class="ui-input @error('id_supplier') border-rose-400 @enderror" required>
                        <option value="" disabled {{ old('id_supplier') ? '' : 'selected' }}>-- Kolom Supplier (Pilih) --</option>
                        @forelse ($suppliers as $supplier)
                            <option value="{{ $supplier->id_supplier }}" {{ old('id_supplier') == $supplier->id_supplier ? 'selected' : '' }}>
                                {{ $supplier->nama }}
                            </option>
                        @empty
                            <option value="" disabled>Belum ada supplier, silakan tambah supplier dulu</option>
                        @endforelse
                    </select>
                    <p class="ui-help">Jika tambah supplier baru, data akan otomatis muncul di kolom ini.</p>
                    @error('id_supplier') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="invoice" class="ui-label">INVOICE</label>
                    <input type="file" id="invoice" name="invoice" class="ui-input @error('invoice') border-rose-400 @enderror">
                    @error('invoice') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="ui-actions">
                    <x-ui.button type="submit">Simpan</x-ui.button>
                    <x-ui.button :href="route('barang-masuk.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
