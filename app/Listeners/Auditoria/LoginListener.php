<?php
namespace App\Listeners\Auditoria;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Logout;
use App\Services\Auditoria\AuditoriaService;
use Illuminate\Support\Facades\Request;

class LoginListener
{
    public function handleLogin(Login $event)
    {
        AuditoriaService::registrarLogLogin([
            'user_email' => $event->user->email,
            'user_id' => $event->user->id,
            'resultado' => 'EXITOSO',
            'host' => Request::ip(),
            'dispositivo' => Request::header('User-Agent'),
            'ubicacion' => null,
            'reintento' => 1
        ]);
    }

    public function handleFailed(Failed $event)
    {
        AuditoriaService::registrarLogLogin([
            'user_email' => $event->credentials['email'] ?? 'unknown',
            'user_id' => null,
            'resultado' => 'CONTRASEÑA_INVALIDA',
            'host' => Request::ip(),
            'dispositivo' => Request::header('User-Agent'),
            'ubicacion' => null,
            'reintento' => 1
        ]);
    }

    public function handleLogout(Logout $event)
    {
        AuditoriaService::registrarLogSistema('INFO', 'Usuario '.$event->user->email.' cerró sesión.');
    }
}
