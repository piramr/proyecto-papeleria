<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuditService;
use Illuminate\Support\Facades\Auth;

class AuditMiddleware
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check()) {
            try {
                $this->auditService->logResource(Auth::user(), $request, $response);
            }
            catch (\Exception $e) {
            // Fail silently to not impact user experience
            // \Log::error('Audit log failed: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
