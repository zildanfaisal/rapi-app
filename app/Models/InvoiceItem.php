<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'harga',
        'sub_total',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}