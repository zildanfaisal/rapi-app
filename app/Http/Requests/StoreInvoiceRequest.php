<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ProductBatch;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_number' => ['nullable', 'string', 'size:8', 'regex:/^[A-Z0-9]+$/', 'unique:invoices,invoice_number'],
            'customer_id' => ['required', 'exists:customers,id'],
            'user_id' => ['required', 'exists:users,id'],
            'tanggal_invoice' => ['required', 'date'],
            'tanggal_jatuh_tempo' => ['required', 'date', 'after_or_equal:tanggal_invoice'],
            'status_pembayaran' => ['nullable', 'in:unpaid,paid,overdue,cancelled,partial'],
            'status_setor' => ['nullable', 'in:belum,sudah'],
            'bukti_setor' => ['nullable', 'string'],
            'alasan_cancel' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.batch_id' => ['required', 'exists:product_batches,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.harga' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $items = (array) $this->input('items', []);
            foreach ($items as $idx => $item) {
                $batchId = $item['batch_id'] ?? null;
                $qty = (int) ($item['quantity'] ?? 0);
                if (!$batchId || $qty <= 0) continue;
                $batch = ProductBatch::find($batchId);
                if (!$batch) continue;
                $stok = (int) ($batch->quantity_sekarang ?? 0);
                if ($qty > $stok) {
                    $v->errors()->add("items.$idx.quantity", "Qty melebihi stok tersedia (maks ${stok}).");
                }
            }
        });
    }
}
