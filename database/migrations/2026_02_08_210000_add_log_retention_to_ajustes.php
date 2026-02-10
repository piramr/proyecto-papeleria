<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            // Retención de logs en días
            $table->integer('log_operacion_retencion')->default(90)->after('notif_compra_recibida');
            $table->boolean('log_operacion_auto_delete')->default(true)->after('log_operacion_retencion');
            
            $table->integer('log_sistema_retencion')->default(30)->after('log_operacion_auto_delete');
            $table->boolean('log_sistema_auto_delete')->default(true)->after('log_sistema_retencion');
            
            $table->integer('log_login_retencion')->default(15)->after('log_sistema_auto_delete');
            $table->boolean('log_login_auto_delete')->default(true)->after('log_login_retencion');
        });
    }

    public function down(): void
    {
        Schema::table('ajustes', function (Blueprint $table) {
            $table->dropColumn([
                'log_operacion_retencion',
                'log_operacion_auto_delete',
                'log_sistema_retencion',
                'log_sistema_auto_delete',
                'log_login_retencion',
                'log_login_auto_delete',
            ]);
        });
    }
};
