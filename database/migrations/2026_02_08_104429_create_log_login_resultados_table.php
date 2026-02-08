<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogLoginResultadosTable extends Migration
{
    public function up()
    {
        Schema::create('log_login_resultados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // EXITOSO, CONTRASEÑA_INVALIDA, CODIGO_ENVIADO, CODIGO_INVALIDO, USUARIO_BLOQUEADO, INTENTOS_AGOTADOS, USUARIO_NO_ENCONTRADO
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_login_resultados');
    }
}
