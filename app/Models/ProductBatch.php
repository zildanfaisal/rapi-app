<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'batch_number',
        'quantity_masuk',
        'quantity_sekarang',
        'tanggal_masuk',
        'tanggal_expired',
        'supplier',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
