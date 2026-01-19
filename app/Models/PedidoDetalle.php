<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoDetalle extends Model {
    use HasFactory;

    protected $fillable = [
        'cantidad',
        'precio_compra',
        'total',
        'proveedor_ruc',
        'producto_id',
    ];

    /**
     * Relación: un detalle pertenece a un pedido
     */
    public function pedido() {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Relación: un detalle pertenece a un producto
     */
    public function producto() {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación: un detalle pertenece a un proveedor
     */
    public function proveedor() {
        return $this->belongsTo(Proveedor::class, 'proveedor_ruc', 'ruc');
    }
}
