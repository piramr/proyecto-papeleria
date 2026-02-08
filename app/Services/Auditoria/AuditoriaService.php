<?php
namespace App\Services\Auditoria;

use App\Models\Auditoria\AuditoriaDatos;
use App\Models\Auditoria\LogOperacion;
use App\Models\Auditoria\LogSistema;
use App\Models\Auditoria\LogNivel;
use App\Models\Auditoria\LogLogin;
use App\Models\Auditoria\LogLoginResultado;
use Illuminate\Support\Facades\Auth;

class AuditoriaService
{
    // Registrar cambios de datos (auditoria_datos)
    public static function registrarAuditoriaDatos($params)
    {
        return AuditoriaDatos::create([
            'timestamp' => now(),
            'user_id' => $params['user_id'] ?? (Auth::id() ?? 0),
            'session_id' => $params['session_id'] ?? session()->getId(),
            'tipo_operacion' => $params['tipo_operacion'],
            'entidad' => $params['entidad'],
            'recurso_id' => $params['recurso_id'],
            'recurso_padre_id' => $params['recurso_padre_id'] ?? null,
            'campo' => $params['campo'],
            'valor_original' => $params['valor_original'] ?? null,
            'valor_nuevo' => $params['valor_nuevo'],
        ]);
    }

    // Registrar operación (log_operacion)
    public static function registrarOperacion($params)
    {
        return LogOperacion::create([
            'timestamp' => now(),
            'user_id' => $params['user_id'] ?? (Auth::id() ?? 0),
            'session_id' => $params['session_id'] ?? session()->getId(),
            'tipo_operacion' => $params['tipo_operacion'],
            'entidad' => $params['entidad'],
            'recurso_id' => $params['recurso_id'] ?? null,
            'recurso_padre_id' => $params['recurso_padre_id'] ?? null,
            'resultado' => $params['resultado'],
            'codigo_error' => $params['codigo_error'] ?? null,
            'mensaje_error' => $params['mensaje_error'] ?? null,
        ]);
    }

    // Registrar log de sistema (log_sistema)
    public static function registrarLogSistema($nivel, $mensaje)
    {
        $nivelId = LogNivel::where('nombre', $nivel)->value('id');
        return LogSistema::create([
            'timestamp' => now(),
            'nivel_log_id' => $nivelId,
            'mensaje' => $mensaje,
        ]);
    }

    // Registrar log de login (log_login)
    public static function registrarLogLogin($params)
    {
        $resultadoId = LogLoginResultado::where('nombre', $params['resultado'])->value('id');
        return LogLogin::create([
            'timestamp' => now(),
            'user_email' => $params['user_email'],
            'user_id' => $params['user_id'] ?? null,
            'host' => $params['host'] ?? request()->ip(),
            'reintento' => $params['reintento'] ?? 1,
            'dispositivo' => $params['dispositivo'] ?? request()->header('User-Agent'),
            'ubicacion' => $params['ubicacion'] ?? null,
            'resultado_log_id' => $resultadoId,
        ]);
    }
}
