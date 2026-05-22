<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Tambah Pengguna Baru') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="ui-label">Nama</label>
                    <x-ui.input name="name" id="name" value="{{ old('name') }}" required />
                    @error('name') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="ui-label">Email</label>
                    <x-ui.input type="email" name="email" id="email" value="{{ old('email') }}" required />
                    @error('email') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="ui-label">Password</label>
                    <x-ui.input type="password" name="password" id="password" required />
                    @error('password') <p class="ui-error">{{ $message }}</p> @enderror
                </div>



                <div class="ui-actions">
                    <x-ui.button type="submit">Simpan</x-ui.button>
                    <x-ui.button :href="route('users.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
