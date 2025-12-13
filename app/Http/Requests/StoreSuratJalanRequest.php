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
            'invoice_id' => ['required', 'exists:invoices,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'tanggal' => ['required', 'date'],
            'ongkos_kirim' => ['required', 'numeric', 'min:0'],
            'grand_total' => ['nullable', 'numeric', 'min:0'],
            'status_pembayaran' => ['nullable', 'in:pending,lunas,cancel'],
            'alasan_cancel' => ['nullable', 'string'],
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
