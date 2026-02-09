<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Notifications\TwoFactorCode as TwoFactorNotification;
use App\Services\Auditoria\AuditoriaService;

class TwoFactorController extends Controller
{
    public function index() 
    {
        if (session('two_factor_verified')) {
            return redirect()->route('dashboard');
        }

        // Send code if not sent recently (optional optimization)
        $this->sendCodeIfNeeded();

        return view('auth.two-factor-email');
    }

    public function store(Request $request) 
    {
        $request->validate([
            'code' => 'required|numeric'
        ]);

        $user = Auth::user();
        $cachedCode = Cache::get('2fa_code_' . $user->id);

        if ($request->code == $cachedCode) {
            Session::put('two_factor_verified', true);
            Cache::forget('2fa_code_' . $user->id);
            // Log de operación y sistema
            AuditoriaService::registrarOperacion([
                'user_id' => $user->id,
                'tipo_operacion' => 'verificar_2fa',
                'entidad' => 'TwoFactor',
                'recurso_id' => $user->id,
                'resultado' => 'exitoso',
                'mensaje_error' => null,
            ]);
            return redirect()->intended('/dashboard');
        }

        // Log de operación y sistema en caso de error
        AuditoriaService::registrarOperacion([
            'user_id' => $user->id,
            'tipo_operacion' => 'verificar_2fa',
            'entidad' => 'TwoFactor',
            'recurso_id' => $user->id,
            'resultado' => 'fallido',
            'mensaje_error' => 'Código incorrecto o expirado',
        ]);
        
        // Registrar en log_login como CODIGO_INVALIDO
        AuditoriaService::registrarLogLogin([
            'user_email' => $user->email,
            'user_id' => $user->id,
            'resultado' => 'CODIGO_INVALIDO',
            'host' => request()->ip(),
            'dispositivo' => request()->header('User-Agent'),
            'ubicacion' => null,
            'reintento' => 1,
        ]);
        
        AuditoriaService::registrarLogSistema('WARNING', '[SEGURIDAD] Intento de verificación 2FA fallido para: ' . $user->email . '. Código incorrecto o expirado.');
        return back()->withErrors(['code' => 'El código de verificación es incorrecto o ha expirado.']);
    }

    public function resend() 
    {
        $this->sendCode(true); // Force send
        // Log de operación y sistema
        $user = Auth::user();
        AuditoriaService::registrarOperacion([
            'user_id' => $user->id,
            'tipo_operacion' => 'reenviar_2fa',
            'entidad' => 'TwoFactor',
            'recurso_id' => $user->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        return back()->with('status', 'El código de verificación ha sido re-enviado.');
    }

    protected function sendCodeIfNeeded()
    {
        $user = Auth::user();
        if (!Cache::has('2fa_code_' . $user->id)) {
            $this->sendCode();
        }
    }

    protected function sendCode($force = false) 
    {
        $user = Auth::user();
        $code = rand(100000, 999999);
        
        Cache::put('2fa_code_' . $user->id, $code, now()->addMinutes(10));

        try {
            $user->notify(new TwoFactorNotification($code));
            
            // Registrar en log_login que se envió el código
            AuditoriaService::registrarLogLogin([
                'user_email' => $user->email,
                'user_id' => $user->id,
                'resultado' => 'CODIGO_ENVIADO',
                'host' => request()->ip(),
                'dispositivo' => request()->header('User-Agent'),
                'ubicacion' => null,
                'reintento' => 1,
            ]);
        } catch (\Exception $e) {
            // Log de operación y sistema en caso de error
            AuditoriaService::registrarOperacion([
                'user_id' => $user->id,
                'tipo_operacion' => 'enviar_2fa',
                'entidad' => 'TwoFactor',
                'recurso_id' => $user->id,
                'resultado' => 'fallido',
                'mensaje_error' => $e->getMessage(),
            ]);
            AuditoriaService::registrarLogSistema('ERROR', '[CORREO] Error al enviar código 2FA a ' . $user->email . ': ' . $e->getMessage());
        }
    }
}
