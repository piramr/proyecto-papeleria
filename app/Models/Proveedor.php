<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model {

    use HasFactory;

    protected $table = 'proveedores';
    protected $primaryKey = 'ruc';
    protected $keyType = 'string';
    public $incrementing = false;


    protected $fillable = [
        'ruc',
        'nombre',
        'email',
        'telefono_principal',
        'telefono_secundario'
    ];

    public function direcciones() {
        return $this->hasMany(ProveedorDireccion::class, 'proveedor_ruc', 'ruc');
    }

    public function pedidos() {
        return $this->hasMany(Pedido::class, 'proveedor_ruc', 'ruc');
    }

    public function detallesPedidos() {
        return $this->hasMany(PedidoDetalle::class, 'proveedor_ruc', 'ruc');
    }

    public function productos() {
        return $this->belongsToMany(Producto::class, 'producto_proveedores', 'proveedor_ruc', 'producto_id', 'ruc', 'id');
    }
}
