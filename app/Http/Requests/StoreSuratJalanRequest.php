<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Invoice;

class StoreSuratJalanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_surat_jalan'  => 'required|string',
            'customer_id'        => 'required|exists:customers,id',
            'invoice_id'         => 'required|exists:invoices,id',
            'tanggal'            => 'required|date',
            'status'             => 'required|in:belum dikirim,sudah dikirim,cancel',
            'bukti_pengiriman'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'alasan_cancel'      => 'nullable|string',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $invoiceId = $this->input('invoice_id');
            $tanggalSj = $this->input('tanggal');
            if (!$invoiceId || !$tanggalSj) return;

            $invoice = Invoice::find($invoiceId);
            if (!$invoice) return;

            $invoiceDate = $invoice->tanggal_invoice;
            if ($invoiceDate && strtotime($tanggalSj) < strtotime($invoiceDate)) {
                $v->errors()->add('tanggal', 'Tanggal surat jalan tidak boleh kurang dari tanggal invoice (' . $invoiceDate . ').');
            }
        });
    }
}
