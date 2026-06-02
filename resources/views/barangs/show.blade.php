<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-2xl font-bold tracking-tight text-ink-900">
                {{ __('Detail Barang: ') }}{{ $barang->nama }}
            </h2>
            <div class="flex items-center gap-2">
                <x-ui.button :href="($barang->status === 'material' ? route('data-material.edit', $barang) : route('data-produk.edit', $barang)) . '?form_mode=' . ($barang->status === 'material' ? 'material' : 'produk')">Edit</x-ui.button>
                <x-ui.button :href="$barang->status === 'material' ? route('data-material.index') : route('data-produk.index')" variant="secondary">Kembali</x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="ui-page">
        <x-ui.card>
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <dt class="ui-label">Kode Barang</dt>
                    <dd class="font-mono text-sm text-ink-800">{{ $barang->kode }}</dd>
                </div>
                <div>
                    <dt class="ui-label">Nama Barang</dt>
                    <dd class="text-sm text-ink-900">{{ $barang->nama }}</dd>
                </div>
            </dl>

            @if ($barang instanceof \App\Models\Product && $barang->materials->isNotEmpty())
                <div class="mt-6 border-t border-ink-100 pt-4">
                    <h3 class="text-sm font-semibold text-ink-900">Material Penyusun</h3>
                    <div class="mt-3 overflow-x-auto rounded-xl border border-sand-200">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-sand-50 text-left text-ink-600">
                                    <th class="px-4 py-3 font-semibold">Material</th>
                                    <th class="px-4 py-3 font-semibold">Ukuran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang->materials as $material)
                                    <tr class="border-t border-sand-100">
                                        <td class="px-4 py-3 text-ink-900">{{ $material->nama }}</td>
                                        <td class="px-4 py-3 text-ink-600">{{ filled($material->ukuran) ? $material->ukuran : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mt-6 border-t border-ink-100 pt-4 text-xs text-ink-500">
                <p>Dibuat: {{ $barang->created_at }}</p>
                <p class="mt-1">Diperbarui: {{ $barang->updated_at }}</p>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
