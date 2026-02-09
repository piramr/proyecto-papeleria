<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ajuste;
use App\Models\Auditoria\LogOperacion;
use App\Models\Auditoria\LogSistema;
use App\Models\Auditoria\LogLogin;
use App\Services\Auditoria\AuditoriaService;
use Illuminate\Support\Facades\Auth;

class AjustesController extends Controller
{
    public function index()
    {
        $ajuste = Ajuste::getOrCreate();
        return view('auditor.ajustes', compact('ajuste'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'log_operacion_retencion' => 'required|integer|min:7|max:365',
            'log_sistema_retencion' => 'required|integer|min:7|max:365',
            'log_login_retencion' => 'required|integer|min:7|max:365',
        ]);

        $ajuste = Ajuste::getOrCreate();
        $ajuste->update([
            'log_operacion_retencion' => $request->log_operacion_retencion,
            'log_operacion_auto_delete' => $request->has('log_operacion_auto_delete'),
            'log_sistema_retencion' => $request->log_sistema_retencion,
            'log_sistema_auto_delete' => $request->has('log_sistema_auto_delete'),
            'log_login_retencion' => $request->log_login_retencion,
            'log_login_auto_delete' => $request->has('log_login_auto_delete'),
        ]);

        AuditoriaService::registrarLogSistema('INFO', '[CONFIGURACIÓN] Ajustes de retención actualizados por usuario ID: ' . Auth::user()->id());

        return redirect()->route('auditor.ajustes')->with('success', 'Configuración de retención guardada correctamente.');
    }

    public function limpiarLog(Request $request, string $tipo)
    {
        $eliminados = 0;
        $nombreTabla = '';

        switch ($tipo) {
            case 'log_operacion':
                $eliminados = LogOperacion::count();
                LogOperacion::truncate();
                $nombreTabla = 'LOG_OPERACION';
                break;
            case 'log_sistema':
                $eliminados = LogSistema::count();
                LogSistema::truncate();
                $nombreTabla = 'LOG_SISTEMA';
                break;
            case 'log_login':
                $eliminados = LogLogin::count();
                LogLogin::truncate();
                $nombreTabla = 'LOG_LOGIN';
                break;
            default:
                return redirect()->route('auditor.ajustes')->with('error', 'Tipo de log no válido.');
        }

        AuditoriaService::registrarLogSistema('INFO', "[CONFIGURACIÓN] Limpieza manual de {$nombreTabla}. Eliminados: {$eliminados} registros.");

        return redirect()->route('auditor.ajustes')->with('success', "Limpieza de {$nombreTabla} completada. Se eliminaron {$eliminados} registros.");
    }

    public function apiStats()
    {
        $ajuste = Ajuste::getOrCreate();
        
        // Contar registros por tabla
        $stats = [
            'log_operacion' => [
                'total' => LogOperacion::count(),
                'antiguos' => LogOperacion::where('timestamp', '<', now()->subDays($ajuste->log_operacion_retencion ?? 90))->count(),
                'retencion' => $ajuste->log_operacion_retencion ?? 90,
                'auto_delete' => $ajuste->log_operacion_auto_delete ?? true,
            ],
            'log_sistema' => [
                'total' => LogSistema::count(),
                'antiguos' => LogSistema::where('timestamp', '<', now()->subDays($ajuste->log_sistema_retencion ?? 30))->count(),
                'retencion' => $ajuste->log_sistema_retencion ?? 30,
                'auto_delete' => $ajuste->log_sistema_auto_delete ?? true,
            ],
            'log_login' => [
                'total' => LogLogin::count(),
                'antiguos' => LogLogin::where('timestamp', '<', now()->subDays($ajuste->log_login_retencion ?? 15))->count(),
                'retencion' => $ajuste->log_login_retencion ?? 15,
                'auto_delete' => $ajuste->log_login_auto_delete ?? true,
            ],
        ];

        return response()->json($stats);
    }
}
