<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Http\Request;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::logAudit($model, 'created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            $original = $model->getOriginal($dirty);

            if (!empty($dirty)) {
                static::logAudit($model, 'updated', $original, $dirty);
            }
        });

        static::deleted(function ($model) {
            static::logAudit($model, 'deleted', $model->getAttributes(), null);
        });
    }

    protected static function logAudit($model, string $action, $oldValues, $newValues): void
    {
        $request = request();

        AuditLog::create([
            'actor_id' => auth()->id(),
            'action' => $action,
            'entity_type' => get_class($model),
            'entity_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
