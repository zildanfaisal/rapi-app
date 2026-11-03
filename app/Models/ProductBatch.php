<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;

class ProductBatch extends Model
{
    protected $fillable = [
        'product_id',
        'batch_number',
        'quantity_masuk',
        'quantity_sekarang',
        'tanggal_masuk',
        'tanggal_expired',
        'status',
    ];

    public static function createForPurchase(Product $product, array $attributes): self
    {
        $now = now();
        $prefix = 'SN-' . $now->format('dmy') . '-'
            . strtoupper(mb_substr($product->nama_produk ?? 'X', 0, 1))
            . $now->format('Hi') . '-';

        $sequence = 1;
        foreach (static::query()->where('batch_number', 'like', $prefix . '%')->pluck('batch_number') as $number) {
            $suffix = substr($number, strlen($prefix));
            if (ctype_digit($suffix)) {
                $sequence = max($sequence, (int) $suffix + 1);
            }
        }

        $connection = (new static)->getConnection();
        for ($attempt = 0; $attempt < 100; $attempt++, $sequence++) {
            $number = $prefix . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
            try {
                // A nested transaction provides a savepoint if the purchase is already in a transaction.
                return $connection->transaction(fn () => static::create(array_merge($attributes, [
                    'batch_number' => $number,
                ])));
            } catch (UniqueConstraintViolationException $exception) {
                // Use a current read: another purchase may have committed after our initial read.
                if (! static::query()->where('batch_number', $number)->lockForUpdate()->exists()) {
                    throw $exception;
                }
            }
        }

        throw $exception;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function refreshStatus(): void
    {
        $status = 'active';
        $today = Carbon::today();
        $expiredAt = $this->tanggal_expired ? Carbon::parse($this->tanggal_expired) : null;

        if ($expiredAt && $expiredAt->lt($today)) {
            $status = 'expired';
        } elseif ((int) $this->quantity_sekarang <= 0) {
            $status = 'sold_out';
        }

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }
    }
}
