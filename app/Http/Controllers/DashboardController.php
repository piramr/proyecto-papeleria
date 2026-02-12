<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ingresos totales del mes actual
        $ingresosMes = Factura::whereBetween('fecha_hora', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->sum('total');

        // Ingresos del mes anterior para calcular el cambio porcentual
        $ingresosMesAnterior = Factura::whereBetween('fecha_hora', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('total');

        $cambioIngresos = $ingresosMesAnterior > 0 
            ? (($ingresosMes - $ingresosMesAnterior) / $ingresosMesAnterior) * 100 
            : 0;

        // Total de clientes registrados
        $clientesActivos = Cliente::count();
        
        // Clientes del mes anterior
        $clientesActivosAnterior = Cliente::whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->count();

        $cambioClientes = $clientesActivos > 0 && $clientesActivosAnterior > 0
            ? (($clientesActivos - $clientesActivosAnterior) / $clientesActivos) * 100 
            : 0;

        // Total de ventas del mes
        $ventasMes = Factura::whereBetween('fecha_hora', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->count();

        $ventasMesAnterior = Factura::whereBetween('fecha_hora', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->count();

        $cambioVentas = $ventasMesAnterior > 0 
            ? (($ventasMes - $ventasMesAnterior) / $ventasMesAnterior) * 100 
            : 0;

        // Stock total de productos
        $stockTotal = Producto::sum('cantidad_stock');
        
        // Productos con bajo stock (según stock_minimo de cada producto)
        $productosBajoStock = Producto::where('cantidad_stock', '<', DB::raw('stock_minimo'))->count();

        // Ingresos por mes (últimos 6 meses) - Genera todos los meses incluso sin datos
        $ingresosPorMes = $this->obtenerIngresosPor6Meses();

        // Productos más vendidos
        $productosMasVendidos = DB::table('factura_detalles')
            ->join('productos', 'factura_detalles.producto_id', '=', 'productos.id')
            ->join('facturas', 'factura_detalles.factura_id', '=', 'facturas.id')
            ->where('facturas.fecha_hora', '>=', Carbon::now()->startOfMonth())
            ->where('facturas.fecha_hora', '<=', Carbon::now()->endOfMonth())
            ->select(
                'productos.nombre',
                DB::raw('SUM(factura_detalles.cantidad) as total_vendido'),
                DB::raw('SUM(factura_detalles.total) as ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderBy('total_vendido', 'desc')
            ->limit(5)
            ->get();

        // Últimas ventas registradas
        $ultimasVentas = Factura::with(['cliente', 'tipoPago'])
            ->orderBy('fecha_hora', 'desc')
            ->limit(10)
            ->get();

        // Gastos en compras del mes
        $gastosMes = Compra::whereBetween('fecha_compra', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->sum('total');

        // Utilidad del mes (ingresos - gastos)
        $utilidadMes = $ingresosMes - $gastosMes;

        return view('admin.dashboard', compact(
            'ingresosMes',
            'cambioIngresos',
            'clientesActivos',
            'cambioClientes',
            'ventasMes',
            'cambioVentas',
            'stockTotal',
            'productosBajoStock',
            'ingresosPorMes',
            'productosMasVendidos',
            'ultimasVentas',
            'gastosMes',
            'utilidadMes'
        ));
    }

    /**
     * Obtiene los ingresos de los últimos 6 meses, incluyendo meses sin datos
     */
    private function obtenerIngresosPor6Meses()
    {
        // Obtener todos los datos de facturas de los últimos 6 meses
        $facturas = Factura::select(
            DB::raw("TO_CHAR(fecha_hora, 'YYYY-MM') as yearmes"),
            DB::raw("EXTRACT(YEAR FROM fecha_hora)::INTEGER as anio"),
            DB::raw("EXTRACT(MONTH FROM fecha_hora)::INTEGER as mes"),
            DB::raw('SUM(total) as total')
        )
        ->where('fecha_hora', '>=', Carbon::now()->subMonths(6)->startOfMonth())
        ->groupBy(DB::raw("TO_CHAR(fecha_hora, 'YYYY-MM')"), DB::raw("EXTRACT(YEAR FROM fecha_hora)"), DB::raw("EXTRACT(MONTH FROM fecha_hora)"))
        ->orderBy(DB::raw("TO_CHAR(fecha_hora, 'YYYY-MM')"), 'asc')
        ->get();

        // Crear un array asociativo para búsqueda rápida (usando yearmes como clave)
        $facturasMap = [];
        foreach ($facturas as $f) {
            $facturasMap[$f->yearmes] = $f->total;
        }

        // Generar los últimos 6 meses
        $ingresos = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i)->startOfMonth();
            $mes = $fecha->month;
            $yearmes = $fecha->format('Y-m');
            
            // Si existe en los datos, usar ese total, de lo contrario usar 0
            $total = isset($facturasMap[$yearmes]) ? floatval($facturasMap[$yearmes]) : 0;
            
            $ingresos[] = (object)[
                'mes' => $mes,
                'total' => $total
            ];
        }

        return collect($ingresos);
    }
}

