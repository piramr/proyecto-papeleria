<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogNivelTable extends Migration
{
    public function up()
    {
        Schema::create('log_nivel', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // INFO, WARNING, ERROR, FATAL
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_nivel');
    }
}
