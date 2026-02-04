<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AuditService;
use Illuminate\Support\Facades\Session;

class LogLogout
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
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $this->auditService->logLogout(
                $event->user,
                Session::getId()
            );
        }
    }
}
