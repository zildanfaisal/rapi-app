<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuratJalanRequest;
use App\Models\Invoice;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class SuratJalanController extends Controller
{
    /**
     * Ensure Surat Jalan grand_total is persisted based on invoice + shipping
     */
    private function syncSuratJalanTotals(array $ids = null): void
    {
        try {
            $query = SuratJalan::with(['invoice']);
            if ($ids && count($ids) > 0) {
                $query->whereIn('id', $ids);
            }
            $query->chunkById(100, function($chunk){
                foreach ($chunk as $sj) {
                    $invoiceTotal = (float) ($sj->invoice->grand_total ?? 0);
                    $shipping = (float) ($sj->ongkos_kirim ?? 0);
                    $computed = $invoiceTotal + $shipping;
                    // Only update when different to avoid unnecessary writes
                    if ((float) ($sj->getRawOriginal('grand_total') ?? 0) !== $computed) {
                        $sj->update(['grand_total' => $computed, 'status_pembayaran' => $sj->invoice->status_pembayaran ?? $sj->status_pembayaran]);
                    }
                }
            });
        } catch (\Throwable $e) {
            // ignore to avoid breaking pages
        }
    }
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Persist totals before listing
        $this->syncSuratJalanTotals();

        $base = SuratJalan::with(['customer', 'invoice'])
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal', '<=', $dateTo));

        $suratJalans = (clone $base)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->only('date_from', 'date_to'));

        $totalCount = (clone $base)->count();
        $paidCount = (clone $base)->where('status_pembayaran', 'lunas')->count();

        return view('penjualan.surat_jalan.index', compact('suratJalans', 'totalCount', 'paidCount', 'dateFrom', 'dateTo'));
    }
    public function create()
    {
        // Only show invoices that do not yet have a Surat Jalan
        $usedInvoiceIds = SuratJalan::query()->select('invoice_id');
        $invoices = Invoice::with(['customer'])
            ->whereNotIn('id', $usedInvoiceIds)
            ->where('status_pembayaran', 'paid')
            ->orderByDesc('created_at')
            ->get();
        return view('penjualan.surat_jalan.create', compact('invoices'));
    }

    public function store(StoreSuratJalanRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoice = Invoice::findOrFail($data['invoice_id']);

            $grandTotal = $data['grand_total'] ?? ($invoice->grand_total + ($data['ongkos_kirim'] ?? 0));

            $sj = SuratJalan::create([
                'nomor_surat_jalan' => $data['nomor_surat_jalan'] ?? strtoupper(Str::random(8)),
                'customer_id' => $data['customer_id'],
                'invoice_id' => $invoice->id,
                'tanggal' => $data['tanggal'],
                'ongkos_kirim' => $data['ongkos_kirim'],
                'grand_total' => $grandTotal,
                'status_pembayaran' => $data['status_pembayaran'] ?? $invoice->status_pembayaran,
                'alasan_cancel' => $data['alasan_cancel'] ?? null,
            ]);

            return redirect()->route('surat-jalan.index', $sj)->with('success', 'Surat Jalan created successfully');
        });
    }

    public function show(SuratJalan $suratJalan)
    {
        // Persist this record's total before rendering
        $this->syncSuratJalanTotals([$suratJalan->id]);
        $suratJalan->load(['invoice', 'customer', 'transactions']);
        return view('penjualan.surat_jalan.show', compact('suratJalan'));
    }

    public function pdf(SuratJalan $suratJalan)
    {
        $suratJalan->load(['invoice', 'customer']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('penjualan.surat_jalan.pdf', compact('suratJalan'))
            ->setPaper('a4');
        $filename = 'Surat-Jalan-' . ($suratJalan->nomor_surat_jalan ?? $suratJalan->id) . '.pdf';
        return $pdf->stream($filename);
    }

    public function edit(SuratJalan $suratJalan)
    {
        // Persist this record's total before edit view
        $this->syncSuratJalanTotals([$suratJalan->id]);
        $suratJalan->load(['invoice', 'customer']);
        $invoices = Invoice::with(['customer'])->orderByDesc('created_at')->get();
        return view('penjualan.surat_jalan.edit', compact('suratJalan', 'invoices'));
    }

    public function update(StoreSuratJalanRequest $request, SuratJalan $suratJalan)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $suratJalan) {
            $invoice = Invoice::findOrFail($data['invoice_id']);
            $grandTotal = $data['grand_total'] ?? ($invoice->grand_total + ($data['ongkos_kirim'] ?? 0));

            $suratJalan->update([
                'nomor_surat_jalan' => $data['nomor_surat_jalan'] ?? $suratJalan->nomor_surat_jalan,
                'customer_id' => $data['customer_id'],
                'invoice_id' => $invoice->id,
                'tanggal' => $data['tanggal'],
                'ongkos_kirim' => $data['ongkos_kirim'],
                'grand_total' => $grandTotal,
                'status_pembayaran' => $data['status_pembayaran'] ?? $invoice->status_pembayaran,
                'alasan_cancel' => $data['alasan_cancel'] ?? null,
            ]);

            return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan updated successfully');
        });
    }

    public function destroy(SuratJalan $suratJalan)
    {
        $suratJalan->delete();
        return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan deleted successfully');
    }
}
