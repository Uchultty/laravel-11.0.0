<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                {{ __('Manajemen Pengguna') }}
            </h2>
            <x-ui.button :href="route('users.create')">
                + Tambah Pengguna
            </x-ui.button>
        </div>
    </x-slot>

    <div class="ui-page">
        @if ($message = Session::get('success'))
            <div class="ui-alert-success">{{ $message }}</div>
        @endif

        <x-ui.card bodyClass="p-0">
            <x-ui.table>
                    <thead>
                        <tr>
                            <th class="text-left">Nama</th>
                            <th class="text-left">Email</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td class="font-semibold text-ink-800">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="space-x-3 text-center">
                                    <a href="{{ route('users.show', $user) }}" class="ui-link">Lihat</a>
                                    <a href="{{ route('users.edit', $user) }}" class="ui-link">Edit</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-sm font-semibold text-rose-600 hover:text-rose-700">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-sm text-ink-500">Tidak ada pengguna</td></tr>
                        @endforelse
                    </tbody>
            </x-ui.table>
            <div class="border-t border-ink-100 p-4">{{ $users->links() }}</div>
        </x-ui.card>
    </div>
</x-app-layout>
