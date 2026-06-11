<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Detail Pengiriman Produk
            </h2>
            <div class="flex items-center gap-2">
                <x-ui.button :href="route('pengiriman-produk.surat-jalan', $pengiriman_produk)" target="_blank">Buat Surat Jalan</x-ui.button>
                <x-ui.button :href="route('pengiriman-produk.edit', $pengiriman_produk)">Edit</x-ui.button>
                <x-ui.button :href="route('pengiriman-produk.index')" variant="secondary">Kembali</x-ui.button>
            </div>
        </div>
    </x-slot>

    @php
        $statusLabels = [
            'Menunggu Pengiriman' => 'Dalam Proses',
            'Dalam Pengiriman'    => 'Siap Dikirim',
        ];
        $statusBadgeClasses = [
            'Dalam Proses'   => 'bg-amber-100 text-amber-800',
            'Siap Dikirim'   => 'bg-blue-100 text-blue-800',
            'Sedang Dikirim' => 'bg-emerald-100 text-emerald-800',
            'Selesai'        => 'bg-slate-100 text-slate-800',
        ];
        $status = $statusLabels[$pengiriman_produk->status_pengiriman ?? ''] ?? ($pengiriman_produk->status_pengiriman ?? 'Dalam Proses');
        $badge  = $statusBadgeClasses[$status] ?? $statusBadgeClasses['Dalam Proses'];
    @endphp

    <div class="ui-page space-y-4">

        {{-- Informasi Utama --}}
        <x-ui.card>
            <h3 class="text-sm font-semibold text-ink-900 mb-4">Informasi Pengiriman</h3>
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="ui-label">Pelanggan</dt>
                    <dd class="text-sm text-ink-900">{{ optional($pengiriman_produk->customer)->nama ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="ui-label">No PO</dt>
                    <dd class="text-sm text-ink-900">{{ $pengiriman_produk->no_po ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="ui-label">No Gambar</dt>
                    <dd class="text-sm text-ink-900">
                        @if (!empty($pengiriman_produk->items) && is_array($pengiriman_produk->items) && count($pengiriman_produk->items) > 1)
                            @foreach($pengiriman_produk->items as $it)
                                <div>{{ $it['no_gambar'] ?? '-' }}</div>
                            @endforeach
                        @else
                            {{ $pengiriman_produk->no_gambar ?? '-' }}
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="ui-label">Qty</dt>
                    <dd class="text-sm text-ink-900">{{ $pengiriman_produk->quantity }}</dd>
                </div>
                <div>
                    <dt class="ui-label">Tanggal Kirim</dt>
                    <dd class="text-sm text-ink-900">{{ optional($pengiriman_produk->tanggal_keluar)->format('d/m/Y') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="ui-label">Status</dt>
                    <dd>
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">
                            {{ $status }}
                        </span>
                    </dd>
                </div>
            </dl>
        </x-ui.card>

        {{-- Detail Produksi --}}
        @if ($productionItems->isNotEmpty())
            <x-ui.card>
                <h3 class="text-sm font-semibold text-ink-900 mb-4">Detail Produksi</h3>
                <div class="overflow-x-auto rounded-xl border border-sand-200">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-sand-50 text-left text-ink-600">
                                <th class="px-4 py-2 font-semibold">Produk</th>
                                <th class="px-4 py-2 font-semibold">Ukuran</th>
                                <th class="px-4 py-2 font-semibold">Satuan</th>
                                <th class="px-4 py-2 font-semibold">Material</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengiriman_produk->items as $it)
                                @php $pi = $productionItems->get($it['id_barang_proses'] ?? null); @endphp
                                <tr class="border-t border-sand-100">
                                    <td class="px-4 py-2 text-ink-900 font-medium">{{ optional($pi?->produk)->nama ?? '-' }}</td>
                                    <td class="px-4 py-2 text-ink-900">{{ $pi?->ukuran ?? '-' }}</td>
                                    <td class="px-4 py-2 text-ink-900">{{ $pi?->satuan ?? '-' }}</td>
                                    <td class="px-4 py-2 text-ink-900">{{ $it['material_type'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @elseif ($pengiriman_produk->barang && ($pengiriman_produk->barang->ukuran || $pengiriman_produk->barang->satuan || $pengiriman_produk->material_type))
            <x-ui.card>
                <h3 class="text-sm font-semibold text-ink-900 mb-4">Detail Produksi</h3>
                <dl class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <dt class="ui-label">Ukuran</dt>
                        <dd class="text-sm text-ink-900">{{ optional($pengiriman_produk->barang)->ukuran ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="ui-label">Satuan</dt>
                        <dd class="text-sm text-ink-900">{{ optional($pengiriman_produk->barang)->satuan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="ui-label">Material</dt>
                        <dd class="text-sm text-ink-900">{{ $pengiriman_produk->material_type ?? '-' }}</dd>
                    </div>
                </dl>
            </x-ui.card>
        @endif

        {{-- Item Pengiriman --}}
        @if (! empty($pengiriman_produk->items) && is_array($pengiriman_produk->items))
            <x-ui.card>
                <h3 class="text-sm font-semibold text-ink-900 mb-4">Item Pengiriman</h3>
                <div class="overflow-x-auto rounded-xl border border-sand-200">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="bg-sand-50 text-left text-ink-600">
                                <th class="px-4 py-2 font-semibold">No</th>
                                <th class="px-4 py-2 font-semibold">Produk</th>
                                <th class="px-4 py-2 font-semibold">Qty</th>
                                <th class="px-4 py-2 font-semibold">No Gambar</th>
                                <th class="px-4 py-2 font-semibold">Material</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pengiriman_produk->items as $i => $it)
                                @php $prod = \App\Models\Product::find($it['id_produk'] ?? null); @endphp
                                <tr class="border-t border-sand-100 hover:bg-sand-50/50 transition-colors">
                                    <td class="px-4 py-2 text-ink-500">{{ $i + 1 }}</td>
                                    <td class="px-4 py-2 text-ink-900 font-medium">{{ optional($prod)->nama ?? '-' }}</td>
                                    <td class="px-4 py-2 text-ink-900">{{ $it['qty'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-ink-600">{{ $it['no_gambar'] ?? '-' }}</td>
                                    <td class="px-4 py-2 text-ink-600">{{ $it['material_type'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        @endif

        {{-- Dokumen --}}
        @if ($pengiriman_produk->gambar_path || $pengiriman_produk->surat_jalan_path || $pengiriman_produk->invoice_path)
            <x-ui.card>
                <h3 class="text-sm font-semibold text-ink-900 mb-4">Dokumen</h3>
                <div class="space-y-4">
                    @if ($pengiriman_produk->gambar_path)
                        <div>
                            <p class="ui-label mb-2">Gambar Barang</p>
                            <img src="{{ Storage::url($pengiriman_produk->gambar_path) }}" alt="Gambar Barang" class="max-w-sm rounded-xl border border-ink-200">
                        </div>
                    @endif
                    @if ($pengiriman_produk->surat_jalan_path || $pengiriman_produk->invoice_path)
                        <div class="flex flex-wrap gap-4 {{ $pengiriman_produk->gambar_path ? 'pt-4 border-t border-ink-100' : '' }}">
                            @if ($pengiriman_produk->surat_jalan_path)
                                <div>
                                    <p class="ui-label mb-1">Surat Jalan</p>
                                    <a href="{{ Storage::url($pengiriman_produk->surat_jalan_path) }}" target="_blank" class="ui-link">Lihat</a>
                                </div>
                            @endif
                            @if ($pengiriman_produk->invoice_path)
                                <div>
                                    <p class="ui-label mb-1">Invoice</p>
                                    <a href="{{ Storage::url($pengiriman_produk->invoice_path) }}" target="_blank" class="ui-link">Lihat</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </x-ui.card>
        @endif

    </div>
</x-app-layout>
