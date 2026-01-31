<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserLockoutListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Lockout $event): void
    {
        // El evento Lockout se dispara cuando el RateLimiter ha alcanzado el límite (3 intentos)
        // Por lo tanto, si entramos aquí, el usuario YA falló 3 veces.
        // Procedemos a inactivar inmediatamente.

        Log::warning("Lockout event for email: " . $event->request->input('email'));

        $user = User::where('email', $event->request->input('email'))->first();
            
        if ($user && $user->is_active) {
            $user->is_active = false;
            $user->inactivated_at = now();
            $user->save();
            
            Log::alert("User deactivated due to excessive lockouts (3 attempts): " . $user->email);
        }
    }
}
