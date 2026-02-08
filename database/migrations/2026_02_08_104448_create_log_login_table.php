<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogLoginTable extends Migration
{
    public function up()
    {
        Schema::create('log_login', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp');
            $table->string('user_email');
            $table->string('user_id')->nullable(); // Puede ser int o string
            $table->string('host');
            $table->integer('reintento')->default(1);
            $table->string('dispositivo')->nullable();
            $table->string('ubicacion')->nullable();
            $table->unsignedBigInteger('resultado_log_id');
            $table->timestamps();

            $table->foreign('resultado_log_id')->references('id')->on('log_login_resultados');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_login');
    }
}
