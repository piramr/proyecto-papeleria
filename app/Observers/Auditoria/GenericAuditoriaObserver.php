<?php
namespace App\Observers\Auditoria;

use App\Services\Auditoria\AuditoriaService;
use Illuminate\Database\Eloquent\Model;

class GenericAuditoriaObserver
{
    protected function getRecursoPadreId(Model $model)
    {
        // Detectar campos de relación padre comunes
        foreach ([
            'compra_id', 'pedido_id', 'factura_id', 'user_id', 'cliente_id', 'proveedor_id', 'proveedor_ruc', 'categoria_id'
        ] as $parentKey) {
            if (isset($model->$parentKey)) {
                return $model->$parentKey;
            }
        }
        return null;
    }

    public function created(Model $model)
    {
        $attributes = $model->getAttributes();
        $recurso_padre_id = $this->getRecursoPadreId($model);
        foreach ($attributes as $campo => $valor_nuevo) {
            if (in_array($campo, ['created_at', 'updated_at', 'deleted_at'])) continue;
            AuditoriaService::registrarAuditoriaDatos([
                'tipo_operacion' => 'CREATE',
                'entidad' => class_basename($model),
                'recurso_id' => $model->getKey(),
                'recurso_padre_id' => $recurso_padre_id,
                'campo' => $campo,
                'valor_original' => null,
                'valor_nuevo' => $valor_nuevo,
            ]);
        }
    }

    public function updated(Model $model)
    {
        $changes = $model->getChanges();
        $original = $model->getOriginal();
        $recurso_padre_id = $this->getRecursoPadreId($model);
        foreach ($changes as $campo => $valor_nuevo) {
            AuditoriaService::registrarAuditoriaDatos([
                'tipo_operacion' => 'UPDATE',
                'entidad' => class_basename($model),
                'recurso_id' => $model->getKey(),
                'recurso_padre_id' => $recurso_padre_id,
                'campo' => $campo,
                'valor_original' => $original[$campo] ?? null,
                'valor_nuevo' => $valor_nuevo,
            ]);
        }
    }

    public function deleted(Model $model)
    {
        $recurso_padre_id = $this->getRecursoPadreId($model);
        AuditoriaService::registrarAuditoriaDatos([
            'tipo_operacion' => 'DELETE',
            'entidad' => class_basename($model),
            'recurso_id' => $model->getKey(),
            'recurso_padre_id' => $recurso_padre_id,
            'campo' => null,
            'valor_original' => null,
            'valor_nuevo' => null,
        ]);
    }
}
