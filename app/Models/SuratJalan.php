<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $table = 'surat_jalans';
    
    protected $fillable = [
        'nomor_surat_jalan',
        'invoice_id',
        'customer_id',
        'tanggal',
        'ongkos_kirim',
        'grand_total',
        'status_pembayaran',
        'alasan_cancel',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
