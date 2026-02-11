<?php

namespace App\Observers;

use App\Models\Categoria;
use App\Services\Auditoria\AuditoriaService;
use Illuminate\Support\Facades\Auth;

class CategoriaObserver
{
    /**
     * Handle the Categoria "created" event.
     */
    public function created(Categoria $categoria): void
    {
        $userId = Auth::id() ?? 1;

        AuditoriaService::registrarAuditoriaDatos([
            'user_id' => $userId,
            'tipo_operacion' => 'INSERT',
            'entidad' => 'categorias',
            'recurso_id' => $categoria->id,
            'campo' => 'nombre',
            'valor_original' => null,
            'valor_nuevo' => $categoria->nombre,
        ]);

        AuditoriaService::registrarLogSistema(
            'INFO',
            "Nueva categoría creada: {$categoria->nombre}"
        );
    }

    /**
     * Handle the Categoria "updated" event.
     */
    public function updated(Categoria $categoria): void
    {
        $userId = Auth::id() ?? 1;
        $original = $categoria->getOriginal();
        $cambios = $categoria->getChanges();

        foreach ($cambios as $campo => $valor) {
            if ($campo !== 'updated_at') {
                AuditoriaService::registrarAuditoriaDatos([
                    'user_id' => $userId,
                    'tipo_operacion' => 'UPDATE',
                    'entidad' => 'categorias',
                    'recurso_id' => $categoria->id,
                    'campo' => $campo,
                    'valor_original' => $original[$campo] ?? null,
                    'valor_nuevo' => $valor,
                ]);
            }
        }
    }

    /**
     * Handle the Categoria "deleted" event.
     */
    public function deleted(Categoria $categoria): void
    {
        $userId = Auth::id() ?? 1;

        AuditoriaService::registrarAuditoriaDatos([
            'user_id' => $userId,
            'tipo_operacion' => 'DELETE',
            'entidad' => 'categorias',
            'recurso_id' => $categoria->id,
            'campo' => 'nombre',
            'valor_original' => $categoria->nombre,
            'valor_nuevo' => null,
        ]);

        AuditoriaService::registrarLogSistema(
            'WARNING',
            "Categoría eliminada: {$categoria->nombre}"
        );
    }
}
