<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model {

    use HasFactory;

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
}
