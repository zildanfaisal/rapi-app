<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
