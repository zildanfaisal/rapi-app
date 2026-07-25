<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'user_id',
        'tanggal_pembelian',
        'grand_total',
        'metode_pembayaran',
        'status_pembayaran',
        'status_setor',
        'bukti_setor',
        'alasan_cancel',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(PembelianItem::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(PembayaranPembelian::class);
    }

    public function getSisaTagihanAttribute()
    {
        $totalBayar = $this->pembayarans()->sum('jumlah_bayar');
        return max(0, $this->grand_total - $totalBayar);
    }
}
