<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">Laporan Pengiriman Produk</h2>
    </x-slot>

    <div class="ui-page space-y-5">
        <div class="flex justify-center">
            <x-ui.card bodyClass="w-full max-w-md p-6 text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-ink-500">Total Item Produk</p>
                <p class="mt-4 text-4xl font-bold text-ink-900">{{ number_format($summaryTotalItem, 0, ',', '.') }}</p>
            </x-ui.card>
        </div>

        <x-ui.card bodyClass="p-0">
            <x-ui.table>
                <thead>
                    <tr>
                        <th class="text-left">Nama Barang</th>
                        <th class="text-left">Jenis</th>
                        <th class="text-left">Total Masuk</th>
                        <th class="text-left">Total Keluar</th>
                        <th class="text-left">Stok Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangs as $barang)
                        <tr>
                            <td class="align-middle text-left text-sm font-semibold text-ink-800">{{ $barang->nama }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ optional($barang->jenisBarang)->nama ?? '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ number_format((int) $barang->total_masuk, 0, ',', '.') }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ number_format((int) $barang->total_keluar, 0, ',', '.') }}</td>
                            <td class="align-middle text-left text-sm font-semibold text-ink-900">{{ number_format((int) $barang->current_quantity, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-ink-500">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
            <div class="border-t border-ink-100 p-4">{{ $barangs->links() }}</div>
        </x-ui.card>
    </div>
</x-app-layout>
