<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogOperacionTable extends Migration
{
    public function up()
    {
        Schema::create('log_operacion', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp');
            $table->unsignedBigInteger('user_id');
            $table->string('session_id');
            $table->string('ip_address', 45)->nullable();
            $table->string('tipo_operacion');
            $table->string('entidad');
            $table->string('recurso_id')->nullable();
            $table->string('recurso_padre_id')->nullable();
            $table->string('resultado'); // OK / ERROR
            $table->string('codigo_error')->nullable();
            $table->string('mensaje_error', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_operacion');
    }
}
