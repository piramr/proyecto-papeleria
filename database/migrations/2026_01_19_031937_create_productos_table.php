<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barras', 100);
            $table->string('nombre');
            $table->string('caracteristicas')->nullable();
            $table->integer('cantidad_stock', unsigned: true);
            $table->integer('stock_minimo', unsigned: true);
            $table->integer('stock_maximo', unsigned: true);
            $table->boolean('tiene_iva');
            $table->string('ubicacion')->nullable();
            $table->decimal('precio_unitario', 10, 2);
            $table->string('marca', 100);
            $table->boolean('en_oferta')->nullable();
            $table->decimal('precio_oferta', 10, 2)->nullable();
            $table->foreignId('categoria_id')->constrained('categorias');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('productos');
    }
};
