<nav class="flex h-full flex-col bg-white border-r border-blue-100">
    <!-- Logo dan Header -->
    <div class="border-b border-blue-100 px-6 py-6 bg-gradient-to-r from-blue-50 to-indigo-50" :class="sidebarCollapsed ? 'lg:px-4' : 'lg:px-6'">
        <div class="flex items-start justify-between gap-3">
            <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 transition-all duration-200">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/90 p-1 shadow-sm group-hover:shadow-md transition-all sm:h-13 sm:w-13 lg:h-14 lg:w-14">
                    <img src="{{ asset('images/logo-mab.jpeg') }}" alt="PT. Metal Amanah Baru" class="h-full w-full object-contain" />
                </div>
                <div class="min-w-0" x-cloak x-show="!sidebarCollapsed">
                    <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Pencatatan Gudang</p>
                    <p class="text-base font-bold text-slate-900">{{ config('app.name', 'PT. Metal Amanah Baru') }}</p>
                </div>
            </a>
        </div>
    </div>

    <!-- User Info -->
    <div class="border-b border-blue-100 px-6 py-4 bg-gradient-to-r from-blue-50/50 to-indigo-50/50">
        <div class="flex items-center gap-3 rounded-lg bg-blue-100/40 px-4 py-3 border border-blue-100/60" :class="sidebarCollapsed ? 'lg:justify-center lg:px-3' : ''">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-400 to-blue-500 text-xs font-bold text-white shadow-md">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1" x-cloak x-show="!sidebarCollapsed">
                <p class="truncate text-sm font-semibold text-slate-900">{{ Auth::user()->name }}</p>
            </div>
        </div>
    </div>

    <!-- Main Menu -->
    <!-- Removed flex-1 so footer (Keluar) appears directly after menu instead of being pushed to the bottom -->
    <div class="overflow-y-auto px-4 py-6">
        @php
            $mainMenuClasses = 'group relative mt-1 flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200';
            $mainMenuActive = 'bg-blue-500/15 text-blue-600 border-l-2 border-blue-500 pl-2.5';
            $mainMenuInactive = 'text-slate-600 hover:text-blue-600 hover:bg-blue-50';

            $pengirimanProdukRouteExists = Route::has('pengiriman-produk.index');
            $laporanStokMaterialRouteExists = Route::has('laporan-stok-material.index') || Route::has('laporan-barang.index');
            $laporanPengirimanProdukRouteExists = Route::has('laporan-pengiriman-produk.index');

            $pengirimanProdukHref = Route::has('pengiriman-produk.index')
                ? route('pengiriman-produk.index')
                : '#';
            $laporanStokMaterialHref = Route::has('laporan-stok-material.index')
                ? route('laporan-stok-material.index')
                : (Route::has('laporan-barang.index') ? route('laporan-barang.index') : '#');
            $laporanPengirimanProdukHref = Route::has('laporan-pengiriman-produk.index')
                ? route('laporan-pengiriman-produk.index')
                : '#';

            $persediaanSubmenuClasses = 'group relative mt-1 flex items-center gap-2 rounded-md px-2.5 py-1.5 text-xs font-medium transition-all duration-200';
            $persediaanSubmenuActive = 'bg-blue-500/10 text-blue-600';
            $persediaanSubmenuInactive = 'text-slate-600 hover:text-blue-600 hover:bg-blue-50';
        @endphp

        <div>
            <a href="{{ route('dashboard') }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('dashboard', 'admin.dashboard') ? $mainMenuActive : $mainMenuInactive }}">
                <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9M9 21h6" />
                </svg>
                <span x-cloak x-show="!sidebarCollapsed">Dashboard</span>
                @if(request()->routeIs('dashboard', 'admin.dashboard'))
                    <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                @endif
            </a>
        </div>

        <!-- Data Master -->
        @auth
            <div class="mt-8">
                    <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-600" x-cloak x-show="!sidebarCollapsed">Data Master</p>

                <!-- Suppliers -->
                <a href="{{ route('suppliers.index') }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('suppliers.*') ? $mainMenuActive : $mainMenuInactive }}">
                    <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span x-cloak x-show="!sidebarCollapsed">Data Suppliers</span>
                    @if(request()->routeIs('suppliers.*'))
                            <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                    @endif
                </a>

                <!-- Pelanggan -->
                <a href="{{ route('pelanggan.index') }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('pelanggan.*') ? $mainMenuActive : $mainMenuInactive }}">
                    <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.488M15 10a3 3 0 11-6 0 3 3 0 016 0zM15 20h4a2 2 0 002-2v-2a4 4 0 00-4-4H9a4 4 0 00-4 4v2a2 2 0 002 2h4" />
                    </svg>
                    <span x-cloak x-show="!sidebarCollapsed">Data Pelanggan</span>
                    @if(request()->routeIs('pelanggan.*'))
                            <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                    @endif
                </a>

                <!-- Data Material -->
                <a href="{{ route('data-material.index') }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('data-material.*') ? $mainMenuActive : $mainMenuInactive }}">
                    <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h10" />
                    </svg>
                    <span x-cloak x-show="!sidebarCollapsed">Data Material</span>
                    @if(request()->routeIs('data-material.*'))
                            <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                    @endif
                </a>

                <!-- Produk -->
                <a href="{{ route('data-produk.index') }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('data-produk.*') ? $mainMenuActive : $mainMenuInactive }}">
                    <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span x-cloak x-show="!sidebarCollapsed">Data Produk</span>
                    @if(request()->routeIs('data-produk.*'))
                            <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                    @endif
                </a>
            </div>
        @endif

        <!-- Menu Utama -->
        <div class="mt-8">
            <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-600" x-cloak x-show="!sidebarCollapsed">Menu Utama</p>

            <a href="{{ route('persediaan-material.index') }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('persediaan-material.*') ? $mainMenuActive : $mainMenuInactive }}">
                <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m0 0l8-4m0 6l-8 4m0 0l-8-4m0 0v6a2 2 0 001.972 2h12.056A2 2 0 0020 13v-6" />
                </svg>
                <span x-cloak x-show="!sidebarCollapsed">Pemesanan Material</span>
                @if(request()->routeIs('persediaan-material.*'))
                    <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                @endif
            </a>

            <a href="{{ route('barang-dalam-proses.index') }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('barang-dalam-proses.*') ? $mainMenuActive : $mainMenuInactive }}">
                <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h8M8 11h8M8 15h5M5 3h14a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V5a2 2 0 012-2z" />
                </svg>
                <span x-cloak x-show="!sidebarCollapsed">Barang Dalam Proses</span>
                @if(request()->routeIs('barang-dalam-proses.*'))
                    <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                @endif
            </a>

            <a href="{{ $pengirimanProdukHref }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('pengiriman-produk.*') ? $mainMenuActive : $mainMenuInactive }} {{ $pengirimanProdukRouteExists ? '' : 'opacity-60 cursor-not-allowed pointer-events-none' }}">
                <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6m-6-4h10m-10-4h10M5 7h.01M5 11h.01M5 15h.01M4 21h16a1 1 0 001-1V4a1 1 0 00-1-1H4a1 1 0 00-1 1v16a1 1 0 001 1z" />
                </svg>
                <span x-cloak x-show="!sidebarCollapsed">Pengiriman Produk</span>
                @if(request()->routeIs('pengiriman-produk.*'))
                    <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                @endif
            </a>
        </div>

        <!-- Laporan -->
        <div class="mt-8">
            <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-600" x-cloak x-show="!sidebarCollapsed">Laporan</p>

            <a href="{{ $laporanStokMaterialHref }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('laporan-stok-material.*') ? $mainMenuActive : $mainMenuInactive }} {{ $laporanStokMaterialRouteExists ? '' : 'opacity-60 cursor-not-allowed pointer-events-none' }}">
                <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6m3 6V7m3 10v-4m4 8H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
                </svg>
                <span x-cloak x-show="!sidebarCollapsed">Laporan Stok Material</span>
                @if(request()->routeIs('laporan-stok-material.*'))
                    <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                @endif
            </a>

            <a href="{{ $laporanPengirimanProdukHref }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('laporan-pengiriman-produk.*') ? $mainMenuActive : $mainMenuInactive }} {{ $laporanPengirimanProdukRouteExists ? '' : 'opacity-60 cursor-not-allowed pointer-events-none' }}">
                <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                </svg>
                <span x-cloak x-show="!sidebarCollapsed">Laporan Pengiriman Produk</span>
                @if(request()->routeIs('laporan-pengiriman-produk.*'))
                    <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                @endif
            </a>
        </div>

        <!-- Admin Menu -->
        @auth
            <div class="mt-8">
                    <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-wider text-slate-600" x-cloak x-show="!sidebarCollapsed">Administrasi</p>

                <!-- Pengguna -->
                <a href="{{ route('users.index') }}" class="{{ $mainMenuClasses }} {{ request()->routeIs('users.*') ? $mainMenuActive : $mainMenuInactive }}">
                    <svg class="h-5 w-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.048M12 4.354L9.172 8.22M12 4.354l2.828 3.866M5.338 8.59a4 4 0 110 8.048M5.338 8.59L2.11 12.646m3.228 3.056l3.228 3.884m6.908-7.94l3.228 3.884m-3.228 3.056l3.228-3.884M18.662 8.59a4 4 0 110 8.048m0 0l3.228 4.056" />
                    </svg>
                    <span x-cloak x-show="!sidebarCollapsed">Pengguna</span>
                    @if(request()->routeIs('users.*'))
                            <div class="absolute inset-y-0 right-0 w-1 rounded-r-lg bg-blue-500"></div>
                    @endif
                </a>
            </div>
        @endif
    </div>

    <!-- Footer - User Actions -->
    <div class="border-t border-blue-100 p-4 bg-gradient-to-r from-blue-50/30 to-indigo-50/30">
        <div class="space-y-2">
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="group flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 transition-all duration-200 hover:bg-red-100/60 hover:text-red-700">
                    <svg class="h-4 w-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-cloak x-show="!sidebarCollapsed">Keluar</span>
                </button>
            </form>
        </div>
    </div>
</nav>
