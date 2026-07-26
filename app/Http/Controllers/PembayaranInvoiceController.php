<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranInvoiceController extends Controller
{
    use ActivityLogger;

    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'tanggal_bayar' => ['required', 'date'],
            'metode_pembayaran' => ['required', 'in:tunai,transfer,qris'],
            'bukti_pembayaran' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'catatan' => ['nullable', 'string'],
        ]);

        if ($invoice->status_pembayaran === 'cancelled') {
            return back()->withErrors(['jumlah_bayar' => 'Invoice yang dibatalkan tidak dapat menerima pembayaran.']);
        }

        return DB::transaction(function () use ($request, $invoice) {
            $invoice->refresh();
            $sisaTagihan = $invoice->sisa_tagihan;

            if ((float) $request->jumlah_bayar > $sisaTagihan) {
                return back()->withErrors([
                    'jumlah_bayar' => 'Jumlah pelunasan tidak boleh melebihi sisa tagihan (Rp ' . number_format($sisaTagihan, 0, ',', '.') . ').',
                ]);
            }

            $buktiPath = $request->hasFile('bukti_pembayaran')
                ? $request->file('bukti_pembayaran')->store('bukti-pembayaran-invoice', 'public')
                : null;

            $pembayaran = $invoice->pembayarans()->create([
                'user_id' => Auth::id(),
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'bukti_pembayaran' => $buktiPath,
                'catatan' => $request->catatan,
            ]);

            $this->syncStatusPembayaran($invoice);
            self::logCreate($pembayaran, 'Pembayaran Invoice', 'Penjualan');

            return redirect()->route('invoices.show', $invoice)->with('success', 'Pelunasan berhasil dicatat.');
        });
    }

    private function syncStatusPembayaran(Invoice $invoice): void
    {
        $totalBayar = (float) $invoice->pembayarans()->sum('jumlah_bayar');
        $status = $totalBayar >= (float) $invoice->grand_total
            ? 'paid'
            : ($totalBayar > 0 ? 'partial' : 'unpaid');

        // Setoran tetap merupakan proses terpisah dan hanya dapat dilakukan setelah lunas.
        $invoice->update(['status_pembayaran' => $status]);
    }
}
