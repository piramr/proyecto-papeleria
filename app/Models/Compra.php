<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model {
    use HasFactory;

    protected $fillable = [
        'numero_compra',
        'fecha_compra',
        'proveedor_ruc',
        'subtotal',
        'iva',
        'total',
        'descripcion',
        'estado',
        'usuario_id',
        'tipo_pago_id',
        'fecha_recepcion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_compra' => 'datetime',
        'fecha_recepcion' => 'datetime',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relación: una compra pertenece a un proveedor
     */
    public function proveedor() {
        return $this->belongsTo(Proveedor::class, 'proveedor_ruc', 'ruc');
    }

    /**
     * Relación: una compra pertenece a un usuario
     */
    public function usuario() {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación: una compra pertenece a un tipo de pago
     */
    public function tipoPago() {
        return $this->belongsTo(TipoPago::class, 'tipo_pago_id');
    }

    /**
     * Relación: una compra tiene muchos detalles
     */
    public function detalles() {
        return $this->hasMany(CompraDetalle::class);
    }

    /**
     * Generar número de compra único
     */
    public static function generarNumeroCompra() {
        $ultimaCompra = self::latest('id')->first();
        $numero = ($ultimaCompra?->id ?? 0) + 1;
        return 'COM-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calcular total con IVA
     */
    public function calcularTotal() {
        $this->subtotal = $this->detalles->sum(function($detalle) {
            return $detalle->cantidad * $detalle->precio_unitario;
        });
        
        $this->iva = $this->detalles->sum(function($detalle) {
            if ($detalle->producto->tiene_iva) {
                return ($detalle->cantidad * $detalle->precio_unitario) * 0.12;
            }
            return 0;
        });
        
        $this->total = $this->subtotal + $this->iva;
        $this->save();
    }
}
