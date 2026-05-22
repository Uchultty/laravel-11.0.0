<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Detail Barang Masuk') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <p class="ui-label">Barang</p>
                    <p class="text-sm font-semibold text-ink-900">{{ optional($barangMasuk->barang)->kode ?? '-' }} - {{ optional($barangMasuk->barang)->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Supplier</p>
                    <p class="text-sm text-ink-900">{{ optional($barangMasuk->supplier)->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Quantity</p>
                    <p class="text-sm text-ink-900">{{ $barangMasuk->quantity }}</p>
                </div>
                <div>
                    <p class="ui-label">Tanggal Masuk</p>
                    <p class="text-sm text-ink-900">{{ $barangMasuk->tanggal_masuk->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="ui-label">Input Oleh</p>
                    <p class="text-sm text-ink-900">{{ optional($barangMasuk->user)->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Tanggal Input</p>
                    <p class="text-sm text-ink-900">{{ $barangMasuk->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            @if ($barangMasuk->gambar_path)
                <div class="mt-6">
                    <p class="ui-label">Gambar Barang</p>
                    <img src="{{ Storage::url($barangMasuk->gambar_path) }}" alt="Gambar Barang" class="mt-2 max-w-sm rounded-xl border border-ink-200">
                </div>
            @endif

            @if ($barangMasuk->surat_jalan_path)
                <div class="mt-4">
                    <p class="ui-label">Surat Jalan</p>
                    <a href="{{ Storage::url($barangMasuk->surat_jalan_path) }}" target="_blank" class="ui-link">Download Surat Jalan</a>
                </div>
            @endif

            @if ($barangMasuk->invoice_path)
                <div class="mt-4">
                    <p class="ui-label">Invoice</p>
                    <a href="{{ Storage::url($barangMasuk->invoice_path) }}" target="_blank" class="ui-link">Download Invoice</a>
                </div>
            @endif

            <div class="ui-actions mt-6 border-t border-ink-100 pt-4">
                <x-ui.button :href="route('barang-masuk.edit', $barangMasuk)">Edit</x-ui.button>
                <x-ui.button :href="route('barang-masuk.index')" variant="secondary">Kembali</x-ui.button>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
