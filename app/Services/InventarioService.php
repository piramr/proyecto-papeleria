<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class InventarioService
{
    /**
     * Obtener stock disponible de un producto
     */
    public function obtenerStockDisponible(int $productoId): int
    {
        $result = DB::select("SELECT fn_stock_disponible(?) as stock", [$productoId]);
        return $result[0]->stock ?? 0;
    }

    /**
     * Obtener precio final del producto (con oferta si aplica)
     */
    public function obtenerPrecioFinal(int $productoId): float
    {
        $result = DB::select("SELECT fn_obtener_precio_final(?) as precio", [$productoId]);
        return (float)($result[0]->precio ?? 0);
    }

    /**
     * Contar productos en una categoría
     */
    public function contarProductosCategoria(int $categoriaId): int
    {
        $result = DB::select("SELECT fn_contar_productos_categoria(?) as total", [$categoriaId]);
        return $result[0]->total ?? 0;
    }

    /**
     * Obtener valor total del inventario
     */
    public function valorInventarioTotal(): float
    {
        $result = DB::select("SELECT fn_valor_inventario_total() as valor");
        return (float)($result[0]->valor ?? 0);
    }

    /**
     * Calcular margen de ganancia
     */
    public function calcularMargenGanancia(float $precioCosto, float $precioVenta): float
    {
        $result = DB::select("SELECT fn_margen_ganancia(?, ?) as margen", [$precioCosto, $precioVenta]);
        return (float)($result[0]->margen ?? 0);
    }

    /**
     * Actualizar stock de un producto
     *
     * @param int $productoId
     * @param int $cantidad
     * @param string $tipo 'entrada' o 'salida'
     * @param string $razon Razon del movimiento
     * @param int $userId ID del usuario
     * @return bool
     */
    public function actualizarStock(
        int $productoId,
        int $cantidad,
        string $tipo = 'entrada',
        string $razon = '',
        int $userId = null
    ): bool {
        try {
            if ($userId === null) {
                $userId = auth()->id() ?? 1;
            }

            DB::statement(
                'CALL sp_actualizar_stock(?, ?, ?, ?, ?)',
                [$productoId, $cantidad, $tipo, $razon, $userId]
            );

            return true;
        } catch (Exception $e) {
            \Log::error('Error al actualizar stock: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener productos bajo stock minimo
     */
    public function obtenerProductosBajoStock(): int
    {
        $resultado = DB::select('SELECT sp_productos_bajo_stock() as count');
        return (int)($resultado[0]->count ?? 0);
    }

    /**
     * Obtener valor inventario por categoria
     */
    public function valorInventarioPorCategoria(): array
    {
        return DB::select('SELECT * FROM sp_valor_inventario_por_categoria()');
    }

    /**
     * Actualizar precio masivamente
     */
    public function actualizarPrecioMasivo(float $porcentaje, int $categoriaId = null): bool
    {
        try {
            DB::statement('CALL sp_actualizar_precio_masivo(?, ?)', [$porcentaje, $categoriaId]);
            return true;
        } catch (Exception $e) {
            \Log::error('Error al actualizar precio masivo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener productos por proveedor
     */
    public function productoPorProveedor(): array
    {
        return DB::select('SELECT * FROM sp_productos_por_proveedor()');
    }

    /**
     * Obtener movimientos de inventario de un producto
     */
    public function obtenerMovimientos(int $productoId, int $limit = 50)
    {
        return DB::table('log_movimiento_inventario')
            ->where('producto_id', $productoId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Validar si se puede hacer movimiento
     */
    public function validarMovimiento(int $productoId, int $cantidad, string $tipo): bool
    {
        if ($tipo === 'salida') {
            $stockActual = $this->obtenerStockDisponible($productoId);
            return $stockActual >= $cantidad;
        }
        return true;
    }
}
