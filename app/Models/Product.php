<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'barcode',
        'kategori',
        'harga',
        'satuan',
        'foto_produk',
        'min_stok_alert',
        'status',
    ];

    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }
    public function latestBatch()
    {
        return $this->hasOne(ProductBatch::class)
            ->latestOfMany('created_at'); 
    }

    
}
