<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that was acted upon
     */
    public function subject()
    {
        return $this->morphTo('model');
    }

    /**
     * Scope untuk filter berdasarkan tipe
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted type name
     */
    public function getTypeNameAttribute()
    {
        $types = [
            'login' => 'Login',
            'logout' => 'Logout',
            'create' => 'Tambah',
            'update' => 'Ubah',
            'delete' => 'Hapus',
            'export' => 'Export',
            'status_change' => 'Ubah Status',
            'cancel' => 'Batalkan',
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get badge color for type
     */
    public function getTypeBadgeColorAttribute()
    {
        $colors = [
            'login' => 'bg-green-100 text-green-700',
            'logout' => 'bg-gray-100 text-gray-700',
            'create' => 'bg-blue-100 text-blue-700',
            'update' => 'bg-yellow-100 text-yellow-700',
            'delete' => 'bg-red-100 text-red-700',
            'export' => 'bg-purple-100 text-purple-700',
            'status_change' => 'bg-indigo-100 text-indigo-700',
            'cancel' => 'bg-orange-100 text-orange-700',
        ];

        return $colors[$this->type] ?? 'bg-gray-100 text-gray-700';
    }
}
