<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de tipos de log (centralizada)
        Schema::create('tipos_log', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique(); // WARNING, INFO, ERROR, SUCCESS, DEBUG
            $table->string('nombre', 50);
            $table->string('descripcion', 255)->nullable();
        });

        // Seed basic types immediately
        DB::table('tipos_log')->insert([
            ['codigo' => 'SUCCESS', 'nombre' => 'Exitoso', 'descripcion' => 'Operación completada correctamente'],
            ['codigo' => 'INFO', 'nombre' => 'Información', 'descripcion' => 'Registro informativo rutinario'],
            ['codigo' => 'WARNING', 'nombre' => 'Advertencia', 'descripcion' => 'Evento sospechoso o advertencia'],
            ['codigo' => 'ERROR', 'nombre' => 'Error', 'descripcion' => 'Fallo en la operación'],
            ['codigo' => 'DEBUG', 'nombre' => 'Depuración', 'descripcion' => 'Información técnica para desarrollo'],
        ]);

        // 2. Auditoría de inicio de sesión
        Schema::create('user_login_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relacion con tabla users existente
            $table->string('session_id', 100);
            $table->string('host', 45); // IPv4/IPv6
            $table->dateTime('login_fecha');
            $table->dateTime('logout_fecha')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->foreignId('tipo_log_id')->nullable()->constrained('tipos_log');

            $table->index(['user_id', 'login_fecha'], 'idx_login_user_fecha');
        });

        // 3. Log de intentos de acceso
        Schema::create('user_login_attempts_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Puede ser NULL si el usuario no existe
            $table->string('username_attempted', 100);
            $table->string('host', 45);
            $table->dateTime('attempt_fecha');
            $table->string('result', 50); // SUCCESS, FAILED, LOCKED, EXPIRED
            $table->string('failure_reason', 255)->nullable();
            $table->foreignId('tipo_log_id')->nullable()->constrained('tipos_log');

            $table->index('attempt_fecha', 'idx_attempts_fecha');
        });

        // 4. Log de uso de recursos/endpoints
        Schema::create('user_recursos_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('endpoint', 255);
            $table->string('http_method', 10); // GET, POST, PUT, DELETE
            $table->text('request_body')->nullable();
            $table->integer('response_code')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->dateTime('timestamp');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->foreignId('tipo_log_id')->nullable()->constrained('tipos_log');

            $table->index(['user_id', 'timestamp'], 'idx_recursos_user_fecha');
        });

        // 5. Auditoría DDL (Estructura de base de datos)
        Schema::create('ddl_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('ddl_fecha');
            $table->string('evento', 50); // CREATE, ALTER, DROP, TRUNCATE
            $table->string('objeto_tipo', 50); // TABLE, VIEW, INDEX, PROCEDURE
            $table->string('objeto_nombre', 255);
            $table->string('esquema', 100);
            $table->text('sql_command');
            $table->foreignId('tipo_log_id')->nullable()->constrained('tipos_log');

            $table->index('ddl_fecha', 'idx_ddl_fecha');
        });

        // 6. Auditoría DML (Manipulación de datos)
        Schema::create('dml_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Quien hizo el cambio
            $table->string('accion', 20); // SELECT, INSERT, UPDATE, DELETE
            $table->dateTime('timestamp');
            $table->string('esquema', 100)->nullable(); // A veces no es relevante en log basico
            $table->string('tabla', 255);
            $table->string('columna', 255)->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->string('fila_id', 100)->nullable(); // Primary Key
            $table->string('transaccion_id', 100)->nullable();
            $table->foreignId('tipo_log_id')->nullable()->constrained('tipos_log');

            $table->index(['tabla', 'timestamp'], 'idx_dml_tabla_fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dml_auditoria');
        Schema::dropIfExists('ddl_auditoria');
        Schema::dropIfExists('user_recursos_log');
        Schema::dropIfExists('user_login_attempts_log');
        Schema::dropIfExists('user_login_audit');
        Schema::dropIfExists('tipos_log');
    }
};
