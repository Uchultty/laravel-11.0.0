<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">Laporan Stok Material</h2>
    </x-slot>

    <div class="ui-page space-y-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <!-- Ringkasan laporan mengikuti data yang sudah difilter di controller. -->
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

        <x-ui.card bodyClass="p-4">
            <!-- Filter diletakkan di bawah card ringkasan supaya alur baca laporan lebih enak. -->
            <form method="GET" action="{{ request()->url() }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                <div class="flex-1">
                    <label for="q" class="ui-label">Cari nama barang</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        value="{{ $search ?? '' }}"
                        placeholder="Cari nama barang"
                        class="ui-input uppercase"
                        autocomplete="off"
                        spellcheck="false"
                        oninput="this.value = this.value.toUpperCase()"
                    >
                </div>
                <div class="flex gap-3">
                    <x-ui.button type="submit">Cari</x-ui.button>
                    @if(!empty($search))
                        <x-ui.button :href="request()->url()" variant="secondary">Reset</x-ui.button>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <x-ui.card class="overflow-hidden" style="height: clamp(560px, 72vh, 760px);" bodyClass="h-full flex flex-col p-0 overflow-hidden">
            <div class="border-b border-ink-100 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar Material</p>
                        <p class="text-xs text-slate-500">Tampilan mengikuti sistem scroll internal pada data material.</p>
                    </div>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-auto pr-1">
                <x-ui.table class="compact-scroll-table">
                    <thead>
                        <tr>
                            <th class="text-left">Nama Barang</th>
                            <th class="text-left">Ukuran</th>
                            <th class="text-left">Jenis</th>
                            <th class="text-left">Stok Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangs as $barang)
                            <tr>
                                <!-- Nama dan ukuran dipisah supaya informasi barang lebih mudah dibaca. -->
                                <td class="align-middle text-left text-sm font-semibold text-ink-800">{{ $barang->nama }}</td>
                                <td class="align-middle text-left text-sm text-ink-700">{{ filled($barang->ukuran) ? $barang->ukuran : '-' }}</td>
                                <td class="align-middle text-left text-sm text-ink-700">{{ optional($barang->jenisBarang)->nama ?? '-' }}</td>
                                <td class="align-middle text-left text-sm text-ink-700">{{ number_format((int) $barang->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-ink-500">Belum ada data material.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>

<style>
.compact-scroll-table thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: #f8fafc;
}

.compact-scroll-table th,
.compact-scroll-table td {
    padding-top: 0.65rem;
    padding-bottom: 0.65rem;
}

.compact-scroll-table tbody tr {
    height: 50px;
}
</style>
