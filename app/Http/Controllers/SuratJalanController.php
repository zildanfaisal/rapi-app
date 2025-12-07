<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSuratJalanRequest;
use App\Models\Invoice;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\DB;

class SuratJalanController extends Controller
{
    public function store(StoreSuratJalanRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $invoice = Invoice::findOrFail($data['invoice_id']);

            $grandTotal = $data['grand_total'] ?? ($invoice->grand_total + ($data['ongkos_kirim'] ?? 0));

            $sj = SuratJalan::create([
                'nomor_surat_jalan' => $data['nomor_surat_jalan'] ?? null,
                'customer_id' => $data['customer_id'],
                'invoice_id' => $invoice->id,
                'tanggal' => $data['tanggal'],
                'ongkos_kirim' => $data['ongkos_kirim'],
                'grand_total' => $grandTotal,
                'status_pembayaran' => $data['status_pembayaran'] ?? $invoice->status_pembayaran,
                'alasan_cancel' => $data['alasan_cancel'] ?? null,
            ]);

            return redirect()->route('surat-jalan.show', $sj)->with('success', 'Surat Jalan created successfully');
        });
    }

    public function show(SuratJalan $suratJalan)
    {
        $suratJalan->load(['invoice', 'customer', 'transactions']);
        return view('surat-jalan.show', compact('suratJalan'));
    }
}
