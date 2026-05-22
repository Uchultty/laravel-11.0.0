<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            Detail Pengiriman Produk
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
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
                    $status = $statusLabels[$pengiriman_produk->status_pengiriman ?? ''] ?? ($pengiriman_produk->status_pengiriman ?? 'Dalam Proses');
                    $badge = $statusBadgeClasses[$status] ?? $statusBadgeClasses['Dalam Proses'];
                @endphp
                <div>
                    <p class="ui-label">Barang</p>
                    <p class="text-sm font-semibold text-ink-900">{{ optional($pengiriman_produk->barang)->kode ?? '-' }} - {{ optional($pengiriman_produk->barang)->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Customer</p>
                    <p class="text-sm text-ink-900">{{ optional($pengiriman_produk->customer)->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Qty</p>
                    <p class="text-sm text-ink-900">{{ $pengiriman_produk->quantity }}</p>
                </div>
                <div>
                    <p class="ui-label">No PO</p>
                    <p class="text-sm text-ink-900">{{ $pengiriman_produk->no_po ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Tanggal Kirim</p>
                    <p class="text-sm text-ink-900">{{ optional($pengiriman_produk->tanggal_keluar)->format('d/m/Y') ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Status</p>
                    <p class="text-sm"><span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">{{ $status }}</span></p>
                </div>
            </div>

            @if ($pengiriman_produk->barang && ($pengiriman_produk->barang->ukuran || $pengiriman_produk->barang->satuan || $pengiriman_produk->material_type))
                <div class="mt-6 border-t border-ink-100 pt-4">
                    <h3 class="text-sm font-semibold text-ink-900 mb-2">Detail Produksi</h3>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <p class="ui-label">Ukuran</p>
                            <p class="text-sm text-ink-900">{{ optional($pengiriman_produk->barang)->ukuran ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="ui-label">Satuan</p>
                            <p class="text-sm text-ink-900">{{ optional($pengiriman_produk->barang)->satuan ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="ui-label">Material</p>
                            <p class="text-sm text-ink-900">{{ $pengiriman_produk->material_type ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($pengiriman_produk->gambar_path)
                <div class="mt-6">
                    <p class="ui-label">Gambar Barang</p>
                    <img src="{{ Storage::url($pengiriman_produk->gambar_path) }}" alt="Gambar Barang" class="mt-2 max-w-sm rounded-xl border border-ink-200">
                </div>
            @endif

            @if ($pengiriman_produk->surat_jalan_path)
                <div class="mt-4">
                    <p class="ui-label">Surat Jalan</p>
                    <a href="{{ Storage::url($pengiriman_produk->surat_jalan_path) }}" target="_blank" class="ui-link">Lihat</a>
                </div>
            @endif

            @if ($pengiriman_produk->invoice_path)
                <div class="mt-4">
                    <p class="ui-label">Invoice</p>
                    <a href="{{ Storage::url($pengiriman_produk->invoice_path) }}" target="_blank" class="ui-link">Lihat</a>
                </div>
            @endif

            <div class="ui-actions mt-6 border-t border-ink-100 pt-4">
                <a href="{{ route('pengiriman-produk.surat-jalan', $pengiriman_produk) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white font-medium hover:bg-indigo-700 transition-colors">Generate Surat Jalan</a>
                <x-ui.button :href="route('pengiriman-produk.edit', $pengiriman_produk)">Edit</x-ui.button>
                <x-ui.button :href="route('pengiriman-produk.index')" variant="secondary">Kembali</x-ui.button>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
