<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model {
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'fecha_hora',
        'total',
        'proveedor_ruc',
        'usuario_id',
        'estado_pedido_id',
    ];

    /**
     * Relación: un pedido pertenece a un usuario
     */
    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Relación: un pedido pertenece a un estado
     */
    public function estado() {
        return $this->belongsTo(EstadoPedido::class, 'estado_pedido_id');
    }

    /**
     * Relación: un pedido tiene muchos detalles
     */
    public function detalles() {
        return $this->hasMany(PedidoDetalle::class);
    }
}
