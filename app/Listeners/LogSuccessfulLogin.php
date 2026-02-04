<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AuditService;
use Illuminate\Support\Facades\Session;

class LogSuccessfulLogin
{
    protected $auditService;

    /**
     * Create the event listener.
     */
    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $this->auditService->logLogin(
            $event->user,
            Session::getId(),
            request()->ip()
        );
    }
}
