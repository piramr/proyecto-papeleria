<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoPedido extends Model {
    use HasFactory;

    protected $table = 'estados_pedido';

    protected $fillable = ['nombre', 'descripcion'];

    /**
     * Relación: un estado tiene muchos pedidos
     */
    public function pedidos() {
        return $this->hasMany(Pedido::class, 'estado_pedido_id');
    }
}
