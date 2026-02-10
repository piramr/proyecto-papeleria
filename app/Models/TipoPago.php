<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model {
    use HasFactory;

    protected $table = 'tipo_pagos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Relación: un tipo de pago tiene muchas facturas
     */
    public function facturas() {
        return $this->hasMany(Factura::class, 'tipo_pago_id');
    }
}
