<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditoriaDatosTable extends Migration
{
    public function up()
    {
        Schema::create('auditoria_datos', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp');
            $table->unsignedBigInteger('user_id');
            $table->string('session_id');
            $table->string('tipo_operacion'); // CREATE, UPDATE, DELETE
            $table->string('entidad');
            $table->string('recurso_id');
            $table->string('recurso_padre_id')->nullable();
            $table->string('campo');
            $table->text('valor_original')->nullable();
            $table->text('valor_nuevo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auditoria_datos');
    }
}
