<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Audit\TipoLog;

class AuditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Types Exist
        $types = [
            ['codigo' => 'SUCCESS', 'nombre' => 'Exitoso', 'descripcion' => 'Operación completada correctamente'],
            ['codigo' => 'INFO', 'nombre' => 'Información', 'descripcion' => 'Registro informativo rutinario'],
            ['codigo' => 'WARNING', 'nombre' => 'Advertencia', 'descripcion' => 'Evento sospechoso o advertencia'],
            ['codigo' => 'ERROR', 'nombre' => 'Error', 'descripcion' => 'Fallo en la operación'],
            ['codigo' => 'DEBUG', 'nombre' => 'Depuración', 'descripcion' => 'Información técnica para desarrollo'],
        ];

        foreach ($types as $type) {
            TipoLog::firstOrCreate(['codigo' => $type['codigo']], $type);
        }

        // Only create dummy logs if no logs exist yet
        if (\App\Models\Audit\UserRecursosLog::count() == 0) {

            // Create a dummy user for logs if none exists or use first
            $userId = \App\Models\User::first()->id ?? 1;

            // Seed dummy Resource Logs
            \App\Models\Audit\UserRecursosLog::create([
                'user_id' => $userId,
                'endpoint' => 'admin/dashboard',
                'http_method' => 'GET',
                'response_code' => 200,
                'response_time_ms' => 120,
                'timestamp' => now()->subMinutes(5),
                'ip_address' => '127.0.0.1',
                'tipo_log_id' => TipoLog::where('codigo', 'INFO')->value('id'),
            ]);

            // Seed dummy Login
            \App\Models\Audit\UserLoginAudit::create([
                'user_id' => $userId,
                'session_id' => \Illuminate\Support\Str::random(40),
                'host' => '127.0.0.1',
                'login_fecha' => now()->subHours(2),
                'tipo_log_id' => TipoLog::where('codigo', 'SUCCESS')->value('id'),
            ]);

            // Seed dummy DML (Price Change)
            \App\Models\Audit\DmlAuditoria::create([
                'user_id' => $userId,
                'accion' => 'UPDATE',
                'timestamp' => now()->subMinutes(10),
                'tabla' => 'productos',
                'fila_id' => '1',
                'valor_anterior' => json_encode(['precio' => 15.50]),
                'valor_nuevo' => json_encode(['precio' => 16.00]),
                'tipo_log_id' => TipoLog::where('codigo', 'INFO')->value('id'),
            ]);
        }
    }
}
