<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Pengiriman Produk
            </h2>
        </div>
    </x-slot>

    <div class="ui-page">
        @if ($message = Session::get('success'))
            <div class="ui-alert-success">{{ $message }}</div>
        @endif

        <div class="mb-6">
            <form method="GET" action="{{ route('pengiriman-produk.index') }}" class="grid gap-3 md:grid-cols-[minmax(240px,1fr)_220px_180px_auto]">
                <div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        placeholder="Cari produk, customer..."
                        class="w-full rounded-lg border border-ink-200 px-4 py-2.5 text-sm placeholder-ink-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div>
                    <select
                        name="status"
                        class="w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-sm text-ink-700 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Semua Status</option>
                        <option value="Siap Dikirim" @selected(($status ?? '') === 'Siap Dikirim')>Siap Dikirim</option>
                        <option value="Sedang Dikirim" @selected(($status ?? '') === 'Sedang Dikirim')>Sedang Dikirim</option>
                        <option value="Selesai" @selected(($status ?? '') === 'Selesai')>Selesai</option>
                    </select>
                </div>

                <div>
                    <input
                        type="date"
                        name="tanggal"
                        value="{{ $tanggal ?? '' }}"
                        class="w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-sm text-ink-700 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" class="px-4 py-2.5 text-sm">Filter</x-ui.button>
                    <a
                        href="{{ route('pengiriman-produk.index') }}"
                        class="inline-flex items-center rounded-lg border border-ink-200 px-4 py-2.5 text-sm font-medium text-ink-700 transition-colors hover:bg-ink-50"
                    >
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <x-ui.card class="overflow-hidden" style="height: clamp(620px, 76vh, 840px);" bodyClass="h-full flex flex-col p-0 overflow-hidden">
            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-auto pr-1">
                <x-ui.table class="compact-scroll-table">
                @php
                    $statusLabels = [
                        'Menunggu Pengiriman' => 'Dalam Proses',
                        'Dalam Pengiriman' => 'Siap Dikirim',
                    ];
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
                        <th class="text-left">No PO</th>
                        <th class="text-left">No Gambar</th>
                        <th class="text-left">Customer</th>
                        <th class="text-left">Tanggal Kirim</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Surat Jalan</th>
                        <th class="text-left">Invoice</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangKeluars as $item)
                        <tr>
                            <td class="align-middle text-left text-sm font-semibold text-ink-800">
                                @if (!empty($item->items) && is_array($item->items) && count($item->items) > 1)
                                    @foreach($item->items as $it)
                                        <div>{{ optional(\App\Models\Product::find($it['id_produk'] ?? null))->nama ?? '-' }}</div>
                                    @endforeach
                                @else
                                    {{ optional($item->barang)->nama ?? '-' }}
                                @endif
                            </td>
                            <td class="align-middle text-left text-sm tabular-nums text-ink-700">
                                @if (!empty($item->items) && is_array($item->items) && count($item->items) > 1)
                                    @foreach($item->items as $it)
                                        <div>{{ number_format($it['qty'] ?? 0, 0, ',', '.') }}</div>
                                    @endforeach
                                @else
                                    {{ number_format($item->quantity, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ $item->no_po ?? '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">
                                @if (!empty($item->items) && is_array($item->items) && count($item->items) > 1)
                                    @foreach($item->items as $it)
                                        <div>{{ $it['no_gambar'] ?? '-' }}</div>
                                    @endforeach
                                @else
                                    {{ $item->no_gambar ?? '-' }}
                                @endif
                            </td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ optional($item->customer)->nama ?? '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ optional($item->tanggal_keluar)->format('d/m/Y') ?? '-' }}</td>
                            <td class="align-middle text-left">
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $item->status_badge_class }}">
                                    {{ $item->status_display }}
                                </span>
                            </td>
                            <td class="align-middle text-left text-sm text-ink-700">
                                @if($item->surat_jalan_path)
                                    <a href="{{ \Storage::url($item->surat_jalan_path) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-ink-400">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-left text-sm text-ink-700">
                                @if($item->invoice_path)
                                    <a href="{{ \Storage::url($item->invoice_path) }}" target="_blank" class="inline-flex items-center gap-1 text-green-600 hover:text-green-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-ink-400">-</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <x-ui.button :href="route('pengiriman-produk.show', $item)" variant="ghost" class="px-3 py-2 text-xs">Detail</x-ui.button>
                                    @auth
                                        <x-ui.button :href="route('pengiriman-produk.edit', $item)" variant="secondary" class="px-3 py-2 text-xs">Edit</x-ui.button>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-sm text-ink-500">Tidak ada data pengiriman produk</td>
                        </tr>
                    @endforelse
                </tbody>
                </x-ui.table>
            </div>
            <div class="border-t border-ink-100 p-4">{{ $barangKeluars->links() }}</div>
        </x-ui.card>
    </div>

    <script>
    function confirmDelete(event, itemId, itemName) {
        event.preventDefault();
        const modal = document.getElementById('deleteModal');
        const itemNameEl = document.getElementById('deleteItemName');
        const confirmBtn = document.getElementById('deleteConfirmBtn');
        const cancelBtn = document.getElementById('deleteCancelBtn');

        itemNameEl.textContent = itemName;
        modal.classList.remove('hidden');

        confirmBtn.onclick = function () {
            document.getElementById('deleteForm' + itemId).submit();
        };

        function closeModal() {
            modal.classList.add('hidden');
        }

        cancelBtn.onclick = closeModal;
        modal.onclick = function(e) {
            if (e.target === modal) closeModal();
        };
    }
    </script>

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

    <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-sm rounded-2xl border border-red-200 bg-white p-6 shadow-2xl">
            <div class="flex justify-end mb-2">
                <button type="button" class="text-slate-400 hover:text-slate-600" onclick="document.getElementById('deleteModal').classList.add('hidden')">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Hapus Pengiriman Produk?</h3>
            <p class="text-center text-sm text-slate-600 mb-4">Anda akan menghapus: <span id="deleteItemName" class="font-semibold text-slate-900"></span></p>
            <div class="flex gap-3">
                <button type="button" id="deleteCancelBtn" class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="deleteConfirmBtn" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
