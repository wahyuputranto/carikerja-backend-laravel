<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    public function logActivity($action)
    {
        $original = $action === 'updated' ? $this->getOriginal() : null;
        $current = $this->getAttributes();
        
        // Remove hidden attributes or sensitive data if needed
        // For now we log everything except hidden ones defined in model
        
        // Calculate diff for updates if desired, or just store all.
        // For strict audit, storing diff is better.
        
        $oldValues = null;
        $newValues = null;

        if ($action === 'created') {
            $newValues = $current;
        } elseif ($action === 'deleted') {
            $oldValues = $current;
        } elseif ($action === 'updated') {
            // Only log changed attributes
            $changes = $this->getChanges();
            $newValues = $changes;
            $oldValues = array_intersect_key($original, $changes);
        }

        AuditTrail::create([
            'user_id' => Auth::id(), // Might be null if run by system/job
            'action' => $action,
            'module' => class_basename($this),
            'reference_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
