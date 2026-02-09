<?php
namespace App\Listeners\Auditoria;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use App\Services\Auditoria\AuditoriaService;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;

class LoginListener
{
    public function handleLogin(Login $event)
    {
        $email = $event->user->email;
        $lockKey = 'login_logged_' . $email . '_' . date('YmdHi');

        // Evitar duplicados: solo registrar una vez por minuto por email
        if (Cache::has($lockKey)) {
            return;
        }
        Cache::put($lockKey, true, 60);

        $reintentos = Cache::get('login_attempts_' . $email, 0);

        AuditoriaService::registrarLogLogin([
            'user_email' => $email,
            'user_id' => $event->user->id,
            'resultado' => 'EXITOSO',
            'host' => Request::ip(),
            'dispositivo' => Request::header('User-Agent'),
            'ubicacion' => null,
            'reintento' => max(1, $reintentos + 1),
        ]);

        // Limpiar contador de intentos después de login exitoso
        Cache::forget('login_attempts_' . $email);
    }

    public function handleFailed(Failed $event)
    {
        $email = $event->credentials['email'] ?? 'unknown';

        // Evitar duplicados: solo registrar una vez por segundo por email
        $dedupKey = 'login_failed_dedup_' . $email . '_' . date('YmdHis');
        if (Cache::has($dedupKey)) {
            return;
        }
        Cache::put($dedupKey, true, 2);

        // Incrementar contador de intentos en cache
        $reintentos = Cache::increment('login_attempts_' . $email);

        // Obtener el usuario si existe
        $user = \App\Models\User::where('email', $email)->first();

        // Determinar resultado según el caso
        if (!$user) {
            // Usuario no existe en la base de datos
            $resultado = 'USUARIO_NO_ENCONTRADO';
        } elseif (!$user->is_active) {
            // Usuario está bloqueado
            $resultado = 'USUARIO_BLOQUEADO';
        } elseif ($reintentos >= 3) {
            // Alcanzó el límite de intentos
            $resultado = 'INTENTOS_AGOTADOS';
        } else {
            // Contraseña incorrecta pero aún tiene intentos
            $resultado = 'CONTRASEÑA_INVALIDA';
        }

        AuditoriaService::registrarLogLogin([
            'user_email' => $email,
            'user_id' => $user->id ?? null,
            'resultado' => $resultado,
            'host' => Request::ip(),
            'dispositivo' => Request::header('User-Agent'),
            'ubicacion' => null,
            'reintento' => $reintentos,
        ]);
    }

    public function handleLogout(Logout $event)
    {
        $email = $event->user->email;
        
        // Evitar duplicados: solo registrar una vez por segundo
        $dedupKey = 'logout_logged_' . $email . '_' . date('YmdHis');
        if (Cache::has($dedupKey)) {
            return;
        }
        Cache::put($dedupKey, true, 2);
        
        AuditoriaService::registrarLogSistema('INFO', '[SESIÓN] Usuario ' . $email . ' cerró sesión en el sistema.');
    }
}
