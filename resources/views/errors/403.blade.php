<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <main class="mx-auto flex min-h-screen max-w-2xl items-center justify-center px-6 py-12">
        <div class="w-full rounded-2xl border border-red-100 bg-white p-8 text-center shadow-sm">
            <p class="text-sm font-semibold uppercase tracking-wide text-red-600">403 Forbidden</p>
            <h1 class="mt-2 text-2xl font-bold">Akses Ditolak</h1>
            <p class="mt-3 text-sm text-slate-600">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
            </p>
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </main>
</body>
</html>
