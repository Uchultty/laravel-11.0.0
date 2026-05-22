<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class LaporanPengirimanProdukController extends Controller
{
    public function index(Request $request): View
    {
        $query = $this->buildQuery($request, true);
        $summaryTotalItem = (clone $query)->count();
        $pengirimanCompletedPaginated = $query->paginate(10)->withQueryString();

        return view('laporan.pengiriman-produk.index', [
            'pengirimanCompletedPaginated' => $pengirimanCompletedPaginated,
            'summaryTotalItem' => $summaryTotalItem,
            'filters' => $this->filtersFromRequest($request),
            'statusOptions' => ['Selesai', 'Sedang Dikirim', 'Siap Dikirim'],
        ]);
    }

    public function exportPdf(Request $request)
    {
        $items = $this->buildExportRows($request);
        $filters = $this->filtersFromRequest($request);
        $generatedAt = now();

        $html = view('laporan.pengiriman-pdf', [
            'items' => $items,
            'filters' => $filters,
            'generatedAt' => $generatedAt,
        ])->render();

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        $filename = 'laporan-pengiriman-produk-' . $generatedAt->format('Y-m-d-His') . '.pdf';

        return response(
            $mpdf->Output('', Destination::STRING_RETURN),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
    private function buildQuery(Request $request, bool $withProductionItem = true)
    {
        $search = trim((string) $request->query('search', ''));
        $status = trim((string) $request->query('status', 'Selesai'));
        $tanggal = trim((string) $request->query('tanggal', ''));

        $withRelations = ['barang:id_product,nama,satuan', 'customer:id_pelanggan,nama'];

        if ($withProductionItem) {
            $withRelations[] = 'productionItem.pelanggan:id_pelanggan,nama';
        }

        return Shipment::query()
            ->with($withRelations)
            ->when($status !== '', function ($query) use ($status) {
                $query->where('status_pengiriman', $status);
            })
            ->when($tanggal !== '', function ($query) use ($tanggal) {
                $query->whereDate('tanggal_pengiriman', $tanggal);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->whereHas('barang', function ($barangQuery) use ($search) {
                        $barangQuery->where('nama', 'ilike', '%' . $search . '%');
                    })->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('nama', 'ilike', '%' . $search . '%');
                    });
                });
            })
            ->latest('created_at');
    }

    private function buildExportRows(Request $request)
    {
        return $this->buildQuery($request, false)
            ->get([
                'id_pengiriman',
                'id_produk',
                'id_pelanggan',
                'qty',
                'tanggal_pengiriman',
                'status_pengiriman',
                'surat_jalan_path',
                'invoice_path',
                'created_at',
            ])
            ->map(function (Shipment $shipment): array {
                return [
                    'id_pengiriman' => $shipment->id_pengiriman,
                    'produk' => optional($shipment->barang)->nama ?? '-',
                    'qty' => (int) $shipment->qty,
                    'customer' => optional($shipment->customer)->nama ?? '-',
                    'tanggal_pengiriman' => optional($shipment->tanggal_pengiriman)->format('d/m/Y') ?? '-',
                    'status_pengiriman' => $shipment->status_pengiriman ?? '-',
                    'surat_jalan' => $shipment->surat_jalan_path ? basename($shipment->surat_jalan_path) : '-',
                    'invoice' => $shipment->invoice_path ? basename($shipment->invoice_path) : '-',
                ];
            });
    }

    private function filtersFromRequest(Request $request): array
    {
        return [
            'search' => trim((string) $request->query('search', '')),
            'status' => trim((string) $request->query('status', 'Selesai')),
            'tanggal' => trim((string) $request->query('tanggal', '')),
        ];
    }
}
