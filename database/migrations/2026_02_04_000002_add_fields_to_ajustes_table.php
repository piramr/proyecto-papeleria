<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            $table->string('empresa_nombre')->nullable()->after('iva_porcentaje');
            $table->string('empresa_ruc')->nullable()->after('empresa_nombre');
            $table->string('empresa_direccion')->nullable()->after('empresa_ruc');
            $table->string('empresa_telefono')->nullable()->after('empresa_direccion');
            $table->string('empresa_email')->nullable()->after('empresa_telefono');
            $table->string('logo_url')->nullable()->after('empresa_email');
            $table->text('pie_factura')->nullable()->after('logo_url');
            $table->string('moneda_simbolo', 10)->default('$')->after('pie_factura');
            $table->unsignedTinyInteger('moneda_decimales')->default(2)->after('moneda_simbolo');
            $table->string('prefijo_factura', 20)->nullable()->after('moneda_decimales');
            $table->unsignedBigInteger('siguiente_factura')->nullable()->after('prefijo_factura');
            $table->unsignedTinyInteger('secuencial_digitos')->default(9)->after('siguiente_factura');
            $table->unsignedBigInteger('tipo_pago_default_id')->nullable()->after('secuencial_digitos');
            $table->unsignedInteger('stock_minimo')->default(5)->after('tipo_pago_default_id');
            $table->boolean('stock_alerta_habilitada')->default(true)->after('stock_minimo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
