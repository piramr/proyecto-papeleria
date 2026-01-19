<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\TextUI\XmlConfiguration\SchemaDetector;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('tipos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30);
            $table->string('descripcion')->nullable();

            $table->timestamps();
        });

        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->timestamp('fecha_hora');
            $table->string('descripcion')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('cliente_cedula', 10);
            $table->foreign('cliente_cedula')->references('cedula')->on('clientes');
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('tipo_pago_id')->constrained('tipos_pago');

            $table->timestamps();
        });

        Schema::create('factura_detalles', function (Blueprint $table) {
            $table->decimal('precio_unitario');
            $table->unsignedInteger('cantidad');
            $table->decimal('total', 10, 2);
            $table->foreignId('factura_id')->constrained('facturas');
            $table->foreignId('producto_id')->constrained('productos');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('factura_detalles');
        Schema::dropIfExists('facturas');
        Schema::dropIfExists('tipos_pago');
    }
};
