<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $invoice = $this->route('invoice');
        $invoiceId = is_object($invoice) ? $invoice->id : $invoice;

        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'user_id' => ['required', 'exists:users,id'],
            'tanggal_invoice' => ['required', 'date'],
            'tanggal_jatuh_tempo' => ['required', 'date'],
            'invoice_number' => [
                'nullable', 'string', 'size:8', 'regex:/^[A-Z0-9]+$/',
                'unique:invoices,invoice_number,' . $invoiceId
            ],
            'status_pembayaran' => ['required', 'in:unpaid,paid,overdue,cancelled'],
            'metode_pembayaran' => ['nullable', 'in:tunai,transfer,qris'],
            'ongkos_kirim' => ['nullable', 'string'],
            'diskon' => ['nullable', 'string'],
            'bukti_setor' => ['nullable', 'image', 'max:5120'],
            'alasan_cancel' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.batch_id' => ['required', 'exists:product_batches,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.harga' => ['required', 'numeric', 'min:0'],
        ];
    }
}
