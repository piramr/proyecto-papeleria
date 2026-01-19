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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barras', 100);
            $table->string('nombre');
            $table->string('caracteristicas');
            $table->integer('cantidad_stock', unsigned: true);
            $table->integer('stock_minimo', unsigned: true);
            $table->boolean('tiene_iva');
            $table->string('ubicacion');
            $table->decimal('precio_unitario', 10, 2);
            $table->string('marca', 100);
            $table->boolean('en_oferta');
            $table->decimal('precio_oferta', 10, 2);
            $table->foreignId('categoria_id')->constrained('categorias');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
