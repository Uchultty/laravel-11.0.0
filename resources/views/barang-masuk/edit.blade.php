<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Edit Direct Material') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('barang-masuk.update', $barangMasuk) }}" method="POST" enctype="multipart/form-data" class="space-y-4" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <label for="id_barang" class="ui-label">Barang</label>
                    <select id="id_barang" name="id_barang" class="ui-input @error('id_barang') border-rose-400 @enderror" required>
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id_barang }}" {{ $barangMasuk->id_barang == $barang->id_barang ? 'selected' : '' }}>
                                {{ $barang->kode }} - {{ $barang->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_barang') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="id_supplier" class="ui-label">Supplier</label>
                    <select id="id_supplier" name="id_supplier" class="ui-input @error('id_supplier') border-rose-400 @enderror" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id_supplier }}" {{ $barangMasuk->id_supplier == $supplier->id_supplier ? 'selected' : '' }}>
                                {{ $supplier->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_supplier') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal_masuk" class="ui-label">Tanggal Masuk</label>
                    <x-ui.input type="date" id="tanggal_masuk" name="tanggal_masuk" value="{{ $barangMasuk->tanggal_masuk->format('Y-m-d') }}" required />
                    @error('tanggal_masuk') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="satuan_display" class="ui-label">Satuan</label>
                        @php($oldSatuan = old('satuan_display', $barangMasuk->satuan))
                        <select id="satuan_display" name="satuan_display" class="ui-input @error('satuan_display') border-rose-400 @enderror">
                            <option value="" {{ $oldSatuan === null || $oldSatuan === '' ? 'selected' : '' }}>Pilih Satuan</option>
                            <option value="kg" {{ $oldSatuan === 'kg' ? 'selected' : '' }}>KG</option>
                            <option value="pcs" {{ $oldSatuan === 'pcs' ? 'selected' : '' }}>PCS</option>
                            <option value="liter" {{ $oldSatuan === 'liter' ? 'selected' : '' }}>LITER</option>
                            <option value="meter" {{ $oldSatuan === 'meter' ? 'selected' : '' }}>METER</option>
                        </select>
                        @error('satuan_display') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="estimasi_tiba_display" class="ui-label">Estimasi Tiba</label>
                        <x-ui.input type="date" id="estimasi_tiba_display" name="estimasi_tiba_display" value="{{ old('estimasi_tiba_display', optional($barangMasuk->estimasi_tiba)->format('Y-m-d')) }}" />
                        @error('estimasi_tiba_display') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="invoice" class="ui-label">Invoice</label>
                    @if ($barangMasuk->invoice_path)
                        <p class="ui-help mb-2">
                            <a href="{{ Storage::url($barangMasuk->invoice_path) }}" target="_blank" class="ui-link">Lihat file saat ini</a>
                        </p>
                    @endif
                    <input type="file" id="invoice" name="invoice" class="ui-input @error('invoice') border-rose-400 @enderror">
                    @error('invoice') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="ui-actions">
                    <x-ui.button type="submit">Perbarui</x-ui.button>
                    <x-ui.button :href="route('barang-masuk.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
