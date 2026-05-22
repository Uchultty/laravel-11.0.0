<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Direct Material
            </h2>
            <x-ui.button :href="route('barang-masuk.create')">
                Tambah Direct Material
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
                                <th>Tanggal Masuk</th>
                                <th>Barang</th>
                                <th>Supplier</th>
                                <th class="text-right">Quantity</th>
                                <th>Input Oleh</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($barangMasuks as $item)
                                <tr>
                                    <td>{{ $item->tanggal_masuk->format('d/m/Y') }}</td>
                                    <td class="font-semibold text-ink-800">{{ optional($item->barang)->nama ?? '-' }}</td>
                                    <td>{{ optional($item->supplier)->nama ?? '-' }}</td>
                                    <td class="text-right font-semibold">{{ $item->quantity }}</td>
                                    <td>{{ optional($item->user)->name ?? '-' }}</td>
                                    <td class="space-x-3 text-center">
                                        <a href="{{ route('barang-masuk.edit', $item) }}" class="ui-link">Edit</a>
                                        <form action="{{ route('barang-masuk.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-ink-500">
                                        Tidak ada data barang masuk
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
            </x-ui.table>

            <div class="border-t border-ink-100 p-4">{{ $barangMasuks->links() }}</div>
        </x-ui.card>
    </div>
</x-app-layout>
