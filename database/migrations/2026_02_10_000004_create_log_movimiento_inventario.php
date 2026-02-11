<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('log_movimiento_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos');
            $table->string('tipo_movimiento')->comment('Tipo de movimiento de inventario');
            $table->integer('cantidad');
            $table->string('razon')->nullable()->comment('Razon del movimiento');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('producto_id');
            $table->index('user_id');
            $table->index('created_at');
        });

        DB::statement("ALTER TABLE log_movimiento_inventario ADD CONSTRAINT chk_tipo_movimiento CHECK (tipo_movimiento IN ('entrada','salida'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('log_movimiento_inventario');
    }
};
