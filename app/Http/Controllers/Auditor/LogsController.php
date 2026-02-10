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

class LogsController extends Controller
{
    public function index()
    {
        return view('auditor.logs');
    }

    public function apiLogs()
    {
        $operaciones = LogOperacion::with('user')->orderByDesc('timestamp')->limit(100)->get();
        $sistema = LogSistema::with('nivel')->orderByDesc('timestamp')->limit(100)->get();
        $login = LogLogin::with('resultado')->orderByDesc('timestamp')->limit(100)->get();
        return response()->json([
            'operaciones' => $operaciones,
            'sistema' => $sistema,
            'login' => $login,
        ]);
    }

    /**
     * Calcular estadísticas para el gráfico Likert de salud del sistema
     * Criterios:
     * 1. Crítico: LOG_SISTEMA con nivel FATAL o LOG_LOGIN con USUARIO_BLOQUEADO
     * 2. Riesgo: LOG_OPERACION con resultado ERROR o LOG_SISTEMA con nivel ERROR
     * 3. Neutral: LOG_LOGIN con reintento > 1 o LOG_SISTEMA con nivel WARNING
     * 4. Estable: LOG_LOGIN con CODIGO_ENVIADO o LOG_SISTEMA con nivel INFO
     * 5. Óptimo: LOG_OPERACION con resultado OK o LOG_LOGIN con EXITOSO
     */
    public function apiLikert(Request $request)
    {
        // Obtener fechas del filtro
        $fechaInicio = $request->input('fecha_inicio', now()->subDays(7)->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());

        // Obtener IDs de niveles
        $nivelFatal = LogNivel::where('nombre', 'FATAL')->value('id');
        $nivelError = LogNivel::where('nombre', 'ERROR')->value('id');
        $nivelWarning = LogNivel::where('nombre', 'WARNING')->value('id');
        $nivelInfo = LogNivel::where('nombre', 'INFO')->value('id');

        // Obtener IDs de resultados de login
        $resultadoBloqueado = LogLoginResultado::where('nombre', 'USUARIO_BLOQUEADO')->value('id');
        $resultadoExitoso = LogLoginResultado::where('nombre', 'EXITOSO')->value('id');
        $resultadoCodigoEnviado = LogLoginResultado::where('nombre', 'CODIGO_ENVIADO')->value('id');
        $resultadoIntentosAgotados = LogLoginResultado::where('nombre', 'INTENTOS_AGOTADOS')->value('id');

        // 1. CRÍTICO: LOG_SISTEMA con FATAL o LOG_LOGIN con USUARIO_BLOQUEADO/INTENTOS_AGOTADOS
        $critico = 0;
        if ($nivelFatal) {
            $critico += LogSistema::where('nivel_log_id', $nivelFatal)
                ->whereDate('timestamp', '>=', $fechaInicio)
                ->whereDate('timestamp', '<=', $fechaFin)
                ->count();
        }
        if ($resultadoBloqueado) {
            $critico += LogLogin::where('resultado_log_id', $resultadoBloqueado)
                ->whereDate('timestamp', '>=', $fechaInicio)
                ->whereDate('timestamp', '<=', $fechaFin)
                ->count();
        }
        if ($resultadoIntentosAgotados) {
            $critico += LogLogin::where('resultado_log_id', $resultadoIntentosAgotados)
                ->whereDate('timestamp', '>=', $fechaInicio)
                ->whereDate('timestamp', '<=', $fechaFin)
                ->count();
        }

        // 2. RIESGO: LOG_OPERACION con ERROR o LOG_SISTEMA con ERROR
        $riesgo = 0;
        $riesgo += LogOperacion::whereIn('resultado', ['ERROR', 'fallido', 'error'])
            ->whereDate('timestamp', '>=', $fechaInicio)
            ->whereDate('timestamp', '<=', $fechaFin)
            ->count();
        if ($nivelError) {
            $riesgo += LogSistema::where('nivel_log_id', $nivelError)
                ->whereDate('timestamp', '>=', $fechaInicio)
                ->whereDate('timestamp', '<=', $fechaFin)
                ->count();
        }

        // 3. NEUTRAL: LOG_LOGIN con reintento > 1 o LOG_SISTEMA con WARNING
        $neutral = 0;
        $neutral += LogLogin::where('reintento', '>', 1)
            ->whereDate('timestamp', '>=', $fechaInicio)
            ->whereDate('timestamp', '<=', $fechaFin)
            ->count();
        if ($nivelWarning) {
            $neutral += LogSistema::where('nivel_log_id', $nivelWarning)
                ->whereDate('timestamp', '>=', $fechaInicio)
                ->whereDate('timestamp', '<=', $fechaFin)
                ->count();
        }

        // 4. ESTABLE: LOG_LOGIN con CODIGO_ENVIADO o LOG_SISTEMA con INFO
        $estable = 0;
        if ($resultadoCodigoEnviado) {
            $estable += LogLogin::where('resultado_log_id', $resultadoCodigoEnviado)
                ->whereDate('timestamp', '>=', $fechaInicio)
                ->whereDate('timestamp', '<=', $fechaFin)
                ->count();
        }
        if ($nivelInfo) {
            $estable += LogSistema::where('nivel_log_id', $nivelInfo)
                ->whereDate('timestamp', '>=', $fechaInicio)
                ->whereDate('timestamp', '<=', $fechaFin)
                ->count();
        }

        // 5. ÓPTIMO: LOG_OPERACION con OK o LOG_LOGIN con EXITOSO
        $optimo = 0;
        $optimo += LogOperacion::whereIn('resultado', ['OK', 'exitoso', 'ok'])
            ->whereDate('timestamp', '>=', $fechaInicio)
            ->whereDate('timestamp', '<=', $fechaFin)
            ->count();
        if ($resultadoExitoso) {
            $optimo += LogLogin::where('resultado_log_id', $resultadoExitoso)
                ->whereDate('timestamp', '>=', $fechaInicio)
                ->whereDate('timestamp', '<=', $fechaFin)
                ->count();
        }

        // Calcular total y porcentajes
        $total = $critico + $riesgo + $neutral + $estable + $optimo;
        
        if ($total === 0) {
            // Si no hay datos, mostrar distribución vacía
            return response()->json([
                'critico' => 0,
                'riesgo' => 0,
                'neutral' => 0,
                'estable' => 0,
                'optimo' => 0,
                'total' => 0,
                'critico_count' => 0,
                'riesgo_count' => 0,
                'neutral_count' => 0,
                'estable_count' => 0,
                'optimo_count' => 0,
            ]);
        }

        return response()->json([
            'critico' => round(($critico / $total) * 100, 1),
            'riesgo' => round(($riesgo / $total) * 100, 1),
            'neutral' => round(($neutral / $total) * 100, 1),
            'estable' => round(($estable / $total) * 100, 1),
            'optimo' => round(($optimo / $total) * 100, 1),
            'total' => $total,
            'critico_count' => $critico,
            'riesgo_count' => $riesgo,
            'neutral_count' => $neutral,
            'estable_count' => $estable,
            'optimo_count' => $optimo,
        ]);
    }
}
