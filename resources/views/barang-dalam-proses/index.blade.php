<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Barang Dalam Proses
            </h2>
            @auth
                <x-ui.button :href="route('barang-dalam-proses.create')">+ Tambah Barang Proses</x-ui.button>
            @endauth
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
                        <th class="text-left">Nama Produk</th>
                        <th class="text-left">No Gambar</th>
                        <th class="text-left">QTY</th>
                        <th class="text-left">Satuan</th>
                        <th class="text-left">Ukuran</th>
                        <th class="text-left">Tanggal Buat</th>
                        <th class="text-left">Tanggal Selesai</th>
                        <th class="text-left">Pelanggan</th>
                        <th class="text-left">No PO</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prosesItems as $item)
                        <tr>
                            <td class="align-middle text-left text-sm font-semibold text-ink-800">
                                @if($item->barang)
                                    <span>{{ $item->barang->nama }}</span>
                                    @if($item->barangMentah)
                                        <p class="mt-1 text-xs text-ink-600">Material: <span class="font-medium">{{ $item->barangMentah->nama }}</span></p>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ $item->no_gambar ?? '-' }}</td>
                            <td class="align-middle text-left text-sm tabular-nums text-ink-700">{{ number_format((int) $item->quantity, 0, ',', '.') }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ $item->satuan ?: '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ $item->ukuran ?: '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ optional($item->created_at)->format('d-m-Y') ?: '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ optional($item->tanggal_selesai)->format('d-m-Y') ?: '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ $item->customer->nama ?? '-' }}</td>
                            <td class="align-middle text-left text-sm text-ink-700">{{ $item->no_po ?? '-' }}</td>
                            <td class="align-middle text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $item->status_lifecycle_class }}">
                                        {{ $item->status_lifecycle }}
                                    </span>
                                    @auth
                                        <label class="inline-flex items-center {{ $item->is_siap_dikirim || $item->is_selesai ? 'cursor-not-allowed' : 'cursor-pointer' }}">
                                            <input
                                                type="checkbox"
                                                class="h-5 w-5 rounded border-ink-300 accent-emerald-600 focus:ring-emerald-500 disabled:cursor-not-allowed"
                                                {{ $item->is_siap_dikirim || $item->is_selesai ? 'checked' : '' }}
                                                {{ $item->is_siap_dikirim || $item->is_selesai ? 'disabled' : '' }}
                                                onclick="window.location.href='{{ route('barang-dalam-proses.prepare-pengiriman', $item) }}'"
                                            >
                                        </label>
                                    @endauth
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                @auth
                                    <div class="flex items-center justify-center gap-2">
                                        <form id="deleteForm{{ $item->id }}" action="{{ route('barang-dalam-proses.destroy', $item) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-ui.button type="button" variant="danger" class="px-3 py-2 text-xs" onclick="confirmDelete(event, '{{ $item->id }}', '{{ $item->barang?->nama ?? 'Barang' }}')">Hapus</x-ui.button>
                                        </form>
                                    </div>
                                @endauth
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 text-center text-sm text-ink-500">Belum ada barang dalam proses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.table>

            <div class="border-t border-ink-100 p-4">{{ $prosesItems->links() }}</div>
        </x-ui.card>
    </div>

    <script>
    function confirmSiapDikirim(event, itemId) {
        // cegah checkbox berubah otomatis
        event.preventDefault();

        // modal elements
        const modal = document.getElementById('statusModal');
        const confirmBtn = document.getElementById('statusConfirmBtn');
        const cancelBtn = document.getElementById('statusCancelBtn');

        // tampilkan modal
        modal.classList.remove('hidden');

        // jika klik YA — tandai siap dikirim
        confirmBtn.onclick = function () {
            document.getElementById('statusForm' + itemId).submit();
        };

        // function tutup modal
        function closeModal() {
            modal.classList.add('hidden');
        }

        // jika klik batal
        cancelBtn.onclick = function () {
            closeModal();
        };

        // klik area luar modal
        modal.onclick = function(e) {
            if (e.target === modal) {
                closeModal();
            }
        };
    }

    function confirmDelete(event, itemId, itemName) {
        event.preventDefault();

        // modal elements
        const modal = document.getElementById('deleteModal');
        const itemNameEl = document.getElementById('deleteItemName');
        const confirmBtn = document.getElementById('deleteConfirmBtn');
        const cancelBtn = document.getElementById('deleteCancelBtn');

        // set item name di modal
        itemNameEl.textContent = itemName;

        // tampilkan modal
        modal.classList.remove('hidden');

        // jika klik YA — submit form
        confirmBtn.onclick = function () {
            const form = document.getElementById('deleteForm' + itemId);
            form.submit();
        };

        // function tutup modal
        function closeModal() {
            modal.classList.add('hidden');
        }

        // jika klik batal
        cancelBtn.onclick = function () {
            closeModal();
        };

        // klik area luar modal
        modal.onclick = function(e) {
            if (e.target === modal) {
                closeModal();
            }
        };
    }
</script>

    <!-- Status Modal -->
    <div id="statusModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-sm rounded-2xl border border-emerald-200 bg-white p-6 shadow-2xl">
            <div class="flex justify-end mb-2">
                <button type="button" class="text-slate-400 hover:text-slate-600" onclick="document.getElementById('statusModal').classList.add('hidden')">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100">
                <svg class="h-6 w-6 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Tandai Siap Dikirim?</h3>
            <p class="text-center text-sm text-slate-600 mb-4">Barang akan tetap berada di halaman proses, tetapi statusnya berubah menjadi siap dikirim.</p>
            <div class="flex gap-3">
                <button type="button" id="statusCancelBtn" class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="button" id="statusConfirmBtn" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition-colors">
                    Ya, Tandai
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
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
            <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Hapus Barang Proses?</h3>
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
