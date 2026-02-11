<?php
namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auditoria\AuditoriaDatos;
use Illuminate\Support\Facades\DB;

class AuditoriaController extends Controller
{
    public function index()
    {
        return view('auditor.auditoria');
    }

    public function apiAuditoria(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $recurso = $request->input('recurso');
        $operacion = $request->input('operacion');
        
        $query = AuditoriaDatos::with('user.roles')->orderByDesc('timestamp');
        
        // Filtrar por recurso/entidad
        if ($recurso && $recurso !== 'Todos') {
            $query->where('entidad', $recurso);
        }
        
        // Filtrar por operación
        if ($operacion && $operacion !== 'Todas') {
            $query->where('tipo_operacion', $operacion);
        }
        
        $auditoria = $query->paginate($perPage, ['*'], 'page', $page);
        
        return response()->json([
            'auditoria' => $auditoria->items(),
            'total' => $auditoria->total(),
            'per_page' => $auditoria->perPage(),
            'current_page' => $auditoria->currentPage(),
            'last_page' => $auditoria->lastPage(),
            'from' => $auditoria->firstItem(),
            'to' => $auditoria->lastItem(),
        ]);
    }

    /**
     * Obtener estadísticas para el gráfico de operaciones por día
     */
    public function apiChartData(Request $request)
    {
        $dias = $request->input('dias', 7);
        $fechaInicio = now()->subDays($dias - 1)->startOfDay();
        
        // Obtener conteo de operaciones por día y tipo
        $datos = AuditoriaDatos::select(
                DB::raw('DATE(timestamp) as fecha'),
                'tipo_operacion',
                DB::raw('COUNT(*) as total')
            )
            ->where('timestamp', '>=', $fechaInicio)
            ->groupBy(DB::raw('DATE(timestamp)'), 'tipo_operacion')
            ->orderBy('fecha')
            ->get();
        
        // Preparar estructura de datos por fecha
        $fechas = [];
        $create = [];
        $update = [];
        $delete = [];
        
        // Generar todas las fechas del rango
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $fechas[] = now()->subDays($i)->format('d M');
            $create[$fecha] = 0;
            $update[$fecha] = 0;
            $delete[$fecha] = 0;
        }
        
        // Llenar con los datos reales
        foreach ($datos as $d) {
            $fecha = $d->fecha;
            if (isset($create[$fecha])) {
                if ($d->tipo_operacion === 'CREATE') {
                    $create[$fecha] = $d->total;
                } elseif ($d->tipo_operacion === 'UPDATE') {
                    $update[$fecha] = $d->total;
                } elseif ($d->tipo_operacion === 'DELETE') {
                    $delete[$fecha] = $d->total;
                }
            }
        }
        
        return response()->json([
            'labels' => $fechas,
            'create' => array_values($create),
            'update' => array_values($update),
            'delete' => array_values($delete),
        ]);
    }
}
