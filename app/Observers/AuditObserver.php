<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditService;

class AuditObserver
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    private function getUser()
    {
        return Auth::user();
    }

    public function created(Model $model)
    {
        if ($user = $this->getUser()) {
            $this->log($user, 'INSERT', $model);
        }
    }

    public function updated(Model $model)
    {
        if ($user = $this->getUser()) {
            // Check for specific fields like password
            if ($model->wasChanged('password')) {
            // You might want a specific log for password change
            // forcing "Password Changed" into colum or logic, but standard DML works too.
            // We will rely on standard DML with hidden password values.
            }

            $this->log($user, 'UPDATE', $model);
        }
    }

    public function deleted(Model $model)
    {
        if ($user = $this->getUser()) {
            $this->log($user, 'DELETE', $model);
        }
    }

    public function restored(Model $model)
    {
        if ($user = $this->getUser()) {
            $this->log($user, 'RESTORE', $model);
        }
    }

    protected function log($user, $action, $model)
    {
        // Get changed attributes
        $new = $action === 'INSERT' ? $model->getAttributes() : $model->getChanges();
        $old = $action === 'UPDATE' ? $model->getOriginal() : null; // simplified, getOriginal might contain everything

        // Filter out sensitive data
        $hidden = $model->getHidden();
        $hidden[] = 'password';
        $hidden[] = 'remember_token';

        if ($action === 'UPDATE') {
            // For updates, we only want the fields that changed
            $changes = $model->getChanges();
            $original = $model->getOriginal();

            $oldValues = [];
            $newValues = [];

            foreach ($changes as $key => $value) {
                if (in_array($key, $hidden))
                    continue;
                $oldValues[$key] = $original[$key] ?? null;
                $newValues[$key] = $value;
            }
            $old = $oldValues;
            $new = $newValues;
        }
        else {
            // For insert/delete, filter hidden
            foreach ($hidden as $hide) {
                unset($new[$hide]);
                if ($old)
                    unset($old[$hide]);
            }
        }

        // If empty changes (e.g. only Remember Token changed or updated_at), skip?
        // But updating 'updated_at' is still an update.
        // Let's minimally clear empty arrays if desired.

        $this->auditService->logDml(
            $user,
            $action,
            $model->getTable(),
            $model->getKey(),
            $old,
            $new
        );
    }
}
