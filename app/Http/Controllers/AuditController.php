<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit\UserLoginAudit;
use App\Models\Audit\UserLoginAttemptsLog;
use App\Models\Audit\UserRecursosLog;
use App\Models\Audit\DdlAuditoria;
use App\Models\Audit\DmlAuditoria;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Request; -- Conflict with Http\Request

class AuditController extends Controller
{
    public function index()
    {
        // For the "Console" view (aggregated), we might want to just show the most recent resource logs or attempts.
        // Or separate tabs. Let's send separate collections for tabs.

        // Data fetching moved to Livewire component
        $activeTab = request()->get('tab', 'resources');
        return view('audit.index', compact('activeTab'));
    }

    public function fetchRecent(Request $request)
    {
        // AJAX endpoint for real-time polling
        $type = $request->input('type', 'resources');

        switch ($type) {
            case 'logins':
                $data = UserLoginAudit::with(['user', 'tipoLog'])->latest('login_fecha')->take(20)->get();
                $view = 'audit.partials.logins';
                break;
            case 'attempts':
                $data = UserLoginAttemptsLog::with(['tipoLog'])->latest('attempt_fecha')->take(20)->get();
                $view = 'audit.partials.attempts';
                break;
            case 'ddl':
                $data = DdlAuditoria::with(['user', 'tipoLog'])->latest('ddl_fecha')->take(20)->get();
                $view = 'audit.partials.ddl';
                break;
            case 'dml':
                $data = DmlAuditoria::with(['user', 'tipoLog'])->latest('timestamp')->take(20)->get();
                $view = 'audit.partials.dml';
                break;
            default: // resources
                $data = UserRecursosLog::with(['user', 'tipoLog'])->latest('timestamp')->take(20)->get();
                $view = 'audit.partials.resources';
                break;
        }

        return view($view, ['logs' => $data]);
    }
}
