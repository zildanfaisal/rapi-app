<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceRecord extends Model
{
    protected $fillable = [
        'tanggal',
        'tipe',
        'kategori',
        'jumlah',
        'deskripsi',
        'created_by'
    ];

   public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
