<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait ActivityLogger
{
    /**
     * Log aktivitas user
     *
     * @param string $type (login, logout, create, update, delete, export, dll)
     * @param string $description
     * @param mixed $model (optional)
     * @param array $properties (optional - untuk menyimpan detail perubahan)
     */
    public static function logActivity($type, $description, $model = null, $properties = [])
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => $type,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'properties' => !empty($properties) ? $properties : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log login activity
     */
    public static function logLogin($user)
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'type' => 'login',
            'description' => "User '{$user->name}' melakukan login",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log logout activity
     */
    public static function logLogout()
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'type' => 'logout',
                'description' => "User '{$user->name}' melakukan logout",
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }

    /**
     * Log create activity
     */
    public static function logCreate($model, $modelName)
    {
        $user = Auth::user();
        $identifier = self::getModelIdentifier($model);

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'create',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "User '{$user->name}' membuat {$modelName} baru: {$identifier}",
            'properties' => ['attributes' => $model->getAttributes()],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log update activity
     */
    public static function logUpdate($model, $modelName, $oldValues = [], $newValues = [])
    {
        $user = Auth::user();
        $identifier = self::getModelIdentifier($model);

        $changes = [];
        foreach ($newValues as $key => $newValue) {
            if (isset($oldValues[$key]) && $oldValues[$key] != $newValue) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $newValue,
                ];
            }
        }

        $changesText = self::formatChanges($changes);

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'update',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "User '{$user->name}' mengubah {$modelName}: {$identifier}. {$changesText}",
            'properties' => ['old' => $oldValues, 'new' => $newValues, 'changes' => $changes],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log delete activity
     */
    public static function logDelete($model, $modelName)
    {
        $user = Auth::user();
        $identifier = self::getModelIdentifier($model);

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'delete',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "User '{$user->name}' menghapus {$modelName}: {$identifier}",
            'properties' => ['deleted_attributes' => $model->getAttributes()],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log export activity
     */
    public static function logExport($exportType, $filters = [])
    {
        $user = Auth::user();

        $filterText = !empty($filters) ? ' dengan filter: ' . json_encode($filters) : '';

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'export',
            'description' => "User '{$user->name}' melakukan export data {$exportType}{$filterText}",
            'properties' => ['export_type' => $exportType, 'filters' => $filters],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log status change
     */
    public static function logStatusChange($model, $modelName, $oldStatus, $newStatus)
    {
        $user = Auth::user();
        $identifier = self::getModelIdentifier($model);

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'status_change',
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "User '{$user->name}' mengubah status {$modelName}: {$identifier} dari '{$oldStatus}' menjadi '{$newStatus}'",
            'properties' => ['old_status' => $oldStatus, 'new_status' => $newStatus],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get model identifier (nama, nomor, dll)
     */
    private static function getModelIdentifier($model)
    {
        // Coba ambil identifier yang paling umum
        if (isset($model->name)) {
            return $model->name;
        } elseif (isset($model->nama)) {
            return $model->nama;
        } elseif (isset($model->nama_customer)) {
            return $model->nama_customer;
        } elseif (isset($model->nama_product)) {
            return $model->nama_product;
        } elseif (isset($model->invoice_number)) {
            return $model->invoice_number;
        } elseif (isset($model->email)) {
            return $model->email;
        }

        return "ID: {$model->id}";
    }

    /**
     * Format changes untuk deskripsi yang mudah dibaca
     */
    private static function formatChanges($changes)
    {
        if (empty($changes)) {
            return '';
        }

        $changeTexts = [];
        foreach ($changes as $field => $values) {
            $changeTexts[] = "{$field}: '{$values['old']}' → '{$values['new']}'";
        }

        return 'Perubahan: ' . implode(', ', $changeTexts);
    }
}
