<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                {{ $pageTitle ?? 'Master Data Barang' }}
            </h2>
            <x-ui.button :href="$isProduk ? route('produk.create') : route('data-material.create')">
                {{ $isProduk ? '+ Tambah Produk' : '+ Tambah Material' }}
            </x-ui.button>
        </div>
    </x-slot>

    <div class="ui-page">
        @if ($message = Session::get('success'))
            <div class="ui-alert-success">{{ $message }}</div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700">{{ $message }}</div>
        @endif

        @if (!$isProduk)
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                <x-ui.card class="border border-cyan-100 bg-white shadow-sm" bodyClass="flex min-h-[112px] items-center gap-3 p-4 sm:p-5">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cyan-50 text-cyan-500 shadow-sm ring-1 ring-cyan-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 2l8 4v8l-8 4-8-4V6l8-4Z" />
                            <path d="M12 22V12" />
                            <path d="M4 6l8 4 8-4" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-600">Total Material</p>
                        <p class="mt-1 text-xs text-slate-500">Jumlah item material yang tersedia.</p>
                        <p class="mt-3 text-3xl font-black leading-none text-slate-900">{{ number_format($summaryTotalItem, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Item</p>
                    </div>
                </x-ui.card>

                <x-ui.card class="border border-sky-100 bg-white shadow-sm" bodyClass="flex min-h-[112px] items-center gap-3 p-4 sm:p-5">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-sky-500 shadow-sm ring-1 ring-sky-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M7 3h10a2 2 0 0 1 2 2v14H5V5a2 2 0 0 1 2-2Z" />
                            <path d="M8 7h8" />
                            <path d="M8 11h8" />
                            <path d="M8 15h5" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-600">Stok Total</p>
                        <p class="mt-1 text-xs text-slate-500">Akumulasi stok saat ini dari semua material.</p>
                        <p class="mt-3 text-3xl font-black leading-none text-slate-900">{{ number_format($summaryTotalStok, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Item</p>
                    </div>
                </x-ui.card>

                <x-ui.card class="border border-emerald-100 bg-white shadow-sm" bodyClass="flex min-h-[112px] items-center gap-3 p-4 sm:p-5">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500 shadow-sm ring-1 ring-emerald-100">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 4l8 14H4L12 4Z" />
                            <circle cx="12" cy="16.5" r="1" fill="currentColor" stroke="none" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-slate-600">Stok Minimum</p>
                        <p class="mt-1 text-xs text-slate-500">Item dengan stok di bawah atau sama dengan {{ $stokMinimumBatas }}.</p>
                        <p class="mt-3 text-3xl font-black leading-none text-slate-900">{{ number_format($summaryStokMinimum, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">Item</p>
                    </div>
                </x-ui.card>
            </div>
        @else
            <x-ui.card bodyClass="flex w-full flex-col items-center justify-center gap-3 py-3 px-6 text-center mb-5">
                <p class="text-xs font-medium text-ink-500">TOTAL PRODUK</p>
                <p class="text-2xl font-bold text-ink-900">
                    {{ number_format($summaryTotalItem, 0, ',', '.') }}
                </p>
                <p class="text-xs text-ink-500">ITEM</p>
            </x-ui.card>
        @endif

        <!-- Search Form -->
        <div class="mb-6">
            <form method="GET" action="{{ request()->url() }}" class="flex gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="{{ $isProduk ? 'Cari nama produk, ukuran, atau satuan...' : 'Cari nama material, ukuran, atau satuan...' }}"
                        class="w-full px-4 py-2.5 border border-ink-200 rounded-lg text-sm placeholder-ink-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                    />
                </div>
                <button
                    type="submit"
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                >
                    Cari
                </button>
                @if ($search)
                    <a
                        href="{{ request()->url() }}"
                        class="px-4 py-2.5 border border-ink-200 text-ink-700 text-sm font-medium rounded-lg hover:bg-ink-50 transition-colors"
                    >
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <x-ui.card class="overflow-hidden" style="height: clamp(560px, 72vh, 760px);" bodyClass="h-full flex flex-col p-0 overflow-hidden">
            <div class="border-b border-ink-100 p-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar {{ $isProduk ? 'Produk' : 'Material' }}</p>
                        <p class="text-xs text-slate-500">Aksi edit dan hapus ada langsung di baris data.</p>
                    </div>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-auto pr-1">
                <table class="compact-scroll-table w-full table-fixed border-collapse">
                    <thead>
                        <tr class="border-b border-ink-200 bg-ink-50">
                            @if ($isProduk)
                                <th class="w-[38%] px-4 py-2 text-left text-sm font-semibold text-ink-600">NAMA PRODUK</th>
                                <th class="w-[14%] px-4 py-2 text-left text-sm font-semibold text-ink-600">UKURAN</th>
                                <th class="w-[14%] px-4 py-2 text-left text-sm font-semibold text-ink-600">SATUAN</th>
                                <th class="w-[30%] whitespace-nowrap px-4 py-2 text-center text-sm font-semibold text-ink-600">AKSI</th>
                            @else
                                <th class="w-[34%] px-4 py-2 text-left text-sm font-semibold text-ink-600">Nama Produk</th>
                                <th class="w-[14%] px-4 py-2 text-left text-sm font-semibold text-ink-600">Ukuran</th>
                                <th class="w-[14%] px-4 py-2 text-left text-sm font-semibold text-ink-600">Satuan</th>
                                <th class="w-[14%] px-4 py-2 text-left text-sm font-semibold text-ink-600">Stok Saat Ini</th>
                                <th class="w-[20%] whitespace-nowrap px-4 py-2 text-center text-sm font-semibold text-ink-600">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barangs as $barang)
                            <tr class="border-b border-ink-100 hover:bg-ink-50 transition-colors">
                                @if ($isProduk)
                                    <td class="w-[40%] align-middle px-4 py-2 text-left text-sm font-medium text-ink-800 break-words">{{ $barang->nama }}</td>
                                    <td class="w-[15%] align-middle px-4 py-2 text-left text-sm font-semibold text-ink-700 whitespace-nowrap">{{ $barang->ukuran ?? '-' }}</td>
                                    <td class="w-[15%] align-middle px-4 py-2 text-left text-sm text-ink-700 whitespace-nowrap">{{ $barang->satuan ?? '-' }}</td>
                                    <td class="w-[30%] align-middle px-4 py-2">
                                        <div class="flex flex-wrap items-center justify-center gap-1.5 whitespace-nowrap">
                                            <x-ui.button :href="route('data-produk.edit', $barang) . '?form_mode=produk'" variant="secondary" class="px-2.5 py-2 text-xs whitespace-nowrap">Edit</x-ui.button>
                                            <form id="deleteForm{{ $barang->getKey() }}" action="{{ route('data-produk.destroy', $barang) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="button" variant="danger" class="px-2.5 py-2 text-xs whitespace-nowrap" onclick="confirmDelete(event, '{{ $barang->getKey() }}', '{{ $barang->nama }}')">Hapus</x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                @else
                                    <td class="w-[35%] align-middle px-4 py-2 text-left text-sm font-medium text-ink-800 break-words">{{ $barang->nama }}</td>
                                    <td class="w-[15%] align-middle px-4 py-2 text-left text-sm font-semibold text-ink-700 whitespace-nowrap">{{ $barang->ukuran ?? '-' }}</td>
                                    <td class="w-[15%] align-middle px-4 py-2 text-left text-sm text-ink-700 whitespace-nowrap">{{ $barang->satuan ?? '-' }}</td>
                                    <td class="w-[15%] align-middle px-4 py-2 text-left text-sm font-semibold text-ink-700 whitespace-nowrap">{{ number_format((int) $barang->display_quantity, 0, ',', '.') }}</td>
                                    <td class="w-[20%] align-middle px-4 py-2">
                                        <div class="flex flex-wrap items-center justify-center gap-1.5 whitespace-nowrap">
                                            <x-ui.button :href="route('data-material.edit', $barang) . '?form_mode=material'" variant="secondary" class="px-2.5 py-2 text-xs whitespace-nowrap">Edit</x-ui.button>
                                            <form id="deleteForm{{ $barang->getKey() }}" action="{{ route('data-material.destroy-barang', $barang) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="button" variant="danger" class="px-2.5 py-2 text-xs whitespace-nowrap" onclick="confirmDelete(event, '{{ $barang->getKey() }}', '{{ $barang->nama }}')">Hapus</x-ui.button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr><td colspan="{{ $isProduk ? 4 : 5 }}" class="px-4 py-8 text-center text-sm text-ink-500">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .compact-scroll-table tbody tr {
        height: 40px;
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
            <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Hapus Data?</h3>
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
