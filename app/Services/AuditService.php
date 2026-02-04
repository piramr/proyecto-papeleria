<?php

namespace App\Services;

use App\Models\Audit\TipoLog;
use App\Models\Audit\UserLoginAudit;
use App\Models\Audit\UserLoginAttemptsLog;
use App\Models\Audit\UserRecursosLog;
use App\Models\Audit\DdlAuditoria;
use App\Models\Audit\DmlAuditoria;
use Illuminate\Support\Facades\Request;

class AuditService
{
    protected $types;

    public function __construct()
    {
    // Cache types to avoid repeated queries, using firstOrCreate if needed in a real app, 
    // but here assuming seeded values.
    }

    private function getTypeId($code)
    {
        // Simple caching or direct query
        return TipoLog::where('codigo', $code)->value('id');
    }

    public function logLogin($user, $sessionId, $host)
    {
        return UserLoginAudit::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'host' => $host,
            'login_fecha' => now(),
            'tipo_log_id' => $this->getTypeId('SUCCESS'),
        ]);
    }

    public function logLogout($user, $sessionId)
    {
        // Find latest active session for user or specific session
        $log = UserLoginAudit::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->whereNull('logout_fecha')
            ->latest('login_fecha')
            ->first();

        if ($log) {
            $now = now();
            $log->update([
                'logout_fecha' => $now,
                'duration_seconds' => $log->login_fecha->diffInSeconds($now),
            ]);
        }
    }

    public function logFailedLogin($username, $host, $reason)
    {
        // Try to find user if exists
        $user = \App\Models\User::where('email', $username)->first();

        UserLoginAttemptsLog::create([
            'user_id' => $user ? $user->id : null,
            'username_attempted' => $username,
            'host' => $host,
            'attempt_fecha' => now(),
            'result' => 'FAILED', // or LOCKED
            'failure_reason' => $reason,
            'tipo_log_id' => $this->getTypeId('WARNING'),
        ]);
    }

    public function logResource($user, $request, $response)
    {
        // Ignore own audit routes to prevent spam if desired, or log everything
        // if (str_contains($request->path(), 'audit/console')) return;

        UserRecursosLog::create([
            'user_id' => $user->id,
            'endpoint' => $request->path(),
            'http_method' => $request->method(),
            'request_body' => json_encode($request->all()), // Be careful with sensitive data
            'response_code' => $response->getStatusCode(),
            'response_time_ms' => defined('LARAVEL_START') ? (microtime(true) - LARAVEL_START) * 1000 : null,
            'timestamp' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'tipo_log_id' => $this->getTypeId($response->isSuccessful() ? 'INFO' : 'ERROR'),
        ]);
    }

    public function logDdl($user, $event, $type, $name, $schema, $sql)
    {
        DdlAuditoria::create([
            'user_id' => $user->id,
            'ddl_fecha' => now(),
            'evento' => $event,
            'objeto_tipo' => $type,
            'objeto_nombre' => $name,
            'esquema' => $schema,
            'sql_command' => $sql,
            'tipo_log_id' => $this->getTypeId('WARNING'), // DDL is significant
        ]);
    }

    // Helper to be called from Model Events or Observers
    public function logDml($user, $action, $table, $id, $old = null, $new = null)
    {
        DmlAuditoria::create([
            'user_id' => $user->id,
            'accion' => $action,
            'timestamp' => now(),
            'esquema' => config('database.connections.mysql.database'), // or env
            'tabla' => $table,
            'fila_id' => $id,
            'valor_anterior' => $old ? json_encode($old) : null,
            'valor_nuevo' => $new ? json_encode($new) : null,
            'tipo_log_id' => $this->getTypeId('INFO'),
        ]);
    }
}
