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
        Schema::create('clientes', function (Blueprint $table) {
            // $table->id();
            $table->string('cedula', 10)->primary();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('telefono', 15)->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('genero', 20)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('direccion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
