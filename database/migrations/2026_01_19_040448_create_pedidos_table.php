<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('estados_pedido', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->string('descripcion')->nullable();

            $table->timestamps();
        });

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion')->nullable();
            $table->timestamp('fecha_hora');
            $table->decimal('total', 10, 2);
            $table->string('proveedor_ruc', 13);
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('estado_pedido_id')->constrained('estados_pedido');
            $table->foreign('proveedor_ruc')->references('ruc')->on('proveedores');

            $table->timestamps();
        });

        Schema::create('pedido_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('total', 10, 2);
            $table->foreignId('producto_id')->constrained('productos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('estados_pedido');
        Schema::dropIfExists('pedido_detalles');
    }
};
