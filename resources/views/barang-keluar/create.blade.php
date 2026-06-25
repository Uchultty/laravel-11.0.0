<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Tambah Pengiriman Produk') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        {{-- Stock checks removed for make-to-order flow --}}

        <x-ui.card>
            @if($prefillData)
                <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-semibold text-blue-900">✓ Data dari Barang Dalam Proses berhasil dimuat</p>
                    @if(!empty($isGroupPrefill))
                        <p class="text-xs text-blue-700">Total item: {{ count($prefillData['items'] ?? []) }} | Customer: {{ $prefillData['customer_nama'] ?? '-' }} | No PO: {{ $prefillData['no_po'] ?? '-' }}</p>
                    @else
                        <p class="text-xs text-blue-700">Barang: {{ $prefillData['barang_nama'] }} | Qty: {{ $prefillData['quantity'] }} | Customer: {{ $prefillData['customer_nama'] ?? '-' }}</p>
                    @endif
                    <p class="text-xs text-blue-700">Tanggal kirim otomatis: {{ $prefillData['tanggal_pengiriman_default'] ?? now()->addDay()->format('Y-m-d') }}</p>
                </div>
            @endif
            <form action="{{ route('pengiriman-produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" novalidate>
                @csrf

                {{-- Readonly fields dari barang dalam proses --}}
                @if($prefillData)
                    @if(!empty($isGroupPrefill))
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="ui-label">Customer</label>
                                <input type="text" class="ui-input bg-ink-50" value="{{ $prefillData['customer_nama'] ?? '-' }}" disabled />
                            </div>
                            <div>
                                <label for="no_po" class="ui-label">No PO</label>
                                <input type="text" id="no_po" name="no_po" class="ui-input bg-ink-50" value="{{ $prefillData['no_po'] ?? '' }}" readonly />
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200">
                            <div class="overflow-x-auto">
                                <table class="w-full min-w-[680px] text-sm">
                                    <thead class="bg-slate-100 text-slate-700">
                                        <tr>
                                            <th class="px-3 py-2 text-left">Barang</th>
                                            <th class="px-3 py-2 text-left">Material</th>
                                            <th class="px-3 py-2 text-left">No Gambar</th>
                                            <th class="px-3 py-2 text-left">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach(($prefillData['items'] ?? []) as $detail)
                                            <tr>
                                                <td class="px-3 py-2">{{ $detail['barang_nama'] ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $detail['material_nama'] ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $detail['no_gambar'] ?? '-' }}</td>
                                                <td class="px-3 py-2">{{ $detail['quantity'] ?? 0 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="id_barang_proses" value="{{ $prefillData['id_barang_proses'] ?? '' }}">
                        <input type="hidden" name="material_nama_prefill" value="{{ $prefillData['material_nama'] ?? $prefillData['material_kategori_nama'] ?? '' }}">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="ui-label">Barang</label>
                                <input type="text" class="ui-input bg-ink-50" value="{{ $prefillData['barang_nama'] }}" disabled />
                                <input type="hidden" name="id_barang" value="{{ $prefillData['id_barang'] }}">
                            </div>
                            <div>
                                <label class="ui-label">Customer</label>
                                <input type="text" class="ui-input bg-ink-50" value="{{ $prefillData['customer_nama'] ?? '-' }}" disabled />
                                <input type="hidden" name="id_customer" value="{{ $prefillData['id_customer'] }}">
                            </div>
                            <div>
                                <label class="ui-label">Quantity</label>
                                <input type="number" class="ui-input bg-ink-50" value="{{ $prefillData['quantity'] }}" disabled />
                                <input type="hidden" name="quantity" value="{{ $prefillData['quantity'] }}">
                            </div>
                            <div>
                                <label for="no_po" class="ui-label">No PO</label>
                                    <input type="text" id="no_po" name="no_po" class="ui-input bg-ink-50" value="{{ $prefillData['no_po'] ?? '' }}" placeholder="Nomor PO ini ambil dari barang dalam proses" readonly />
                            </div>
                            <div>
                                <label for="no_gambar" class="ui-label">No Gambar</label>
                                <input type="text" id="no_gambar" name="no_gambar" class="ui-input bg-ink-50 flex-1" value="{{ $prefillData['no_gambar'] ?? '' }}" placeholder="No Gambar dari Barang Dalam Proses" readonly />
                            </div>
                            <div>
                                <label class="ui-label">Material</label>
                                <input type="text" class="ui-input bg-ink-50" value="{{ $prefillData['material_nama'] ?? $prefillData['material_kategori_nama'] ?? '-' }}" disabled />
                            </div>
                        </div>
                    @endif

                    {{-- Only editable field: Tanggal Keluar --}}
                        <div>
                        <label for="tanggal_keluar" class="ui-label">Tanggal Kirim <span class="text-rose-500">*</span></label>
                        <x-ui.input type="date" id="tanggal_keluar" name="tanggal_keluar" value="{{ old('tanggal_keluar', $prefillData['tanggal_pengiriman_default'] ?? now()->addDay()->format('Y-m-d')) }}" required />
                        @error('tanggal_keluar') <p class="ui-error">{{ $message }}</p> @enderror
                        </div>

                    <div>
                        <label class="ui-label">Status</label>
                        <div class="mt-2 rounded-lg border border-sand-200 bg-sand-50 px-4 py-3 text-sm font-semibold text-ink-800">
                            Siap Dikirim
                        </div>
                        <input type="hidden" name="status_pengiriman" value="Siap Dikirim">
                    </div>

                @else
                    {{-- Normal form if not from barang dalam proses --}}
                    <div>
                        <label for="id_barang" class="ui-label">Barang</label>
                        <select id="id_barang" name="id_barang" class="ui-input @error('id_barang') border-rose-400 @enderror" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barangs as $barang)
                                <option value="{{ $barang->id_product }}" {{ old('id_barang') == $barang->id_product ? 'selected' : '' }}>
                                    {{ $barang->nama }} ({{ $barang->ukuran ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_barang') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="id_customer" class="ui-label">Customer</label>
                        <select id="id_customer" name="id_customer" class="ui-input @error('id_customer') border-rose-400 @enderror" required>
                            <option value="">-- Pilih Customer --</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id_customer }}" {{ old('id_customer') == $customer->id_customer ? 'selected' : '' }}>
                                    {{ $customer->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_customer') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="quantity" class="ui-label">Quantity <span class="text-rose-500">*</span></label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" placeholder="Masukkan jumlah..." class="ui-input @error('quantity') border-rose-400 @enderror" required />
                        @error('quantity') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="no_po" class="ui-label">No PO</label>
                        <input type="text" id="no_po" name="no_po" placeholder="Nomor PO ini ambil dari barang dalam proses" value="{{ old('no_po') }}" class="ui-input bg-ink-50 @error('no_po') border-rose-400 @enderror" readonly />
                        @error('no_po') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="tanggal_keluar" class="ui-label">Tanggal Kirim</label>
                        <x-ui.input type="date" id="tanggal_keluar" name="tanggal_keluar" value="{{ old('tanggal_keluar', $prefillData['tanggal_pengiriman_default'] ?? now()->addDay()->format('Y-m-d')) }}" required />
                        @error('tanggal_keluar') <p class="ui-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="ui-label">Status</label>
                        <div class="mt-2 rounded-lg border border-sand-200 bg-sand-50 px-4 py-3 text-sm font-semibold text-ink-800">
                            Siap Dikirim
                        </div>
                        <input type="hidden" name="status_pengiriman" value="Siap Dikirim">
                    </div>

                @endif

                <div class="grid grid-cols-2 gap-3 pt-6">
    <x-ui.button type="submit" class="w-full">
        Simpan Pengiriman
    </x-ui.button>

    @if($prefillData)
        @if(!empty($isGroupPrefill))
            <x-ui.button
                type="button"
                variant="secondary"
                class="w-full"
                onclick="document.getElementById('cancelGroupForm').submit()">
                Batal
            </x-ui.button>
        @else
            <x-ui.button
                type="button"
                variant="secondary"
                class="w-full"
                onclick="openCancelReserveModal('{{ $prefillData['id_barang_proses'] }}')">
                Batal
            </x-ui.button>
        @endif
    @else
        <x-ui.button
            type="button"
            variant="secondary"
            class="w-full"
            onclick="window.location.href='{{ route('barang-dalam-proses.index') }}'">
            Batal
        </x-ui.button>
    @endif
</div>
            </form>

@if(!empty($isGroupPrefill))
<form id="cancelGroupForm" action="{{ route('barang-dalam-proses.cancel-reserve-group') }}" method="POST" style="display:none">
    @csrf
</form>
@endif
        </x-ui.card>
    </div>

    {{-- Cancel Reserve Modal --}}
    <div id="cancelReserveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">
        <div class="w-full max-w-sm rounded-2xl border border-amber-200 bg-white p-6 shadow-2xl">
            <div class="flex justify-end mb-2">
                <button type="button" class="text-slate-400 hover:text-slate-600" onclick="document.getElementById('cancelReserveModal').classList.add('hidden')">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100">
                <svg class="h-6 w-6 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-center text-lg font-bold text-slate-900 mb-2">Batalkan Reservasi?</h3>
            <p class="text-center text-sm text-slate-600 mb-4">Reservasi untuk barang dalam proses akan dibatalkan dan Anda kembali ke halaman Barang Dalam Proses.</p>
            <div class="flex gap-3">
                <button type="button" class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" onclick="document.getElementById('cancelReserveModal').classList.add('hidden')">
                    Tidak, Lanjutkan
                </button>
                <button type="button" id="confirmCancelBtn" class="flex-1 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">
                    Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function setupProductSearch(selectId) {
        const select = document.getElementById(selectId);

        if (!select || select.dataset.searchEnhanced) {
            return;
        }

        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Cari produk...';
        searchInput.className = 'ui-input mb-2';
        select.parentElement.insertBefore(searchInput, select);

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.trim().toLowerCase();
            const selectedValue = select.value;

            Array.from(select.options).forEach(function(option, index) {
                if (index === 0 || !option.value) {
                    option.hidden = false;
                    return;
                }

                const matches = option.text.toLowerCase().includes(query);
                option.hidden = !matches && option.value !== selectedValue;
            });
        });

        select.dataset.searchEnhanced = '1';
    }

    function openCancelReserveModal(idBarangProses) {
        const modal = document.getElementById('cancelReserveModal');
        const confirmBtn = document.getElementById('confirmCancelBtn');

        modal.classList.remove('hidden');

        confirmBtn.onclick = function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('barang-dalam-proses.index') }}/" + idBarangProses + "/cancel-reserve";
            form.style.display = 'none';

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';

            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        };
    }

    window.addEventListener('load', () => {
        setupProductSearch('id_barang');
    });

    // Auto-fill No PO when selecting a Barang (uses production items with status_kirim and no_po)
    try {
        const poItems = @json($poItems ?? []);
        const poMap = {};
        poItems.forEach(pi => {
            if (!poMap[pi.id_produk]) poMap[pi.id_produk] = pi.no_po;
        });

        const idBarangSelect = document.getElementById('id_barang');
        if (idBarangSelect) {
            idBarangSelect.addEventListener('change', function () {
                const selected = this.value;
                const noPoInput = document.getElementById('no_po');
                if (noPoInput) {
                    noPoInput.value = poMap[selected] ?? '';
                }
            });
        }
    } catch (e) {
        // silent fail if JSON not present
    }

</script>
