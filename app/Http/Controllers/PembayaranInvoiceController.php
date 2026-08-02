<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PembayaranInvoice;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    public function update(Request $request, PembayaranInvoice $pembayaran)
    {
        $request->validate([
            'jumlah_bayar' => ['required', 'numeric', 'min:1'],
            'tanggal_bayar' => ['required', 'date'],
            'metode_pembayaran' => ['required', 'in:tunai,transfer,qris'],
            'bukti_pembayaran' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'catatan' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($request, $pembayaran) {
            $invoice = $pembayaran->invoice;
            $totalPembayaranLain = (float) $invoice->pembayarans()
                ->where('id', '!=', $pembayaran->id)
                ->sum('jumlah_bayar');
            $maksimalBayar = max(0, (float) $invoice->grand_total - $totalPembayaranLain);

            if ((float) $request->jumlah_bayar > $maksimalBayar) {
                return back()->withErrors([
                    'jumlah_bayar' => 'Jumlah pembayaran tidak boleh melebihi sisa tagihan (Rp ' . number_format($maksimalBayar, 0, ',', '.') . ').',
                ]);
            }

            $data = $request->only(['jumlah_bayar', 'tanggal_bayar', 'metode_pembayaran', 'catatan']);
            if ($request->metode_pembayaran === 'tunai') {
                if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
                    Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
                }
                $data['bukti_pembayaran'] = null;
            } elseif ($request->hasFile('bukti_pembayaran')) {
                if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
                    Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
                }
                $data['bukti_pembayaran'] = $request->file('bukti_pembayaran')->store('bukti-pembayaran-invoice', 'public');
            }

            $pembayaran->update($data);
            $this->syncStatusPembayaran($invoice);

            return redirect()->route('invoices.show', $invoice)->with('success', 'Riwayat pembayaran berhasil diperbarui.');
        });
    }

    public function destroy(PembayaranInvoice $pembayaran)
    {
        return DB::transaction(function () use ($pembayaran) {
            $invoice = $pembayaran->invoice;
            if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
                Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }

            self::logDelete($pembayaran, 'Pembayaran Invoice', 'Penjualan');
            $pembayaran->delete();
            $this->syncStatusPembayaran($invoice);

            return redirect()->route('invoices.show', $invoice)->with('success', 'Riwayat pembayaran berhasil dihapus.');
        });
    }

    private function syncStatusPembayaran(Invoice $invoice): void
    {
        $totalBayar = (float) $invoice->pembayarans()->sum('jumlah_bayar');
        $status = $totalBayar >= (float) $invoice->grand_total
            ? 'paid'
            : ($totalBayar > 0 ? 'partial' : 'unpaid');

        // Setoran tetap merupakan proses terpisah dan hanya dapat dilakukan setelah lunas.
        $data = ['status_pembayaran' => $status];
        if ($status !== 'paid') {
            $data['status_setor'] = 'belum';
            $data['tanggal_setor'] = null;
        }
        $invoice->update($data);
    }
}
