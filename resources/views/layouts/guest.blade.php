<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PT. Metal Amanah Baru') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 font-sans text-ink-900 antialiased">
        <div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-6 sm:py-8">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(28,166,248,0.16),transparent_45%)]"></div>

            <div class="relative w-full max-w-sm">
                <div class="mb-4 flex justify-center px-2">
                    <a href="/" class="inline-flex max-w-full items-center gap-2.5 rounded-2xl border border-ink-200 bg-white px-3 py-2.5 shadow-panel">
                        <div class="h-12 w-12 shrink-0">
                            <x-application-logo class="h-full w-full object-contain" />
                        </div>
                        <span class="max-w-[12rem] break-words text-xs font-bold uppercase leading-tight text-ink-900 sm:text-sm">{{ config('app.name', 'PT. Metal Amanah Baru') }}</span>
                    </a>
                </div>

                <div class="ui-card">
                    <div class="ui-card-body p-4 sm:p-5">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
