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
        'harga_beli',
        'supplier',
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

    public function refreshAvailability(): void
    {
        $totalStok = (int) $this->batches()->sum('quantity_sekarang');
        $newStatus = $totalStok > 0 ? 'available' : 'unavailable';
        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }
    }

    /**
     * Stock balance based on recorded movements, so overselling stays visible.
     * Sales are counted when recorded on an invoice, unless that invoice is cancelled.
     */
    public function getStokBersihAttribute(): int
    {
        $stokMasuk = (int) $this->batches()->sum('quantity_masuk');
        $stokTerjual = (int) $this->invoiceItems()
            ->whereHas('invoice', fn ($query) => $query->where('status_pembayaran', '!=', 'cancelled'))
            ->sum('quantity');

        return $stokMasuk - $stokTerjual;
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
