<?php

namespace App\Observers;

use App\Models\Proveedor;
use App\Services\Auditoria\AuditoriaService;
use Illuminate\Support\Facades\Auth;

class ProveedorObserver
{
    /**
     * Handle the Proveedor "created" event.
     */
    public function created(Proveedor $proveedor): void
    {
        $userId = Auth::id() ?? 1;

        AuditoriaService::registrarAuditoriaDatos([
            'user_id' => $userId,
            'tipo_operacion' => 'INSERT',
            'entidad' => 'proveedores',
            'recurso_id' => $proveedor->ruc,
            'campo' => 'nombre',
            'valor_original' => null,
            'valor_nuevo' => $proveedor->nombre,
        ]);

        AuditoriaService::registrarLogSistema(
            'INFO',
            "Nuevo proveedor registrado: {$proveedor->nombre} (RUC: {$proveedor->ruc})"
        );
    }

    /**
     * Handle the Proveedor "updated" event.
     */
    public function updated(Proveedor $proveedor): void
    {
        $userId = Auth::id() ?? 1;
        $original = $proveedor->getOriginal();
        $cambios = $proveedor->getChanges();

        foreach ($cambios as $campo => $valor) {
            if ($campo !== 'updated_at') {
                AuditoriaService::registrarAuditoriaDatos([
                    'user_id' => $userId,
                    'tipo_operacion' => 'UPDATE',
                    'entidad' => 'proveedores',
                    'recurso_id' => $proveedor->ruc,
                    'campo' => $campo,
                    'valor_original' => $original[$campo] ?? null,
                    'valor_nuevo' => $valor,
                ]);
            }
        }

        AuditoriaService::registrarLogSistema(
            'INFO',
            "Proveedor actualizado: {$proveedor->nombre}"
        );
    }

    /**
     * Handle the Proveedor "deleted" event.
     */
    public function deleted(Proveedor $proveedor): void
    {
        $userId = Auth::id() ?? 1;

        AuditoriaService::registrarAuditoriaDatos([
            'user_id' => $userId,
            'tipo_operacion' => 'DELETE',
            'entidad' => 'proveedores',
            'recurso_id' => $proveedor->ruc,
            'campo' => 'nombre',
            'valor_original' => $proveedor->nombre,
            'valor_nuevo' => null,
        ]);

        AuditoriaService::registrarLogSistema(
            'WARNING',
            "Proveedor eliminado: {$proveedor->nombre} (RUC: {$proveedor->ruc})"
        );
    }
}
