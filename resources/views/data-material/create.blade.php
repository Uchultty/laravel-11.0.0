<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Tambah Kategori Material') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('data-material.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="nama" class="ui-label">Nama Kategori Material</label>
                    <x-ui.input name="nama" id="nama" value="{{ old('nama') }}" required />
                    @error('nama') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="deskripsi" class="ui-label">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3" class="ui-input">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="ui-actions">
                    <x-ui.button type="submit">Simpan</x-ui.button>
                    <x-ui.button :href="route('data-material.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
