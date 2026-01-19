<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model {
    use HasFactory;

    protected $fillable = [
        'fecha_hora',
        'descripcion',
        'subtotal',
        'total',
        'cliente_cedula',
        'usuario_id',
        'tipo_pago_id',
    ];

    /**
     * Relación: una factura pertenece a un cliente
     */
    public function cliente() {
        return $this->belongsTo(Cliente::class, 'cliente_cedula', 'cedula');
    }

    /**
     * Relación: una factura pertenece a un usuario
     */
    public function usuario() {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: una factura pertenece a un tipo de pago
     */
    public function tipoPago() {
        return $this->belongsTo(TipoPago::class, 'tipo_pago_id');
    }

    /**
     * Relación: una factura tiene muchos detalles
     */
    public function detalles() {
        return $this->hasMany(FacturaDetalle::class);
    }
}
