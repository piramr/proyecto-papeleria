<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Ajuste;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    /**
     * Obtener productos con stock bajo
     */
    public function stockBajo()
    {
        $ajuste = Ajuste::getOrCreate();
        $stockMinimo = $ajuste->stock_minimo ?? 4;

        $productos = Producto::where('cantidad_stock', '<=', $stockMinimo)
            ->orderBy('cantidad_stock', 'asc')
            ->get();

        return view('admin.alertas.stock-bajo', compact('productos', 'stockMinimo'));
    }

    /**
     * API para obtener alertas de stock bajo (JSON)
     */
    public function apiStockBajo()
    {
        $ajuste = Ajuste::getOrCreate();
        $stockMinimo = $ajuste->stock_minimo ?? 4;

        // Verificar que las notificaciones de stock bajo estén habilitadas
        if (!Ajuste::notificacionesStockBajoHabilitadas()) {
            return response()->json([
                'alertas' => [],
                'total' => 0,
                'habilitadas' => false,
            ]);
        }

        $productos = Producto::where('cantidad_stock', '<=', $stockMinimo)
            ->orderBy('cantidad_stock', 'asc')
            ->get(['id', 'nombre', 'cantidad_stock']);

        return response()->json([
            'alertas' => $productos,
            'total' => $productos->count(),
            'habilitadas' => true,
            'stock_minimo' => $stockMinimo,
        ]);
    }

    /**
     * API para obtener todas las notificaciones (combinadas)
     */
    public function apiTodasAlertas()
    {
        $ajuste = Ajuste::getOrCreate();
        $stockMinimo = $ajuste->stock_minimo ?? 4;

        $notificaciones = [];

        // Alertas de stock bajo - Solo mostrar si las notificaciones están habilitadas
        if (Ajuste::notificacionesStockBajoHabilitadas()) {
            $productosStockBajo = Producto::where('cantidad_stock', '<=', $stockMinimo)
                ->orderBy('cantidad_stock', 'asc')
                ->get();

            foreach ($productosStockBajo as $producto) {
                $notificaciones[] = [
                    'id' => 'stock_' . $producto->id,
                    'tipo' => 'stock_bajo',
                    'titulo' => 'Stock Bajo: ' . $producto->nombre,
                    'mensaje' => 'Stock: ' . $producto->cantidad_stock . ' (Mínimo: ' . $stockMinimo . ')',
                    'icono' => 'fas fa-exclamation-triangle',
                    'color' => 'warning',
                    'url' => route('admin.alertas.stock-bajo'),
                ];
            }
        }

        // Aquí se pueden agregar más tipos de notificaciones en el futuro
        // Por ejemplo:
        // if (Ajuste::notificacionesVentaHabilitadas()) { ... }
        // if (Ajuste::notificacionesCompraHabilitadas()) { ... }

        return response()->json([
            'notificaciones' => $notificaciones,
            'total' => count($notificaciones),
        ]);
    }
}
