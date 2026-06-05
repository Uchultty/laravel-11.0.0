<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Edit Supplier') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="nama" class="ui-label">Nama Supplier</label>
                    <x-ui.input name="nama" id="nama" value="{{ old('nama', $supplier->nama) }}" required />
                    @error('nama') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="pic" class="ui-label">PIC</label>
                    <x-ui.input name="pic" id="pic" value="{{ old('pic', $supplier->pic) }}" oninput="this.value = this.value.toUpperCase()" />
                    @error('pic') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="alamat" class="ui-label">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="ui-input">{{ old('alamat', $supplier->alamat) }}</textarea>
                </div>

                <div>
                    <label for="kontak" class="ui-label">Kontak</label>
                    <x-ui.input type="tel" name="kontak" id="kontak" value="{{ old('kontak', $supplier->kontak) }}" minlength="8" maxlength="13" placeholder="Minimal 8 - Maksimal 13 angka" />
                    @error('kontak') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="ui-label">Email</label>
                    <x-ui.input type="email" name="email" id="email" value="{{ old('email', $supplier->email) }}" />
                    @error('email') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="ui-actions">
                    <x-ui.button type="submit">Simpan</x-ui.button>
                    <x-ui.button :href="route('suppliers.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
