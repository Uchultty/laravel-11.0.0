<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 24px 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 11px;
            line-height: 1.45;
        }

        .header {
            border-bottom: 2px solid #111827;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }

        .title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: #111827;
        }

        .subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }

        .meta {
            margin-top: 8px;
            font-size: 10px;
            color: #4b5563;
        }

        .summary {
            margin: 10px 0 14px;
            font-size: 10px;
            color: #374151;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #9ca3af;
            padding: 6px 7px;
            vertical-align: top;
        }

        .report-table th {
            background: #e5e7eb;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .muted {
            color: #6b7280;
        }

        .no-data {
            padding: 18px 10px;
            text-align: center;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">LAPORAN PENGIRIMAN PRODUK</p>
        <div class="subtitle">MABSTOCK</div>
        <div class="meta">Tanggal export: {{ $generatedAt->format('d/m/Y H:i') }}</div>
    </div>

    <div class="summary">
        <div><strong>Filter Aktif:</strong></div>
        <div>Search: {{ $filters['search'] !== '' ? $filters['search'] : '-' }}</div>
        <div>Status: {{ $filters['status'] !== '' ? $filters['status'] : '-' }}</div>
        <div>Tanggal: {{ $filters['tanggal'] !== '' ? $filters['tanggal'] : '-' }}</div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Produk</th>
                <th style="width: 70px;">Qty</th>
                <th style="width: 155px;">Customer</th>
                <th style="width: 100px;">Tanggal Kirim</th>
                <th style="width: 95px;">Status</th>
                <th style="width: 140px;">Surat Jalan</th>
                <th style="width: 140px;">Invoice</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item['produk'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format((int) ($item['qty'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ $item['customer'] ?? '-' }}</td>
                    <td>{{ $item['tanggal_pengiriman'] ?? '-' }}</td>
                    <td>{{ $item['status_pengiriman'] ?? '-' }}</td>
                    <td>{{ $item['surat_jalan'] ?? '-' }}</td>
                    <td>{{ $item['invoice'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="no-data">Tidak ada data pengiriman yang cocok dengan filter aktif.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
