<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Edit Pengguna') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="ui-label">Nama</label>
                    <x-ui.input name="name" id="name" value="{{ old('name', $user->name) }}" required />
                    @error('name') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="ui-label">Email</label>
                    <x-ui.input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required />
                    @error('email') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="ui-label">Password Baru (Kosongkan jika tidak diubah)</label>
                    <x-ui.input type="password" name="password" id="password" />
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
