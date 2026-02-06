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
        Schema::table('ajustes', function (Blueprint $table) {
            $table->boolean('notif_stock_bajo')->default(true)->after('stock_alerta_habilitada');
            $table->boolean('notif_venta_realizada')->default(true)->after('notif_stock_bajo');
            $table->boolean('notif_compra_recibida')->default(true)->after('notif_venta_realizada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            $table->dropColumn(['notif_stock_bajo', 'notif_venta_realizada', 'notif_compra_recibida']);
        });
    }
};
