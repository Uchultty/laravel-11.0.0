<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Laporan Pengiriman Produk
            </h2>
            <a href="{{ route('laporan-pengiriman-produk.export-pdf', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl bg-ink-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-ink-800">
                Export PDF
            </a>
        </div>
    </x-slot>

    <div class="ui-page space-y-5">
        <div class="flex justify-center">
            <x-ui.card bodyClass="w-full max-w-md p-6 text-center">
                <p class="text-xs font-semibold uppercase tracking-wide text-ink-500">Total Data Pengiriman</p>
                <p class="mt-4 text-4xl font-bold text-ink-900">{{ number_format($summaryTotalItem, 0, ',', '.') }}</p>
            </x-ui.card>
        </div>

        <x-ui.card bodyClass="space-y-4 p-4">
            <form method="GET" action="{{ route('laporan-pengiriman-produk.index') }}" class="grid gap-3 md:grid-cols-4">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ink-500">Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Produk / customer" class="w-full rounded-xl border-ink-200 px-3 py-2 text-sm focus:border-ink-400 focus:ring-ink-400">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ink-500">Status</label>
                    <select name="status" class="w-full rounded-xl border-ink-200 px-3 py-2 text-sm focus:border-ink-400 focus:ring-ink-400">
                        @foreach (($statusOptions ?? ['Selesai']) as $statusOption)
                            <option value="{{ $statusOption }}" @selected(($filters['status'] ?? 'Selesai') === $statusOption)>{{ $statusOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-ink-500">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $filters['tanggal'] ?? '' }}" class="w-full rounded-xl border-ink-200 px-3 py-2 text-sm focus:border-ink-400 focus:ring-ink-400">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-ink-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-ink-800">Filter</button>
                    <a href="{{ route('laporan-pengiriman-produk.index') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-ink-200 px-4 py-2 text-sm font-semibold text-ink-700 transition hover:bg-ink-50">Reset</a>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card class="overflow-hidden" style="height: clamp(620px, 76vh, 840px);" bodyClass="h-full flex flex-col p-0 overflow-hidden">
            <div class="border-b border-ink-100 p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar Pengiriman</p>
                        <p class="text-xs text-slate-500">Tampilan mengikuti sistem scroll internal seperti laporan material.</p>
                    </div>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-auto pr-1">
                <x-ui.table class="compact-scroll-table">
                    @php
                        $statusBadgeClasses = [
                            'Dalam Proses' => 'bg-amber-100 text-amber-800',
                            'Siap Dikirim' => 'bg-blue-100 text-blue-800',
                            'Sedang Dikirim' => 'bg-emerald-100 text-emerald-800',
                            'Selesai' => 'bg-slate-100 text-slate-800',
                        ];
                    @endphp
                    <thead>
                        <tr>
                            <th class="text-left">Produk</th>
                            <th class="text-left">Qty</th>
                            <th class="text-left">Customer</th>
                            <th class="text-left">Tanggal Kirim</th>
                            <th class="text-left">Status</th>
                            <th class="text-left">Surat Jalan</th>
                            <th class="text-left">Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pengirimanCompletedPaginated as $item)
                            <tr>
                                <td class="align-middle text-left text-sm font-semibold text-ink-800">{{ optional($item->barang)->nama ?? '-' }}</td>
                                <td class="align-middle text-left text-sm tabular-nums text-ink-700">{{ number_format((int) $item->quantity, 0, ',', '.') }}</td>
                                <td class="align-middle text-left text-sm text-ink-700">{{ data_get($item, 'customer.nama') ?? data_get($item, 'productionItem.pelanggan.nama') ?? '-' }}</td>
                                <td class="align-middle text-left text-sm text-ink-700">{{ optional($item->tanggal_pengiriman)->format('d/m/Y') ?? '-' }}</td>
                                <td class="align-middle text-left">
                                    <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $statusBadgeClasses[$item->status_pengiriman] ?? 'bg-slate-100 text-slate-800' }}">
                                        {{ $item->status_pengiriman ?? '-' }}
                                    </span>
                                </td>
                                <td class="align-middle text-left text-sm text-ink-700">
                                    {{ $item->surat_jalan_path ? basename($item->surat_jalan_path) : '-' }}
                                </td>
                                <td class="align-middle text-left text-sm text-ink-700">
                                    {{ $item->invoice_path ? basename($item->invoice_path) : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-ink-500">Belum ada data pengiriman.</td>
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
