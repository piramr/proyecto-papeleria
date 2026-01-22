<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        // Tabla proveedores
        Schema::table('proveedores', function (Blueprint $table) {
            $table->index('ruc', 'idx_proveedores_ruc');
            $table->index('nombre', 'idx_proveedores_nombre');
            $table->index('email', 'idx_proveedores_email');
        });

        // Tabla proveedor_direcciones
        Schema::table('proveedor_direcciones', function (Blueprint $table) {
            $table->index('proveedor_ruc', 'idx_dir_proveedor_ruc');
        });
    }

    public function down(): void {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropIndex('idx_proveedores_ruc');
            $table->dropIndex('idx_proveedores_nombre');
            $table->dropIndex('idx_proveedores_email');
        });

        Schema::table('proveedor_direcciones', function (Blueprint $table) {
            $table->dropIndex('idx_dir_proveedor_ruc');
        });
    }
};
