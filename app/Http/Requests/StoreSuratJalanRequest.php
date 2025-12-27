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
            'nomor_surat_jalan'  => 'required|string|unique:surat_jalans,nomor_surat_jalan',
            'customer_id'        => 'required|exists:customers,id',
            'invoice_id'         => 'required|exists:invoices,id',
            'tanggal'            => 'required|date',
            'status'             => 'required|in:belum dikirim,sudah dikirim,cancel',

            // ✅ Validasi conditional: wajib jika status "sudah dikirim"
            'bukti_pengiriman' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('status') === 'sudah dikirim' && !$this->hasFile('bukti_pengiriman')) {
                        $fail('Bukti pengiriman wajib diisi jika status "Sudah Dikirim".');
                    }
                },
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048'
            ],

            'alasan_cancel' => 'nullable|required_if:status,cancel|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor_surat_jalan.required' => 'Nomor surat jalan wajib diisi.',
            'nomor_surat_jalan.unique' => 'Nomor surat jalan sudah digunakan.',
            'status.required' => 'Status pengiriman wajib dipilih.',
            'bukti_pengiriman.image' => 'Bukti pengiriman harus berupa gambar.',
            'bukti_pengiriman.max' => 'Ukuran bukti pengiriman maksimal 2MB.',
            'alasan_cancel.required_if' => 'Alasan pembatalan wajib diisi jika status dibatalkan.',
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
