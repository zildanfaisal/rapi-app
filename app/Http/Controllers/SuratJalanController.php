<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuratJalanRequest;
use App\Models\Invoice;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\Storage;

class SuratJalanController extends Controller
{
    use ActivityLogger;

    private function syncSuratJalanTotals(array $ids = null): void
    {
        try {
            $query = SuratJalan::with(['invoice']);
            if ($ids && count($ids) > 0) {
                $query->whereIn('id', $ids);
            }
            $query->chunkById(100, function ($chunk) {
                foreach ($chunk as $sj) {
                    $invoiceTotal = (float) ($sj->invoice->grand_total ?? 0);
                    $shipping = (float) ($sj->ongkos_kirim ?? 0);
                    $computed = $invoiceTotal + $shipping;
                    if ((float) ($sj->getRawOriginal('grand_total') ?? 0) !== $computed) {
                        $sj->update(['grand_total' => $computed, 'status_pembayaran' => $sj->invoice->status_pembayaran ?? $sj->status_pembayaran]);
                    }
                }
            });
        } catch (\Throwable $e) {
        }
    }

    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $this->syncSuratJalanTotals();

        $base = SuratJalan::with(['customer', 'invoice'])
            ->when($dateFrom, fn($q) => $q->whereDate('tanggal', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('tanggal', '<=', $dateTo));

        $suratJalans = (clone $base)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->only('date_from', 'date_to'));

        $totalCount = (clone $base)->count();
        $paidCount = (clone $base)->where('status', 'sudah dikirim')->count();

        return view('penjualan.surat_jalan.index', compact('suratJalans', 'totalCount', 'paidCount', 'dateFrom', 'dateTo'));
    }

    public function create()
    {
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

        return DB::transaction(function () use ($request, $data) {
            $invoice = Invoice::findOrFail($data['invoice_id']);

            $buktiPath = null;

            // 🔥 HANDLE UPLOAD
            if ($request->hasFile('bukti_pengiriman')) {
                $buktiPath = $request->file('bukti_pengiriman')
                    ->store('bukti-pengiriman', 'public');
            }

            $sj = SuratJalan::create([
                'nomor_surat_jalan' => $data['nomor_surat_jalan']
                    ?? 'SJ-' . now()->format('ymd') . '-' . Str::upper(Str::random(5)),

                'customer_id'       => $invoice->customer_id,
                'invoice_id'        => $invoice->id,
                'tanggal'           => $data['tanggal'],
                'status'            => $data['status'],
                'alasan_cancel'     => $data['alasan_cancel'] ?? null,

                // 🔥 SIMPAN PATH FILE
                'bukti_pengiriman'  => $buktiPath,
            ]);

            self::logCreate($sj, 'Surat Jalan');

            return redirect()
                ->route('surat-jalan.index')
                ->with('success', 'Surat Jalan berhasil dibuat');
        });
    }


    public function show(SuratJalan $suratJalan)
    {
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
        $this->syncSuratJalanTotals([$suratJalan->id]);
        $suratJalan->load(['invoice', 'customer']);
        $invoices = Invoice::with(['customer'])->orderByDesc('created_at')->get();
        return view('penjualan.surat_jalan.edit', compact('suratJalan', 'invoices'));
    }

    public function update(StoreSuratJalanRequest $request, SuratJalan $suratJalan)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data, $suratJalan) {
            $oldValues = $suratJalan->only([
                'customer_id',
                'invoice_id',
                'tanggal',
                'bukti_pengiriman',
                'status',
                'alasan_cancel'
            ]);

            if ($request->hasFile('bukti_pengiriman')) {

                if (
                    $suratJalan->bukti_pengiriman &&
                    Storage::disk('public')->exists($suratJalan->bukti_pengiriman)
                ) {
                    Storage::disk('public')->delete($suratJalan->bukti_pengiriman);
                }


                $buktiPath = $request->file('bukti_pengiriman')
                    ->store('bukti-pengiriman', 'public');

                $suratJalan->bukti_pengiriman = $buktiPath;
            }

            $suratJalan->update([
                'nomor_surat_jalan' => $data['nomor_surat_jalan'],
                'customer_id'       => $data['customer_id'],
                'invoice_id'        => $data['invoice_id'],
                'tanggal'           => $data['tanggal'],
                'status'            => $data['status'],
                'alasan_cancel'     => $data['status'] === 'cancel'
                    ? ($data['alasan_cancel'] ?? null)
                    : null,
            ]);

            $newValues = $suratJalan->only([
                'customer_id',
                'invoice_id',
                'tanggal',
                'bukti_pengiriman',
                'status',
                'alasan_cancel'
            ]);

            self::logUpdate($suratJalan, 'Surat Jalan', $oldValues, $newValues);

            return redirect()
                ->route('surat-jalan.index')
                ->with('success', 'Surat Jalan berhasil diperbarui');
        });
    }

    public function destroy(SuratJalan $suratJalan)
    {
        self::logDelete($suratJalan, 'Surat Jalan');
        if ($product->foto_produk && Storage::disk('public')->exists($product->foto_produk)) {
            Storage::disk('public')->delete($product->foto_produk);
        }
        $suratJalan->delete();
        return redirect()->route('surat-jalan.index')->with('success', 'Surat Jalan deleted successfully');
    }
}
