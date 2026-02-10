<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProveedorDireccion extends Model {

    use HasFactory;

    protected $table = 'proveedor_direcciones';

    protected $fillable = [
        'provincia',
        'ciudad',
        'calle',
        'referencia',
        'proveedor_ruc'
    ];

    public function proveedor() {
        return $this->belongsTo(Proveedor::class, 'proveedor_ruc', 'ruc');
    }
}
