<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacturaDetalle extends Model {
    use HasFactory;

    protected $table = 'factura_detalles';

    protected $fillable = [
        'precio_unitario',
        'cantidad',
        'total',
        'factura_id',
        'producto_id',
    ];

    /**
     * Relación: un detalle pertenece a una factura
     */
    public function factura() {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Relación: un detalle pertenece a un producto
     */
    public function producto() {
        return $this->belongsTo(Producto::class);
    }
}
