<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogSistemaTable extends Migration
{
    public function up()
    {
        Schema::create('log_sistema', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp');
            $table->unsignedBigInteger('nivel_log_id');
            $table->string('mensaje');
            $table->timestamps();

            $table->foreign('nivel_log_id')->references('id')->on('log_nivel');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_sistema');
    }
}
