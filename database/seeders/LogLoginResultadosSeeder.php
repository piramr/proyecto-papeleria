<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogLoginResultadosSeeder extends Seeder
{
    public function run()
    {
        DB::table('log_login_resultados')->insert([
            ['nombre' => 'EXITOSO', 'description' => 'Login exitoso'],
            ['nombre' => 'CONTRASEÑA_INVALIDA', 'description' => 'Contraseña incorrecta'],
            ['nombre' => 'CODIGO_ENVIADO', 'description' => 'Código de doble factor enviado'],
            ['nombre' => 'CODIGO_INVALIDO', 'description' => 'Código de doble factor inválido'],
            ['nombre' => 'USUARIO_BLOQUEADO', 'description' => 'Usuario bloqueado por intentos fallidos'],
            ['nombre' => 'INTENTOS_AGOTADOS', 'description' => 'Intentos de login agotados'],
            ['nombre' => 'USUARIO_NO_ENCONTRADO', 'description' => 'Usuario no encontrado en el sistema'],
        ]);
    }
}
