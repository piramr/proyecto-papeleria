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
        // Primero, eliminar las restricciones de facturas si existen
        if (Schema::hasTable('facturas')) {
            Schema::table('facturas', function (Blueprint $table) {
                try {
                    $table->dropForeign('facturas_tipo_pago_id_foreign');
                } catch (\Exception $e) {
                    // Ignorar si no existe
                }
            });
        }
        
        Schema::dropIfExists('tipos_pago');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
