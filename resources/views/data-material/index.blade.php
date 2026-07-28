<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Master Data Material
            </h2>
            <x-ui.button :href="route('data-material.create')">
                + Tambah Material
            </x-ui.button>
        </div>
    </x-slot>

    <div class="ui-page mx-auto max-w-6xl space-y-8">
        @if ($message = Session::get('success'))
            <div class="ui-alert-success">{{ $message }}</div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>
        @endif

        <div class="space-y-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-3xl border border-cyan-100 bg-gradient-to-br from-cyan-50 via-white to-sky-50 p-5 shadow-sm sm:p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-cyan-500 shadow-sm ring-1 ring-cyan-100">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 2l8 4v8l-8 4-8-4V6l8-4Z" />
                                <path d="M12 22V12" />
                                <path d="M4 6l8 4 8-4" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-lg font-bold text-slate-900">Total Material</p>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Jumlah item material yang tersedia.</p>
                            <p class="mt-5 text-5xl font-black leading-none text-slate-900">{{ number_format($summaryTotalItem, 0, ',', '.') }}</p>
                            <p class="mt-2 text-xs font-bold uppercase tracking-[0.28em] text-cyan-700">Item</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-sky-100 bg-gradient-to-br from-sky-50 via-white to-blue-50 p-5 shadow-sm sm:p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-sky-500 shadow-sm ring-1 ring-sky-100">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M7 3h10a2 2 0 0 1 2 2v14H5V5a2 2 0 0 1 2-2Z" />
                                <path d="M8 7h8" />
                                <path d="M8 11h8" />
                                <path d="M8 15h5" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-lg font-bold text-slate-900">Stok Total</p>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Akumulasi stok saat ini dari semua material.</p>
                            <p class="mt-5 text-5xl font-black leading-none text-slate-900">{{ number_format($summaryTotalStok, 0, ',', '.') }}</p>
                            <p class="mt-2 text-xs font-bold uppercase tracking-[0.28em] text-sky-700">Item</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-lime-50 p-5 shadow-sm sm:p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path d="M12 4l8 14H4L12 4Z" />
                                <circle cx="12" cy="16.5" r="1" fill="currentColor" stroke="none" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-lg font-bold text-slate-900">Stok Minimum</p>
                            <p class="mt-1 text-sm leading-6 text-slate-600">Item dengan stok di bawah atau sama dengan {{ $stokMinimumBatas }}.</p>
                            <p class="mt-5 text-5xl font-black leading-none text-slate-900">{{ number_format($summaryStokMinimumItem, 0, ',', '.') }}</p>
                            <p class="mt-2 text-xs font-bold uppercase tracking-[0.28em] text-emerald-700">Item</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Cari material</p>
                        <p class="text-xs text-slate-500">Gunakan nama, ukuran, atau satuan</p>
                    </div>
                    <form method="GET" action="{{ request()->url() }}" class="flex w-full gap-3 lg:max-w-xl">
                        <div class="flex-1">
                            <input
                                type="text"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Cari nama material..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm placeholder-slate-400 focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-100"
                            />
                        </div>
                        <button
                            type="submit"
                            class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-slate-800"
                        >
                            Cari
                        </button>
                        @if ($search)
                            <a
                                href="{{ request()->url() }}"
                                class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50"
                            >
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Daftar Material</p>
                        <p class="mt-1 text-sm text-slate-600">Aksi edit dan hapus ada langsung di baris data.</p>
                    </div>
                </div>
                <x-ui.table>
                    <thead>
                        <tr>
                            <th class="text-left">Nama Material</th>
                            <th class="text-left">Ukuran</th>
                            <th class="text-left">Satuan</th>
                            <th class="text-left">Stok Saat Ini</th>
                            <th class="text-left">Stok Minimum</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangs as $barang)
                            <tr>
                                <td class="align-middle text-left text-sm font-semibold text-ink-800">{{ $barang->nama }}</td>
                                <td class="align-middle text-left text-sm font-semibold tabular-nums text-ink-700">
                                    {{ $barang->ukuran ?? '-' }}
                                </td>
                                <td class="align-middle text-left text-sm text-ink-700">{{ $barang->latest_satuan ?: '-' }}</td>
                                <td class="align-middle text-left text-sm font-semibold tabular-nums text-ink-700">{{ number_format((int) $barang->display_quantity, 0, ',', '.') }}</td>
                                <td class="align-middle text-left text-sm font-semibold tabular-nums text-ink-700">{{ number_format((int) $barang->stok_minimum, 0, ',', '.') }}</td>
                                <td class="align-middle text-center">
                                    <div class="flex flex-wrap items-center justify-center gap-2">
                                        <x-ui.button :href="route('data-material.edit', $barang) . '?form_mode=material'" variant="secondary" class="px-3 py-2 text-xs">Edit</x-ui.button>
                                        <form id="deleteForm{{ $barang->getKey() }}" action="{{ route('data-material.destroy-barang', $barang) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="button" variant="danger" class="px-3 py-2 text-xs" onclick="confirmDelete(event, '{{ $barang->getKey() }}', '{{ $barang->nama }}')">Hapus</x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-ink-500">Tidak ada data material.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>

                <div class="border-t border-slate-200 bg-slate-50 p-4">{{ $barangs->links() }}</div>
            </div>
        </div>
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
            <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Hapus Material?</h3>
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
