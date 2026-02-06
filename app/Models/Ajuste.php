<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    protected $table = 'ajustes';

    protected $fillable = [
        'iva_porcentaje',
        'empresa_nombre',
        'empresa_ruc',
        'empresa_direccion',
        'empresa_telefono',
        'empresa_email',
        'logo_url',
        'pie_factura',
        'moneda_simbolo',
        'moneda_decimales',
        'prefijo_factura',
        'siguiente_factura',
        'secuencial_digitos',
        'tipo_pago_default_id',
        'stock_minimo',
        'stock_alerta_habilitada',
        'notif_stock_bajo',
        'notif_venta_realizada',
        'notif_compra_recibida',
    ];

    public static function getOrCreate(): self
    {
        return static::firstOrCreate([], ['iva_porcentaje' => 15.00]);
    }

    public static function getIvaPorcentaje(): float
    {
        return (float) static::getOrCreate()->iva_porcentaje;
    }
}
