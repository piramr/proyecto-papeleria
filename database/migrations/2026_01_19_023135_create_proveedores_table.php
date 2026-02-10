<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('proveedores', function (Blueprint $table) {
            // $table->id();
            $table->string('ruc', 13)->primary();
            $table->string('nombre');
            $table->string('email');
            $table->string('telefono_principal', 10)->unique();
            $table->string('telefono_secundario', 10)->unique()->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('proveedor_direcciones', function (Blueprint $table) {
            $table->id();
            $table->string('provincia');
            $table->string('ciudad');
            $table->string('calle');
            $table->string('referencia');
            $table->string('proveedor_ruc', 13);
            $table->foreign('proveedor_ruc')->references('ruc')->on('proveedores');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('proveedor_direcciones');
        Schema::dropIfExists('proveedores');
    }
};
