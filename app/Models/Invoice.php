<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'user_id',
        'tanggal_invoice',
        'tanggal_jatuh_tempo',
        'grand_total',
        'status_pembayaran',
        'status_setor',
        'bukti_setor',
        'alasan_cancel',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function surat_jalan()
    {
        return $this->hasOne(SuratJalan::class, 'invoice_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }


    
}
