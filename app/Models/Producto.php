<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model {

    protected $fillable = [
        'codigo_barras',
        'nombre',
        'caracteristicas',
        'cantidad_stock',
        'stock_minimo',
        'tiene_iva',
        'ubicacion',
        'precio_unitario',
        'marca',
        'en_oferta',
        'precio_oferta',
        'categoria_id',
    ];

    public function categoria() {
        return $this->belongsTo(Categoria::class);
    }
}
