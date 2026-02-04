<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AuditService;

class LogFailedLogin
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
    public function handle(Failed $event): void
    {
        $username = $event->credentials['email'] ?? ($event->credentials['username'] ?? 'unknown');

        $this->auditService->logFailedLogin(
            $username,
            request()->ip(),
            'Invalid credentials' // Generic reason, could be more specific
        );
    }
}
