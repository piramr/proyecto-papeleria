<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Audit\UserLoginAudit;
use App\Models\Audit\UserLoginAttemptsLog;
use App\Models\Audit\UserRecursosLog;
use App\Models\Audit\DdlAuditoria;
use App\Models\Audit\DmlAuditoria;
use Illuminate\Support\Facades\Request;

class AuditMonitor extends Component
{
    public $activeTab = 'resources';

    // Query string to keep tab selected on refresh
    protected $queryString = ['activeTab' => ['except' => 'resources', 'as' => 'tab']];

    public function mount()
    {
        // Allow initial override from request if not set by query string mechanism
        if (request()->has('tab')) {
            $this->activeTab = request()->get('tab');
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $data = [];

        switch ($this->activeTab) {
            case 'logins':
                $data = UserLoginAudit::with(['user', 'tipoLog'])->latest('login_fecha')->take(50)->get();
                break;
            case 'attempts':
                $data = UserLoginAttemptsLog::with(['tipoLog'])->latest('attempt_fecha')->take(50)->get();
                break;
            case 'ddl':
                $data = DdlAuditoria::with(['user', 'tipoLog'])->latest('ddl_fecha')->take(50)->get();
                break;
            case 'dml':
                $data = DmlAuditoria::with(['user', 'tipoLog'])->latest('timestamp')->take(50)->get();
                break;
            default: // resources
                $data = UserRecursosLog::with(['user', 'tipoLog'])->latest('timestamp')->take(50)->get();
                break;
        }

        return view('livewire.audit-monitor', [
            'logs' => $data
        ]);
    }
}
