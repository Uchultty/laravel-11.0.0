<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                {{ __('Detail Pengguna: ') }}{{ $user->name }}
            </h2>
            <div class="flex items-center gap-2">
                <x-ui.button :href="route('users.edit', $user)">Edit</x-ui.button>
                <x-ui.button :href="route('users.index')" variant="secondary">Kembali</x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <dt class="ui-label">Nama</dt>
                    <dd class="text-sm text-ink-900">{{ $user->name }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="ui-label">Email</dt>
                    <dd class="text-sm text-ink-900">{{ $user->email }}</dd>
                </div>
            </dl>

            <div class="mt-6 border-t border-ink-100 pt-4 text-xs text-ink-500">
                <p>Email Terverifikasi: {{ $user->email_verified_at ?? 'Belum' }}</p>
                <p class="mt-1">Dibuat: {{ $user->created_at }}</p>
                <p class="mt-1">Diperbarui: {{ $user->updated_at }}</p>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
