<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Edit Pengiriman Produk') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        {{-- Stock checks removed for make-to-order flow --}}

        <x-ui.card>
            <form action="{{ route('pengiriman-produk.update', $pengiriman_produk) }}" method="POST" enctype="multipart/form-data" class="space-y-4" novalidate>
                @csrf
                @method('PUT')

                <div>
                    <label for="id_barang" class="ui-label">Barang</label>
                    <select id="id_barang_display" class="ui-input bg-ink-50 text-ink-700" disabled>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id_product }}" {{ $pengiriman_produk->id_produk == $barang->id_product ? 'selected' : '' }}>
                                {{ $barang->nama }} ({{ $barang->ukuran ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="id_barang" value="{{ $pengiriman_produk->id_produk }}">
                    <p class="mt-1 text-xs text-slate-500">Barang dikunci pada pengiriman yang sudah dibuat.</p>
                    @error('id_barang') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="id_customer" class="ui-label">Pelanggan</label>
                    <select id="id_customer_display" class="ui-input bg-ink-50 text-ink-700" disabled>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id_customer }}" {{ $pengiriman_produk->id_customer == $customer->id_customer ? 'selected' : '' }}>
                                {{ $customer->nama }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="id_customer" value="{{ $pengiriman_produk->id_customer }}">
                    <p class="mt-1 text-xs text-slate-500">Pelanggan dikunci agar data pengiriman tetap konsisten.</p>
                    @error('id_customer') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="quantity" class="ui-label">Quantity</label>
                    <x-ui.input type="number" id="quantity" name="quantity" value="{{ $pengiriman_produk->quantity }}" readonly class="bg-ink-50 text-ink-700" />
                    <p class="mt-1 text-xs text-slate-500">Qty mengikuti jumlah produksi/pengiriman yang sudah ditetapkan.</p>
                    @error('quantity') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_po" class="ui-label">No PO</label>
                    <x-ui.input type="text" id="no_po" name="no_po" value="{{ $pengiriman_produk->no_po ?? '' }}" />
                    @error('no_po') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="no_gambar" class="ui-label">No Gambar</label>
                    <x-ui.input type="text" id="no_gambar" name="no_gambar" value="{{ $pengiriman_produk->no_gambar ?? '' }}" />
                    @error('no_gambar') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tanggal_keluar" class="ui-label">Tanggal Kirim</label>
                    <x-ui.input type="date" id="tanggal_keluar" name="tanggal_keluar" value="{{ $pengiriman_produk->tanggal_keluar->format('Y-m-d') }}" required />
                    @error('tanggal_keluar') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status_pengiriman" class="ui-label">Status</label>
                    @php
                        $statusOptions = ['Siap Dikirim','Sedang Dikirim','Selesai'];
                        $selectedStatus = $pengiriman_produk->status_pengiriman ?? 'Siap Dikirim';
                    @endphp
                    <select id="status_pengiriman" name="status_pengiriman" class="ui-input">
                        @foreach($statusOptions as $opt)
                            <option value="{{ $opt }}" {{ $selectedStatus === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                    @error('status_pengiriman') <p class="ui-error">{{ $message }}</p> @enderror
                </div>

                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">File Pendukung</h3>

                    <div class="mb-4">
                        <label for="surat_jalan" class="block text-sm font-medium text-slate-700 mb-1">Surat Jalan</label>
                        @if ($pengiriman_produk->surat_jalan_path)
                            <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700">
                                    File saat ini:
                                    <a href="{{ Storage::url($pengiriman_produk->surat_jalan_path) }}" target="_blank" class="font-medium underline">Lihat File</a>
                                </p>
                            </div>
                        @endif
                        <input type="file" id="surat_jalan" name="surat_jalan" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('surat_jalan') border-rose-400 @enderror">
                        <p class="text-xs text-slate-500 mt-1">Format: PDF, JPG, PNG (Max 5MB)</p>
                        @error('surat_jalan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="invoice" class="block text-sm font-medium text-slate-700 mb-1">Invoice</label>
                        @if ($pengiriman_produk->invoice_path)
                            <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700">
                                    File saat ini:
                                    <a href="{{ Storage::url($pengiriman_produk->invoice_path) }}" target="_blank" class="font-medium underline">Lihat File</a>
                                </p>
                            </div>
                        @endif
                        <input type="file" id="invoice" name="invoice" accept=".jpg,.jpeg,.png,.pdf" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none @error('invoice') border-rose-400 @enderror">
                        <p class="text-xs text-slate-500 mt-1">Format: JPG, PNG, PDF (Max 5MB)</p>
                        @error('invoice') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="ui-actions">
                    <x-ui.button type="submit">Perbarui</x-ui.button>
                    <x-ui.button :href="route('pengiriman-produk.index')" variant="secondary">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
