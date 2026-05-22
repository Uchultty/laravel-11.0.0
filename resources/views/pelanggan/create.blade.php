<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Tambah Pelanggan Baru') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('pelanggan.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="nama" class="ui-label">Nama Pelanggan</label>
                    <x-ui.input name="nama" id="nama" value="{{ old('nama') }}" required />
                    @error('nama') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="jabatan" class="ui-label">Jabatan</label>
                    <x-ui.input name="jabatan" id="jabatan" value="{{ old('jabatan') }}" oninput="this.value = this.value.toUpperCase()" />
                    @error('jabatan') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="alamat" class="ui-label">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="ui-input">{{ old('alamat') }}</textarea>
                </div>

                <div>
                    <label for="kontak" class="ui-label">Kontak</label>
                    <x-ui.input type="tel" name="kontak" id="kontak" value="{{ old('kontak') }}" minlength="8" maxlength="13" placeholder="Minimal 8 - Maksimal 13 angka" />
                    @error('kontak') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="ui-label">Email</label>
                    <x-ui.input type="email" name="email" id="email" value="{{ old('email') }}" />
                    @error('email') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="ui-actions">
                    <x-ui.button type="submit">Simpan</x-ui.button>
                    <x-ui.button :href="route('pelanggan.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
