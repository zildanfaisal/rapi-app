<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranPembelian extends Model
{
    protected $fillable = [
        'pembelian_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'metode_pembayaran',
        'bukti_setor',
        'catatan',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }
}