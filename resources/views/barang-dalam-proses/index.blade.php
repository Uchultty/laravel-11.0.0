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

        @php
            $groupedItems = $prosesItems->getCollection()->groupBy(function ($item) {
                return implode('|', [
                    $item->id_pelanggan ?? '-',
                    $item->no_po ?? '-',
                    optional($item->tgl_dibuat)->format('Y-m-d') ?? '-',
                    optional($item->tgl_selesai)->format('Y-m-d') ?? '-',
                ]);
            });
        @endphp

        <x-ui.card class="overflow-hidden" style="height: clamp(620px, 76vh, 840px);" bodyClass="h-full flex flex-col p-0 overflow-hidden">
            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-auto pr-1">
                <x-ui.table class="compact-scroll-table">
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groupedItems as $items)
                            @php
                                $header = $items->first();
                            @endphp
                            <tr>
                                <td class="align-top text-sm font-semibold text-ink-800">
                                    <div class="space-y-1">
                                        @foreach ($items as $item)
                                            <div>{{ $item->produk->nama ?? '-' }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="align-top text-sm text-ink-700">
                                    <div class="space-y-1">
                                        @foreach ($items as $item)
                                            <div>{{ $item->no_gambar ?: '-' }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="align-top text-sm tabular-nums text-ink-700">
                                    <div class="space-y-1">
                                        @foreach ($items as $item)
                                            <div>{{ number_format((int) $item->qty, 0, ',', '.') }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="align-top text-sm text-ink-700">
                                    <div class="space-y-1">
                                        @foreach ($items as $item)
                                            <div>{{ $item->satuan ?: '-' }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="align-top text-sm text-ink-700">
                                    <div class="space-y-1">
                                        @foreach ($items as $item)
                                            <div>{{ $item->ukuran ?: '-' }}</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="align-top text-sm text-ink-700">{{ optional($header->tgl_dibuat)->format('d-m-Y') ?: '-' }}</td>
                                <td class="align-top text-sm text-ink-700">{{ optional($header->tgl_selesai)->format('d-m-Y') ?: '-' }}</td>
                                <td class="align-top text-sm text-ink-700">{{ $header->pelanggan->nama ?? '-' }}</td>
                                <td class="align-top text-sm text-ink-700">{{ $header->no_po ?? '-' }}</td>
                                <td class="align-top text-center">
                                    <form action="{{ route('barang-dalam-proses.prepare-pengiriman-group') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="id_pelanggan" value="{{ $header->id_pelanggan }}">
                                        <input type="hidden" name="no_po" value="{{ $header->no_po }}">
                                        <input type="hidden" name="tgl_dibuat" value="{{ optional($header->tgl_dibuat)->format('Y-m-d') }}">
                                        <input type="hidden" name="tgl_selesai" value="{{ optional($header->tgl_selesai)->format('Y-m-d') }}">

                                        <input
                                            type="checkbox"
                                            class="h-5 w-5 cursor-pointer rounded border-ink-300 accent-emerald-600 focus:ring-emerald-500"
                                            onclick="this.form.submit();"
                                        >
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-8 text-center text-sm text-ink-500">Belum ada barang dalam proses.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-ui.table>
            </div>

            <div class="border-t border-ink-100 p-4">{{ $prosesItems->links() }}</div>
        </x-ui.card>
    </div>

    <style>
        .compact-scroll-table thead th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f8fafc;
        }

        .compact-scroll-table th,
        .compact-scroll-table td {
            padding-top: 0.65rem;
            padding-bottom: 0.65rem;
            white-space: nowrap;
        }

        .compact-scroll-table tbody tr {
            height: 50px;
        }
    </style>
</x-app-layout>
