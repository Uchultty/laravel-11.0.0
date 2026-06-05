<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                {{ __('Master Data Pelanggan') }}
            </h2>
            <x-ui.button :href="route('pelanggan.create')">
                + Tambah Pelanggan
            </x-ui.button>
        </div>
    </x-slot>

    <div class="ui-page">
        @if ($message = Session::get('success'))
            <div class="ui-alert-success">{{ $message }}</div>
        @endif
        @if ($message = Session::get('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $message }}</div>
        @endif

        <x-ui.card class="overflow-hidden" style="height: clamp(540px, 70vh, 740px);" bodyClass="h-full flex flex-col p-0 overflow-hidden">
            <div class="border-b border-ink-100 p-4">
                <form method="GET" action="{{ route('pelanggan.index') }}" class="flex gap-2">
                    <input type="text" name="q" placeholder="Cari nama pelanggan..." value="{{ $search }}" class="flex-1 px-3 py-2 border border-ink-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-sand-500" />
                    <button type="submit" class="px-4 py-2 bg-sand-600 text-white rounded-lg text-sm font-medium hover:bg-sand-700 transition-colors">Cari</button>
                    @if ($search)
                        <a href="{{ route('pelanggan.index') }}" class="px-4 py-2 bg-ink-200 text-ink-700 rounded-lg text-sm font-medium hover:bg-ink-300 transition-colors">Reset</a>
                    @endif
                </form>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-auto pr-1">
                <x-ui.table class="compact-scroll-table">
                    <thead>
                        <tr>
                            <th>Nama Pelanggan</th>
                            <th>PIC</th>
                            <th>Alamat</th>
                            <th>Kontak</th>
                            <th>Email</th>
                            <th class="w-44 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="font-semibold text-ink-800">{{ $customer->nama }}</td>
                                <td>{{ $customer->pic ? mb_strtoupper($customer->pic) : '-' }}</td>
                                <td>{{ $customer->alamat ?: '-' }}</td>
                                <td>{{ $customer->kontak ?: '-' }}</td>
                                <td>{{ $customer->email ?: '-' }}</td>
                                <td class="w-44 align-middle text-center">
                                    <div class="flex flex-wrap items-center justify-center gap-2 whitespace-nowrap">
                                        <x-ui.button
                                            :href="route('pelanggan.edit', ['pelanggan' => $customer])"
                                            variant="secondary"
                                            class="px-3 py-2 text-xs whitespace-nowrap"
                                        >
                                            Edit
                                        </x-ui.button>

                                        <form
                                            id="deleteForm{{ $customer->id_pelanggan }}"
                                            action="{{ route('pelanggan.destroy', $customer) }}"
                                            method="POST"
                                            class="inline"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <x-ui.button
                                                type="button"
                                                variant="danger"
                                                class="px-3 py-2 text-xs whitespace-nowrap"
                                                onclick="confirmDelete(event, '{{ $customer->id_pelanggan }}', '{{ $customer->nama }}')"
                                            >
                                                Hapus
                                            </x-ui.button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-ink-500">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>
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
        padding-top: 0.625rem;
        padding-bottom: 0.625rem;
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
            <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Hapus Pelanggan?</h3>
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
