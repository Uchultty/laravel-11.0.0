<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold tracking-tight text-ink-900">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="ui-page">
        <!-- HEADER SECTION -->
        <x-ui.card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-ink-900">Selamat Datang, {{ auth()->user()->name }}</h3>
                    <p class="mt-1 text-sm text-ink-600">Anda login sebagai <span class="font-semibold text-brand-700">Admin</span>.</p>
                    <p class="mt-2 text-sm text-ink-600">Monitoring aktivitas gudang, produksi, dan pengiriman.</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('data-material.create') }}" class="px-3 py-1.5 text-xs font-medium bg-brand-600 text-white rounded-lg hover:bg-brand-700 transition">+ Material</a>
                    <a href="{{ route('barang-dalam-proses.create') }}" class="px-3 py-1.5 text-xs font-medium bg-slate-200 text-slate-900 rounded-lg hover:bg-slate-300 transition">+ Proses</a>
                    <a href="{{ route('pengiriman-produk.create') }}" class="px-3 py-1.5 text-xs font-medium bg-slate-200 text-slate-900 rounded-lg hover:bg-slate-300 transition">+ Pengiriman</a>
                </div>
            </div>
        </x-ui.card>

        @if($deadlineAlertsCount > 0)
            <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-700 text-lg font-bold">
                            !
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-amber-900">Ada {{ $deadlineAlertsCount }} barang dalam proses yang deadline-nya lewat</p>
                            <p class="text-xs text-amber-800">Belum ditandai siap dikirim, cek daftar jika perlu follow up.</p>
                        </div>
                    </div>

                    <a href="{{ route('barang-dalam-proses.index') }}" class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-amber-700 transition sm:self-start">
                        Cek daftar
                    </a>
                </div>
            </div>
        @endif

        <!-- ROW 1: SUMMARY CARDS WITH ICONS -->
        <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-5">
            <div class="rounded-2xl border border-slate-200 p-4 bg-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-ink-500 uppercase tracking-wide">Total Material</div>
                        <div class="mt-2 text-3xl font-bold text-ink-900">{{ number_format($totalMaterials) }}</div>
                    </div>
                    <div class="text-4xl">📦</div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 bg-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-ink-500 uppercase tracking-wide">Stok Minimum</div>
                        <div class="mt-2 text-3xl font-bold text-amber-600">{{ number_format($materialLowStock) }}</div>
                    </div>
                    <div class="text-4xl">⚠</div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 bg-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-ink-500 uppercase tracking-wide">Dalam Proses</div>
                        <div class="mt-2 text-3xl font-bold text-ink-900">{{ number_format($barangDalamProsesCount) }}</div>
                    </div>
                    <div class="text-4xl">🏭</div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 bg-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-ink-500 uppercase tracking-wide">Sedang Dikirim</div>
                        <div class="mt-2 text-3xl font-bold text-emerald-700">{{ number_format($pengirimanTerkirim) }}</div>
                    </div>
                        <div class="text-4xl">
                            <div class="p-2 rounded-full bg-amber-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h13l4 4v6a1 1 0 0 1-1 1h-1a2 2 0 1 1-4 0H9a2 2 0 1 1-4 0H4a1 1 0 0 1-1-1V7z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7v4h4" />
                                </svg>
                            </div>
                        </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-4 bg-white shadow-sm hover:shadow-md transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-medium text-ink-500 uppercase tracking-wide">Selesai</div>
                        <div class="mt-2 text-3xl font-bold text-slate-700">{{ number_format($pengirimanSelesai) }}</div>
                    </div>
                        <div class="text-4xl">
                            <div class="p-2 rounded-full bg-emerald-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <!-- ROW 2: 2-COLUMN BALANCED LAYOUT -->
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- BARANG DALAM PROSES -->
            <x-ui.card>
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-semibold text-ink-900">Barang Dalam Proses</h4>
                    <a href="{{ route('barang-dalam-proses.index') }}" class="text-xs text-brand-600 hover:text-brand-700">Lihat semua →</a>
                </div>

                <div class="mt-4 overflow-x-auto">
                    @if($barangDalamProsesLatest->isEmpty())
                        <div class="py-8 text-center text-ink-500">
                            <p class="text-sm">Belum ada barang dalam proses.</p>
                        </div>
                    @else
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-ink-700 text-xs font-semibold">
                                <tr>
                                    <th class="px-4 py-2">Produk</th>
                                    <th class="px-4 py-2">Customer</th>
                                    <th class="px-4 py-2">Qty</th>
                                    <th class="px-4 py-2">Deadline</th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($barangDalamProsesLatest as $p)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-ink-900">{{ $p->barang->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 text-ink-700">{{ $p->customer->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 text-ink-700">{{ $p->quantity }}</td>
                                        <td class="px-4 py-3 text-ink-700">{{ optional($p->tanggal_selesai)->format('d/m/Y') ?? '-' }}</td>
                                        <td class="px-4 py-3"><span class="inline-block rounded-full px-2.5 py-1 text-xs font-medium {{ $p->status_lifecycle_class ?? 'bg-amber-100 text-amber-700' }}">{{ $p->status_lifecycle }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </x-ui.card>

            <!-- PENGIRIMAN AKTIF -->
            <x-ui.card>
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-semibold text-ink-900">Pengiriman Aktif</h4>
                    <a href="{{ route('pengiriman-produk.index') }}" class="text-xs text-brand-600 hover:text-brand-700">Lihat semua →</a>
                </div>

                <div class="mt-4 overflow-x-auto">
                    @if($pengirimanActive->isEmpty())
                        <div class="py-8 text-center text-ink-500">
                            <p class="text-sm">Belum ada pengiriman aktif.</p>
                        </div>
                    @else
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-ink-700 text-xs font-semibold">
                                <tr>
                                    <th class="px-4 py-2">Produk</th>
                                    <th class="px-4 py-2">Customer</th>
                                    <th class="px-4 py-2">Tanggal Kirim</th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($pengirimanActive as $k)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-3 text-ink-900">{{ $k->barang->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 text-ink-700">{{ $k->customer->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 text-ink-700">{{ optional($k->tanggal_keluar)->format('d/m/Y') ?? '-' }}</td>
                                        <td class="px-4 py-3"><span class="inline-block rounded-full px-2.5 py-1 text-xs font-medium {{ $k->status_badge_class ?? 'bg-amber-100 text-amber-700' }}">{{ $k->status_display ?? $k->status_pengiriman ?? '-' }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </x-ui.card>
        </div>

        <!-- ROW 3: AKTIVITAS TERBARU -->
        <x-ui.card class="mt-6">
            <h4 class="text-lg font-semibold text-ink-900">Aktivitas Terbaru</h4>

            <div class="mt-4 space-y-3">
                @if($activities->isEmpty())
                    <div class="py-6 text-center text-ink-500">
                        <p class="text-sm">Tidak ada aktivitas baru.</p>
                    </div>
                @else
                    @foreach($activities as $act)
                        <div class="flex items-start gap-3 pb-3 border-b last:border-b-0">
                            <div class="text-2xl flex-shrink-0">✔</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-ink-800">{{ $act['text'] }}</p>
                                <p class="mt-1 text-xs text-ink-500">{{ optional($act['time'])->format('d/m/Y H:i') ?? '-' }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
