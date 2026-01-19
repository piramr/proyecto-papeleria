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
        Schema::create('producto_proveedores', function (Blueprint $table) {
            $table->foreignId('producto_id')->constrained('productos');
            $table->string('proveedor_ruc', 13);
            $table->foreign('proveedor_ruc')->references('ruc')->on('proveedores');
            $table->unique(['producto_id', 'proveedor_ruc']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos_proveedores');
    }
};
