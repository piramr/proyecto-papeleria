<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
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
    
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    
        // Define gates to use Role checks in "can" middleware/menus
        // Registrar Observers
        \App\Models\Producto::observe(\App\Observers\ProductoObserver::class);
        \App\Models\Proveedor::observe(\App\Observers\ProveedorObserver::class);
        \App\Models\Categoria::observe(\App\Observers\CategoriaObserver::class);

        // Define gates to use Role checks in "can" middleware/menus
        \Illuminate\Support\Facades\Gate::define('Admin', function ($user) {
            return $user->hasRole('Admin');
        });

        \Illuminate\Support\Facades\Gate::define('Auditor', function ($user) {
            return $user->hasRole('Auditor');
        });

        \Illuminate\Support\Facades\Gate::define('Empleado', function ($user) {
            return $user->hasRole('Empleado');
        });

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Lockout::class,
            \App\Listeners\UserLockoutListener::class,
        );
    }
}
