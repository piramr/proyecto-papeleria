<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model {

    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'codigo_barras',
        'nombre',
        'caracteristicas',
        'cantidad_stock',
        'stock_minimo',
        'stock_maximo',
        'tiene_iva',
        'ubicacion',
        'precio_unitario',
        'marca',
        'en_oferta',
        'precio_oferta',
        'categoria_id',
    ];

    // Alias para compatibilidad
    public function getStockAttribute() {
        return $this->cantidad_stock;
    }

    public function getPrecioAttribute() {
        return $this->precio_unitario;
    }

    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }

    public function proveedores() {
        return $this->belongsToMany(Proveedor::class, 'producto_proveedores', 'producto_id', 'proveedor_ruc', 'id', 'ruc')
            ->withPivot('precio_costo');
    }
}
