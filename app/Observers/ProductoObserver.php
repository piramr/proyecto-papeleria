<?php

namespace App\Observers;

use App\Models\Producto;
use App\Services\Auditoria\AuditoriaService;
use Illuminate\Support\Facades\Auth;

class ProductoObserver
{
    /**
     * Handle the Producto "created" event.
     */
    public function created(Producto $producto): void
    {
        $userId = Auth::id() ?? 1;

        // Registrar en auditoría
        AuditoriaService::registrarAuditoriaDatos([
            'user_id' => $userId,
            'tipo_operacion' => 'INSERT',
            'entidad' => 'productos',
            'recurso_id' => $producto->id,
            'campo' => 'nombre',
            'valor_original' => null,
            'valor_nuevo' => $producto->nombre,
        ]);

        // Log del sistema
        AuditoriaService::registrarLogSistema(
            'INFO',
            "Nuevo producto creado: {$producto->nombre} (ID: {$producto->id})"
        );
    }

    /**
     * Handle the Producto "updated" event.
     */
    public function updated(Producto $producto): void
    {
        $userId = Auth::id() ?? 1;
        $original = $producto->getOriginal();
        $cambios = $producto->getChanges();

        // Registrar cada campo que cambió
        foreach ($cambios as $campo => $valor) {
            if ($campo !== 'updated_at') {
                AuditoriaService::registrarAuditoriaDatos([
                    'user_id' => $userId,
                    'tipo_operacion' => 'UPDATE',
                    'entidad' => 'productos',
                    'recurso_id' => $producto->id,
                    'campo' => $campo,
                    'valor_original' => $original[$campo] ?? null,
                    'valor_nuevo' => $valor,
                ]);
            }
        }

        // Log de cambios importantes
        if (isset($cambios['precio_unitario'])) {
            AuditoriaService::registrarLogSistema(
                'WARNING',
                "Cambio de precio en {$producto->nombre}: {$original['precio_unitario']} → {$cambios['precio_unitario']}"
            );
        }

        if (isset($cambios['cantidad_stock']) && $cambios['cantidad_stock'] < $producto->stock_minimo) {
            AuditoriaService::registrarLogSistema(
                'WARNING',
                "Stock crítico en {$producto->nombre}: {$cambios['cantidad_stock']} unidades (mínimo: {$producto->stock_minimo})"
            );
        }
    }

    /**
     * Handle the Producto "deleted" event.
     */
    public function deleted(Producto $producto): void
    {
        $userId = Auth::id() ?? 1;

        AuditoriaService::registrarAuditoriaDatos([
            'user_id' => $userId,
            'tipo_operacion' => 'DELETE',
            'entidad' => 'productos',
            'recurso_id' => $producto->id,
            'campo' => 'nombre',
            'valor_original' => $producto->nombre,
            'valor_nuevo' => null,
        ]);

        AuditoriaService::registrarLogSistema(
            'WARNING',
            "Producto eliminado: {$producto->nombre} (ID: {$producto->id})"
        );
    }
}
