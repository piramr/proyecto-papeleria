<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // users.create
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30)->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->primary(['rol_id', 'permiso_id']);

        });

        Schema::create('usuario_roles', function (Blueprint $table) {
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['usuario_id', 'rol_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('usuario_roles');
        Schema::dropIfExists('rol_permisos');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permisos');
    }
};
