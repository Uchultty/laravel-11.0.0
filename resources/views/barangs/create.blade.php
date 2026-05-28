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
</x-app-layout>
