<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogNivelSeeder extends Seeder
{
    public function run()
    {
        DB::table('log_nivel')->insert([
            ['nombre' => 'INFO', 'descripcion' => 'Información general'],
            ['nombre' => 'WARNING', 'descripcion' => 'Advertencia'],
            ['nombre' => 'ERROR', 'descripcion' => 'Error de sistema'],
            ['nombre' => 'FATAL', 'descripcion' => 'Error crítico'],
        ]);
    }
}
