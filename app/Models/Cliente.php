<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model {
    use HasFactory;

    protected $table = 'clientes';

    protected $primaryKey = 'cedula';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cedula',
        'nombres',
        'apellidos',
        'telefono',
        'email',
        'genero',
        'fecha_nacimiento',
        'direccion',
    ];

    /**
     * Relaciona pedidos del cliente
     */
    public function facturas() {
        return $this->hasMany(Pedido::class, 'cliente_cedula', 'cedula');
    }
}
