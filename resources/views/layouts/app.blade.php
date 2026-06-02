<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PT. Metal Amanah Baru') }}</title>

        <style>[x-cloak] { display: none !important; }</style>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <!-- background global halaman utama -->
    <body class="font-sans antialiased bg-slate-100" x-data="{ mobileSidebarOpen: false, sidebarCollapsed: false }">
        <div class="h-screen md:flex">
            <!-- Mobile Sidebar Overlay -->
            <div x-show="mobileSidebarOpen" x-transition.opacity class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm lg:hidden" @click="mobileSidebarOpen = false"></div>


            <!-- Sidebar -->
            <aside
                class="fixed inset-y-0 left-0 z-40 transform bg-white shadow-xl transition-all duration-300 ease-in-out
                    w-72 md:translate-x-0
                    lg:static lg:inset-0"
                :class="{
                    '-translate-x-full': !mobileSidebarOpen,
                    'translate-x-0': mobileSidebarOpen,
                    'w-20': sidebarCollapsed,
                    'w-72': !sidebarCollapsed
                }"
            >
                <!-- Close Button for Mobile -->
                <button
                    type="button"
                    class="absolute right-4 top-4 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 transition hover:bg-slate-300 lg:hidden z-50"
                    @click="mobileSidebarOpen = false"
                >
                    <span class="sr-only">Tutup menu</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>



                <div class="flex flex-col h-full">
                    @include('layouts.navigation')
                </div>
            </aside>

            <!-- Main Content -->
            <main class="min-w-0 flex h-full min-h-0 flex-col md:flex-1">
                <!-- Mobile Menu Button -->
                <div class="sticky top-0 z-30 md:hidden bg-white border-b border-blue-100 px-4 py-3">
                    <button
                        type="button"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-blue-200 text-slate-700 transition hover:bg-blue-50"
                        @click="mobileSidebarOpen = true"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <div id="main-scroll-area" class="min-h-0 flex-1 overflow-y-auto" data-scroll-restore="main">
                    <!-- Desktop Sidebar Toggle -->
                    <div class="hidden md:flex items-center justify-end px-6 pt-6">
                        <button
                            type="button"
                            class="inline-flex h-10 items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-blue-50"
                            @click="sidebarCollapsed = !sidebarCollapsed"
                        >
                            <svg x-show="!sidebarCollapsed" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <svg x-show="sidebarCollapsed" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <span x-cloak x-show="!sidebarCollapsed">Sembunyikan sidebar</span>
                            <span x-cloak x-show="sidebarCollapsed">Tampilkan sidebar</span>
                        </button>
                    </div>

                    <div class="w-full px-4 py-6 sm:px-6 lg:px-8">
                        <div class="mx-auto max-w-6xl">
                            <!-- Header Section -->
                            @isset($header)
                                <div class="mb-8">
                                    <div class="rounded-xl border border-blue-200/60 bg-white p-6 shadow-sm">
                                        {{ $header }}
                                    </div>
                                </div>
                            @endisset

                            <!-- Page Content -->
                            <div>
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <script>
        (function () {
            const mainScrollArea = document.getElementById('main-scroll-area');
            const sidebarScrollArea = document.getElementById('sidebar-scroll-area');

            function storageKey(name) {
                return `scroll-position:${name}`;
            }

            function restoreScroll(element, keyName) {
                if (!element) return;

                const saved = sessionStorage.getItem(storageKey(keyName));
                if (saved !== null) {
                    element.scrollTop = parseInt(saved, 10) || 0;
                }

                element.addEventListener('scroll', function () {
                    sessionStorage.setItem(storageKey(keyName), String(element.scrollTop));
                }, { passive: true });
            }

            window.addEventListener('beforeunload', function () {
                if (mainScrollArea) {
                    sessionStorage.setItem(storageKey('main'), String(mainScrollArea.scrollTop));
                }

                if (sidebarScrollArea) {
                    sessionStorage.setItem(storageKey('sidebar'), String(sidebarScrollArea.scrollTop));
                }
            });

            restoreScroll(mainScrollArea, 'main');
            restoreScroll(sidebarScrollArea, 'sidebar');
        })();
        </script>
    </body>
</html>
