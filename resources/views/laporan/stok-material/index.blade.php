<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">Laporan Stok Material</h2>
    </x-slot>

    <div class="ui-page space-y-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-ui.card bodyClass="p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-ink-500">Total Item Material</p>
                <p class="mt-2 text-2xl font-bold text-ink-900">{{ number_format($summaryTotalItem, 0, ',', '.') }}</p>
            </x-ui.card>
            <x-ui.card bodyClass="p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-ink-500">Total Stok Material</p>
                <p class="mt-2 text-2xl font-bold text-ink-900">{{ number_format($summaryTotalStok, 0, ',', '.') }}</p>
            </x-ui.card>
            <x-ui.card bodyClass="p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-ink-500">Stok Menipis (&lt;= {{ $stokMinimumBatas }})</p>
                <p class="mt-2 text-2xl font-bold text-amber-600">{{ number_format($summaryStokMenipis, 0, ',', '.') }}</p>
            </x-ui.card>
        </div>

        <x-ui.card bodyClass="p-0">
            <x-ui.table>
                <thead>
                    <tr>
                        <th class="text-left">Nama Barang</th>
                        <th class="text-left">Jenis</th>
                        <th class="text-left">Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangs as $barang)
                        <tr>
                            <td class="align-middle text-left text-sm font-semibold text-ink-800">{{ $barang->nama }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ optional($barang->jenisBarang)->nama ?? '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ number_format((int) $barang->quantity, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-sm text-ink-500">Belum ada data material.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>
            <div class="border-t border-ink-100 p-4">{{ $barangs->links() }}</div>
        </x-ui.card>
    </div>
</x-app-layout>
