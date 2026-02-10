<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditoriaTipoOperacionSeeder extends Seeder
{
    public function run()
    {
        DB::table('auditoria_tipo_operacion')->insert([
            ['nombre' => 'CREATE', 'descripcion' => 'Creación de registro'],
            ['nombre' => 'UPDATE', 'descripcion' => 'Actualización de registro'],
            ['nombre' => 'DELETE', 'descripcion' => 'Eliminación de registro'],
        ]);
    }
}
