<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auditoria\LogOperacion;
use App\Models\Auditoria\LogSistema;
use App\Models\Auditoria\LogLogin;
use App\Models\Auditoria\LogNivel;
use App\Models\Auditoria\LogLoginResultado;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('auditor.dashboard');
    }

    public function apiStats()
    {
        $hoy = now()->startOfDay();
        $ayer = now()->subDay()->startOfDay();
        
        // 1. Errores en las últimas 24h (log_sistema con nivel ERROR o FATAL + log_operacion con resultado ERROR)
        $nivelError = LogNivel::where('nombre', 'ERROR')->value('id');
        $nivelFatal = LogNivel::where('nombre', 'FATAL')->value('id');
        
        $erroresSistema = LogSistema::where('timestamp', '>=', $hoy)
            ->whereIn('nivel_log_id', array_filter([$nivelError, $nivelFatal]))
            ->count();
        
        $erroresOperacion = LogOperacion::where('timestamp', '>=', $hoy)
            ->whereIn('resultado', ['ERROR', 'error', 'fallido'])
            ->count();
        
        $erroresHoy = $erroresSistema + $erroresOperacion;
        
        // Errores de ayer para comparación
        $erroresSistemaAyer = LogSistema::whereBetween('timestamp', [$ayer, $hoy])
            ->whereIn('nivel_log_id', array_filter([$nivelError, $nivelFatal]))
            ->count();
        $erroresOperacionAyer = LogOperacion::whereBetween('timestamp', [$ayer, $hoy])
            ->whereIn('resultado', ['ERROR', 'error', 'fallido'])
            ->count();
        $erroresAyer = $erroresSistemaAyer + $erroresOperacionAyer;
        
        $erroresCambio = $erroresAyer > 0 ? round((($erroresHoy - $erroresAyer) / $erroresAyer) * 100) : 0;
        
        // 2. Alertas de login (intentos fallidos hoy)
        $resultadoFallidos = LogLoginResultado::whereIn('nombre', ['CONTRASEÑA_INVALIDA', 'USUARIO_NO_ENCONTRADO', 'CODIGO_INVALIDO', 'INTENTOS_AGOTADOS'])
            ->pluck('id')->toArray();
        $resultadoBloqueado = LogLoginResultado::where('nombre', 'USUARIO_BLOQUEADO')->value('id');
        
        $alertasLogin = LogLogin::where('timestamp', '>=', $hoy)
            ->whereIn('resultado_log_id', $resultadoFallidos)
            ->count();
        
        $usuariosBloqueados = LogLogin::where('timestamp', '>=', $hoy)
            ->where('resultado_log_id', $resultadoBloqueado)
            ->distinct('user_email')
            ->count('user_email');
        
        // 3. Operaciones exitosas hoy
        $operacionesExitosas = LogOperacion::where('timestamp', '>=', $hoy)
            ->whereIn('resultado', ['OK', 'ok', 'exitoso'])
            ->count();
        
        // 4. Logins exitosos hoy
        $resultadoExitoso = LogLoginResultado::where('nombre', 'EXITOSO')->value('id');
        $loginsExitosos = LogLogin::where('timestamp', '>=', $hoy)
            ->where('resultado_log_id', $resultadoExitoso)
            ->count();
        
        // 5. Últimas actividades importantes (para la tabla)
        $actividadesRecientes = LogOperacion::where('timestamp', '>=', now()->subHours(24))
            ->orderByDesc('timestamp')
            ->limit(5)
            ->get(['entidad', 'tipo_operacion', 'user_id', 'timestamp', 'resultado']);
        
        // 6. Datos para el gráfico de 7 días
        $chartData = $this->getChartData();
        
        return response()->json([
            'errores_hoy' => $erroresHoy,
            'errores_cambio' => $erroresCambio,
            'alertas_login' => $alertasLogin,
            'usuarios_bloqueados' => $usuariosBloqueados,
            'operaciones_exitosas' => $operacionesExitosas,
            'logins_exitosos' => $loginsExitosos,
            'actividades_recientes' => $actividadesRecientes,
            'chart' => $chartData,
        ]);
    }

    private function getChartData()
    {
        $dias = 7;
        $labels = [];
        $errores = [];
        $operaciones = [];
        $logins = [];
        
        $nivelError = LogNivel::where('nombre', 'ERROR')->value('id');
        $nivelFatal = LogNivel::where('nombre', 'FATAL')->value('id');
        $resultadoExitoso = LogLoginResultado::where('nombre', 'EXITOSO')->value('id');
        
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            $inicioDelDia = $fecha->copy()->startOfDay();
            $finDelDia = $fecha->copy()->endOfDay();
            
            $labels[] = $fecha->locale('es')->isoFormat('ddd');
            
            // Errores del día
            $erroresDia = LogSistema::whereBetween('timestamp', [$inicioDelDia, $finDelDia])
                ->whereIn('nivel_log_id', array_filter([$nivelError, $nivelFatal]))
                ->count();
            $erroresDia += LogOperacion::whereBetween('timestamp', [$inicioDelDia, $finDelDia])
                ->whereIn('resultado', ['ERROR', 'error', 'fallido'])
                ->count();
            $errores[] = $erroresDia;
            
            // Operaciones exitosas del día
            $operaciones[] = LogOperacion::whereBetween('timestamp', [$inicioDelDia, $finDelDia])
                ->whereIn('resultado', ['OK', 'ok', 'exitoso'])
                ->count();
            
            // Logins del día
            $logins[] = LogLogin::whereBetween('timestamp', [$inicioDelDia, $finDelDia])
                ->where('resultado_log_id', $resultadoExitoso)
                ->count();
        }
        
        return [
            'labels' => $labels,
            'errores' => $errores,
            'operaciones' => $operaciones,
            'logins' => $logins,
        ];
    }
}
