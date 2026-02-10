<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\Auditoria\GenericAuditoriaObserver;

class AuditoriaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $models = [
            'Ajuste',
            'Categoria',
            'Cliente',
            'Compra',
            'CompraDetalle',
            'EstadoPedido',
            'Factura',
            'FacturaDetalle',
            'Pedido',
            'PedidoDetalle',
            'Producto',
            'Proveedor',
            'ProveedorDireccion',
            'TipoPago',
            'User',
        ];
        foreach ($models as $model) {
            $class = "App\\Models\\$model";
            if (class_exists($class)) {
                $class::observe(GenericAuditoriaObserver::class);
            }
        }
    }
}
