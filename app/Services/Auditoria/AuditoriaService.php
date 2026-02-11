<?php

namespace App\Services\Auditoria;

use App\Models\Auditoria\AuditoriaDatos;
use App\Models\Auditoria\LogOperacion;
use App\Models\Auditoria\LogSistema;
use App\Models\Auditoria\LogNivel;
use App\Models\Auditoria\LogLogin;
use App\Models\Auditoria\LogLoginResultado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    // Registrar operacion (log_operacion)
    public static function registrarOperacion($params)
    {
        return LogOperacion::create([
            'timestamp' => now(),
            'user_id' => $params['user_id'] ?? (Auth::id() ?? 0),
            'session_id' => $params['session_id'] ?? session()->getId(),
            'ip_address' => $params['ip_address'] ?? request()->ip(),
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

    // ============ METODOS USANDO PROCEDIMIENTOS ALMACENADOS ============

    public static function obtenerUltimaAuditoria(string $entidad, string $recursoId): int
    {
        $result = DB::select(
            'SELECT fn_ultima_auditoria(?, ?) as id',
            [$entidad, $recursoId]
        );
        return $result[0]->id ?? 0;
    }

    public static function contarCambiosPorUsuario(int $userId, string $fecha = null): int
    {
        if ($fecha === null) {
            $fecha = now()->toDateString();
        }
        $result = DB::select(
            'SELECT fn_cambios_por_usuario(?, ?) as total',
            [$userId, $fecha]
        );
        return $result[0]->total ?? 0;
    }

    public static function usuarioActivo(int $userId): bool
    {
        $result = DB::select(
            'SELECT fn_usuario_activo(?) as activo',
            [$userId]
        );
        return (bool)($result[0]->activo ?? false);
    }

    public static function cambiosCriticosCount(): int
    {
        $result = DB::select('SELECT fn_cambios_criticos_count() as total');
        return $result[0]->total ?? 0;
    }

    public static function limpiarLogsAntiguos(int $diasRetencion = 90): bool
    {
        try {
            DB::statement('CALL sp_limpiar_logs_antiguos(?)', [$diasRetencion]);
            return true;
        } catch (\Exception $e) {
            \Log::error('Error limpiando logs: ' . $e->getMessage());
            return false;
        }
    }

    public static function generarReporte(string $fechaInicio, string $fechaFin): array
    {
        try {
            return DB::select(
                'SELECT * FROM sp_reporte_auditoria(?, ?)',
                [$fechaInicio, $fechaFin]
            );
        } catch (\Exception $e) {
            \Log::error('Error generando reporte: ' . $e->getMessage());
            return [];
        }
    }

    public static function obtenerHistorialCambios(string $entidad, string $recursoId): array
    {
        try {
            return DB::select(
                'SELECT * FROM sp_historial_cambios(?, ?)',
                [$entidad, $recursoId]
            );
        } catch (\Exception $e) {
            \Log::error('Error obteniendo historial: ' . $e->getMessage());
            return [];
        }
    }

    public static function validarIntegridad(int $userId): array
    {
        try {
            $result = DB::select('SELECT * FROM sp_validar_usuario(?)', [$userId]);
            return !empty($result) ? (array)$result[0] : [];
        } catch (\Exception $e) {
            \Log::error('Error validando integridad: ' . $e->getMessage());
            return [];
        }
    }

    public static function obtenerCambiosCriticos(): array
    {
        try {
            return DB::select('SELECT * FROM sp_cambios_criticos()');
        } catch (\Exception $e) {
            \Log::error('Error obteniendo cambios criticos: ' . $e->getMessage());
            return [];
        }
    }
}
