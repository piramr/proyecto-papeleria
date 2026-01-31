<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model {
    use HasFactory;

    protected $table = 'compra_detalles';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relación: un detalle de compra pertenece a una compra
     */
    public function compra() {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Relación: un detalle de compra pertenece a un producto
     */
    public function producto() {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Calcular subtotal
     */
    public function calcularSubtotal() {
        $this->subtotal = $this->cantidad * $this->precio_unitario;
        return $this;
    }
}
