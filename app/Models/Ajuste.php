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

    /**
     * Verificar si las notificaciones de stock bajo están habilitadas
     */
    public static function notificacionesStockBajoHabilitadas(): bool
    {
        $ajuste = static::getOrCreate();
        return ($ajuste->stock_alerta_habilitada ?? true) && ($ajuste->notif_stock_bajo ?? true);
    }

    /**
     * Verificar si las notificaciones de ventas están habilitadas
     */
    public static function notificacionesVentaHabilitadas(): bool
    {
        $ajuste = static::getOrCreate();
        return ($ajuste->notif_venta_realizada ?? true);
    }

    /**
     * Verificar si las notificaciones de compras están habilitadas
     */
    public static function notificacionesCompraHabilitadas(): bool
    {
        $ajuste = static::getOrCreate();
        return ($ajuste->notif_compra_recibida ?? true);
    }
}
