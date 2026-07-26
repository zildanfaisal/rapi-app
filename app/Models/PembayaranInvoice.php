<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranInvoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'user_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'catatan',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
