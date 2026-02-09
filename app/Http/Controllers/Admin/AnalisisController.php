<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalisisController extends Controller
{
    public function index()
    {
        // Métricas principales
        $totalVentas = Factura::sum('total');
        $ventasHoy = Factura::whereDate('created_at', Carbon::today())->sum('total');
        $totalClientes = Cliente::count();
        $totalProductos = Producto::sum('cantidad_stock');
        $totalPedidos = Factura::count();

        // Ventas últimos 7 días
        $ventasÚltimos7Días = $this->ventasÚltimos7Días();
        
        // Productos más vendidos
        $productosMásVendidos = $this->productosMásVendidos();
        
        // Categorías más vendidas
        $categoriasMásVendidas = $this->categoriasMásVendidas();
        
        // Clientes con más compras
        $clientesMásCompras = $this->clientesMásCompras();
        
        // Ingresos por mes (últimos 12 meses)
        $ingresosPorMes = $this->ingresosPorMes();
        
        // Stock de productos
        $productosBajoStock = Producto::where('cantidad_stock', '<', DB::raw('stock_minimo'))->count();
        $productosEnOferta = Producto::where('en_oferta', true)->count();

        return view('admin.analisis.index', compact(
            'totalVentas',
            'ventasHoy',
            'totalClientes',
            'totalProductos',
            'totalPedidos',
            'ventasÚltimos7Días',
            'productosMásVendidos',
            'categoriasMásVendidas',
            'clientesMásCompras',
            'ingresosPorMes',
            'productosBajoStock',
            'productosEnOferta'
        ));
    }

    /**
     * Obtiene las ventas de los últimos 7 días
     */
    private function ventasÚltimos7Días()
    {
        $hoy = Carbon::today();
        $datos = [];
        $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sab'];

        for ($i = 6; $i >= 0; $i--) {
            $fecha = $hoy->clone()->subDays($i);
            $total = Factura::whereDate('created_at', $fecha)->sum('total');
            
            $datos[] = [
                'fecha' => $diasSemana[$fecha->dayOfWeek],
                'fecha_completa' => $fecha->format('Y-m-d'),
                'total' => floatval($total),
            ];
        }

        return $datos;
    }

    /**
     * Productos más vendidos (últimos 30 días)
     */
    private function productosMásVendidos()
    {
        $hace30Días = Carbon::now()->subDays(30);

        return DB::table('factura_detalles as fd')
            ->select(
                DB::raw('p.nombre as producto_nombre'),
                DB::raw('SUM(fd.cantidad) as total_vendido'),
                DB::raw('SUM(fd.cantidad * fd.precio_unitario) as ingresos')
            )
            ->join('productos as p', 'fd.producto_id', '=', 'p.id')
            ->join('facturas as f', 'fd.factura_id', '=', 'f.id')
            ->where('f.created_at', '>=', $hace30Días)
            ->groupBy('p.id', 'p.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();
    }

    /**
     * Categorías más vendidas
     */
    private function categoriasMásVendidas()
    {
        $hace30Días = Carbon::now()->subDays(30);

        return DB::table('factura_detalles as fd')
            ->select(
                DB::raw('cat.nombre as categoria'),
                DB::raw('COUNT(DISTINCT fd.factura_id) as total_vendido'),
                DB::raw('SUM(fd.cantidad * fd.precio_unitario) as ingresos')
            )
            ->join('productos as p', 'fd.producto_id', '=', 'p.id')
            ->join('categorias as cat', 'p.categoria_id', '=', 'cat.id')
            ->join('facturas as f', 'fd.factura_id', '=', 'f.id')
            ->where('f.created_at', '>=', $hace30Días)
            ->groupBy('cat.id', 'cat.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();
    }

    /**
     * Clientes con más compras
     */
    private function clientesMásCompras()
    {
        $hace30Días = Carbon::now()->subDays(30);

        return DB::table('facturas as f')
            ->select(
                DB::raw('CONCAT(c.nombres, " ", c.apellidos) as cliente_nombre'),
                DB::raw('COUNT(*) as total_compras'),
                DB::raw('SUM(f.total) as monto_total')
            )
            ->join('clientes as c', 'f.cliente_cedula', '=', 'c.cedula')
            ->where('f.created_at', '>=', $hace30Días)
            ->groupBy('c.cedula')
            ->orderByDesc('total_compras')
            ->limit(5)
            ->get();
    }

    /**
     * Ingresos por mes (últimos 12 meses)
     */
    private function ingresosPorMes()
    {
        $meses = [];
        $etiquetas = [];
        $mesesNombres = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

        for ($i = 11; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $mes = $fecha->format('Y-m');
            $total = DB::table('facturas')
                ->whereRaw('strftime("%Y-%m", created_at) = ?', [$mes])
                ->sum('total');
            
            $meses[] = floatval($total ?? 0);
            $etiquetas[] = $mesesNombres[$fecha->month - 1];
        }

        return [
            'etiquetas' => $etiquetas,
            'datos' => $meses,
        ];
    }
}
