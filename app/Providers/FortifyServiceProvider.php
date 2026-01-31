<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)->first();
            $key = 'login_lock|'.$request->input('email');

            // 1. Verificación de Desbloqueo Automático (10 minutos)
            if ($user && ! $user->is_active && $user->inactivated_at) {
                // Si han pasado 10 minutos (o más) desde el bloqueo
                if ($user->inactivated_at->addMinutes(10)->isPast()) {
                    $user->forceFill([
                        'is_active' => true,
                        'inactivated_at' => null,
                    ])->save();

                    RateLimiter::clear($key);
                    // El usuario continúa al siguiente chequeo ya como activo
                }
            }

            // 2. Validar si el usuario existe y está bloqueado (Persistentemente)
            // Nota: Si se acabó de desbloquear arriba, is_active ya es true, así que no entra aquí.
            if ($user && RateLimiter::tooManyAttempts($key, 3)) {
                 // Asegurarnos que esté marcado como bloqueado en DB si no lo está (sincronización)
                 if ($user->is_active) {
                     $user->forceFill([
                        'is_active' => false,
                        'inactivated_at' => now(),
                    ])->save();
                 }
                
                 throw \Illuminate\Validation\ValidationException::withMessages([
                    Fortify::username() => ['Se ha bloqueado la cuenta por exceder el número de intentos, espere 10 min o contáctese con el administrador.'],
                ]);
            }

            // Chequeo explícito de DB por si acaso (aunque el RateLimiter debería coincidir)
            if ($user && ! $user->is_active) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    Fortify::username() => ['Se ha bloqueado la cuenta por exceder el número de intentos, espere 10 min o contáctese con el administrador.'],
                ]);
            }

            // 3. Validar credenciales
            if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
                RateLimiter::clear($key);
                // Resetear campos de seguridad por si acaso quedó algo sucio
                if ($user->inactivated_at) {
                     $user->forceFill(['inactivated_at' => null])->save();
                }
                return $user;
            }

            // 4. Manejo de intentos fallidos
            RateLimiter::hit($key);
            
            // Verificar si ACABA de alcanzar el límite (hit lo incrementó a 3)
            if (RateLimiter::attempts($key) >= 3) {
                 if ($user) {
                    $user->forceFill([
                        'is_active' => false,
                        'inactivated_at' => now(),
                    ])->save();
                 }

                 throw \Illuminate\Validation\ValidationException::withMessages([
                    Fortify::username() => ['Se ha bloqueado la cuenta por exceder el número de intentos, espere 10 min o contáctese con el administrador.'],
                ]);
            }

            return null; // Fortify lanzará el error genérico de credenciales
        });
    }
}
