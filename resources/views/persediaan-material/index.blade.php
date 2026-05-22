<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Persediaan Material
            </h2>
            <x-ui.button :href="route('persediaan-material.create')">
                <svg class="h-4 w-4 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Material
            </x-ui.button>
        </div>
    </x-slot>

    <div class="ui-page">
        @if ($message = Session::get('success'))
            <div class="ui-alert-success">{{ $message }}</div>
        @endif

        <x-ui.card class="mb-6">
            <form method="GET" action="{{ route('persediaan-material.index') }}" class="grid gap-3 md:grid-cols-[minmax(240px,1fr)_180px_180px_auto] md:items-end">
                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1">Cari</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nama material atau supplier..."
                        class="w-full rounded-lg border border-ink-200 px-4 py-2.5 text-sm placeholder-ink-400 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1">Dari Tanggal</label>
                    <input
                        type="date"
                        name="tanggal_dari"
                        value="{{ request('tanggal_dari') }}"
                        class="w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-sm text-ink-700 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 mb-1">Sampai Tanggal</label>
                    <input
                        type="date"
                        name="tanggal_sampai"
                        value="{{ request('tanggal_sampai') }}"
                        class="w-full rounded-lg border border-ink-200 bg-white px-4 py-2.5 text-sm text-ink-700 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" class="px-4 py-2.5 text-sm">Filter</x-ui.button>
                    <a href="{{ route('persediaan-material.index') }}" class="inline-flex items-center rounded-lg border border-ink-200 px-4 py-2.5 text-sm font-medium text-ink-700 transition-colors hover:bg-ink-50">
                        Reset
                    </a>
                </div>
            </form>
        </x-ui.card>

        <x-ui.card bodyClass="p-0">
            <div class="overflow-x-auto">
                <x-ui.table>
                    <thead>
                        <tr>
                            <th class="text-left">Tanggal Pemesanan</th>
                            <th class="text-left">No PO</th>
                            <th class="text-left">Material</th>
                            <th class="text-left">Qty</th>
                            <th class="text-left">Satuan</th>
                            <th class="text-left">Supplier</th>
                            <th class="text-left">Surat Jalan</th>
                            <th class="text-left">Invoice</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($persediaanMaterials as $materialOrder)
                            <tr>
                                <td class="align-middle text-sm text-ink-700">
                                    {{ optional($materialOrder->tgl_pemesanan)->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="align-middle text-sm text-ink-700 font-medium">
                                    {{ $materialOrder->no_po ?? '-' }}
                                </td>
                                <td class="align-middle text-sm font-semibold text-ink-800">
                                    {{ optional($materialOrder->material)->nama ?? '-' }}
                                </td>
                                <td class="align-middle text-sm tabular-nums text-ink-700">
                                    {{ number_format($materialOrder->qty) }}
                                </td>
                                <td class="align-middle text-sm text-ink-700">
                                    {{ $materialOrder->satuan ?? '-' }}
                                </td>
                                <td class="align-middle text-sm text-ink-700">
                                    {{ optional($materialOrder->supplier)->nama ?? '-' }}
                                </td>
                                <td class="align-middle text-sm text-ink-700">
                                    @if($materialOrder->surat_jalan_path)
                                        <a href="{{ \Storage::url($materialOrder->surat_jalan_path) }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-ink-400">-</span>
                                    @endif
                                </td>
                                <td class="align-middle text-sm text-ink-700">
                                    @if($materialOrder->invoice_path)
                                        <a href="{{ \Storage::url($materialOrder->invoice_path) }}" target="_blank" class="inline-flex items-center gap-1 text-green-600 hover:text-green-700">
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
                                        <x-ui.button :href="route('persediaan-material.show', $materialOrder)" variant="ghost" class="px-3 py-2 text-xs">Detail</x-ui.button>
                                        <x-ui.button :href="route('persediaan-material.edit', $materialOrder)" variant="secondary" class="px-3 py-2 text-xs">Edit</x-ui.button>
                                        <form id="deleteForm{{ $materialOrder->id_pemesanan }}" action="{{ route('persediaan-material.destroy', $materialOrder) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="button" variant="danger" class="px-3 py-2 text-xs" onclick="confirmDelete(event, '{{ $materialOrder->id_pemesanan }}', '{{ optional($materialOrder->material)->nama ?? 'Material' }}')">Hapus</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-ink-500">
                                    Tidak ada data persediaan material
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>
            </div>
            <div class="border-t border-ink-100 p-4">{{ $persediaanMaterials->links() }}</div>
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
            <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Hapus Persediaan Material?</h3>
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
