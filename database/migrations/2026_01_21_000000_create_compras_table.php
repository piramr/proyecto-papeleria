<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->string('numero_compra')->unique();
            $table->dateTime('fecha_compra');
            $table->string('proveedor_ruc', 13);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('iva', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['pendiente', 'recibida', 'cancelada', 'anulada'])->default('pendiente');
            $table->unsignedBigInteger('usuario_id');
            $table->unsignedBigInteger('tipo_pago_id')->nullable();
            $table->dateTime('fecha_recepcion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Claves foráneas
            $table->foreign('proveedor_ruc')->references('ruc')->on('proveedores')->onDelete('restrict');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('tipo_pago_id')->references('id')->on('tipo_pagos')->onDelete('set null');

            // Índices
            $table->index('proveedor_ruc');
            $table->index('usuario_id');
            $table->index('estado');
            $table->index('fecha_compra');
        });

        Schema::create('compra_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compra_id');
            $table->unsignedBigInteger('producto_id');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            // Claves foráneas
            $table->foreign('compra_id')->references('id')->on('compras')->onDelete('cascade');
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('restrict');

            // Índices
            $table->index('compra_id');
            $table->index('producto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('compra_detalles');
        Schema::dropIfExists('compras');
    }
};
