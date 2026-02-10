<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            [\App\Listeners\Auditoria\LoginListener::class, 'handleLogin'],
        ],
        \Illuminate\Auth\Events\Failed::class => [
            [\App\Listeners\Auditoria\LoginListener::class, 'handleFailed'],
        ],
        \Illuminate\Auth\Events\Logout::class => [
            [\App\Listeners\Auditoria\LoginListener::class, 'handleLogout'],
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
