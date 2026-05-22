<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                Detail Persediaan Material
            </h2>
        </div>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            @php
                $displayTanggal = optional($barangMasuk->tgl_pemesanan)->format('d/m/Y') ?? '-';
                $displayEstimasi = optional($barangMasuk->estimasi_tiba)->format('d/m/Y') ?? '-';
            @endphp

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <p class="ui-label">No PO</p>
                    <p class="text-sm font-semibold text-ink-900">{{ $barangMasuk->no_po ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Tanggal Pemesanan</p>
                    <p class="text-sm text-ink-900">{{ $displayTanggal }}</p>
                </div>
                <div>
                    <p class="ui-label">Nama Material</p>
                    <p class="text-sm text-ink-900">{{ optional($barangMasuk->material)->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">QTY</p>
                    <p class="text-sm text-ink-900">{{ number_format($barangMasuk->qty) }}</p>
                </div>
                <div>
                    <p class="ui-label">Satuan</p>
                    <p class="text-sm text-ink-900">{{ $barangMasuk->satuan ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Supplier</p>
                    <p class="text-sm text-ink-900">{{ optional($barangMasuk->supplier)->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="ui-label">Estimasi Tiba</p>
                    <p class="text-sm text-ink-900">{{ $displayEstimasi }}</p>
                </div>
            </div>

            <div class="mt-6 border-t border-ink-100 pt-4">
                <h3 class="mb-3 text-sm font-semibold text-ink-900">File Pendukung</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <p class="ui-label">Surat Jalan</p>
                        @if ($barangMasuk->surat_jalan_path)
                            <a href="{{ Storage::url($barangMasuk->surat_jalan_path) }}" target="_blank" class="ui-link">Lihat</a>
                        @else
                            <p class="text-sm text-ink-400">-</p>
                        @endif
                    </div>

                    <div>
                        <p class="ui-label">Invoice</p>
                        @if ($barangMasuk->invoice_path)
                            <a href="{{ Storage::url($barangMasuk->invoice_path) }}" target="_blank" class="ui-link">Lihat</a>
                        @else
                            <p class="text-sm text-ink-400">-</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ui-actions mt-6 border-t border-ink-100 pt-4">
                <x-ui.button :href="route('persediaan-material.edit', $barangMasuk)">Edit</x-ui.button>
                <form id="deleteForm{{ $barangMasuk->id_pemesanan }}" action="{{ route('persediaan-material.destroy', $barangMasuk) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="button" variant="danger" onclick="confirmDelete(event, '{{ $barangMasuk->id_pemesanan }}', '{{ optional($barangMasuk->material)->nama ?? 'Material' }}')">Hapus</x-ui.button>
                </form>
                <x-ui.button :href="route('persediaan-material.index')" variant="secondary">Kembali</x-ui.button>
            </div>
        </x-ui.card>
    </div>

    <script>
    function confirmDelete(event, itemId, itemName) {
        event.preventDefault();
        if (window.confirm('Yakin ingin menghapus ' + itemName + '? Tindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('deleteForm' + itemId).submit();
        }
    }
    </script>
</x-app-layout>
