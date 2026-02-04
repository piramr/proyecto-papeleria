<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit\DmlAuditoria;
use App\Models\Audit\DdlAuditoria;
use Illuminate\Support\Facades\DB;

class AuditAnalysisController extends Controller
{
    public function index()
    {
        // Chart Data: DML Activity by Table
        $dmlChartData = DmlAuditoria::select('tabla', DB::raw('count(*) as total'))
            ->groupBy('tabla')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Chart Data: DML Activity by Action (Insert/Update/Delete)
        $dmlActionData = DmlAuditoria::select('accion', DB::raw('count(*) as total'))
            ->groupBy('accion')
            ->get();

        // Chart Data: DDL Activity by Event Type
        $ddlChartData = DdlAuditoria::select('evento', DB::raw('count(*) as total'))
            ->groupBy('evento')
            ->get();

        // Detailed Logs (Paginated)
        $dmlLogs = DmlAuditoria::with('user')->latest('timestamp')->paginate(15, ['*'], 'dml_page');
        $ddlLogs = DdlAuditoria::with('user')->latest('ddl_fecha')->paginate(15, ['*'], 'ddl_page');

        return view('audit.analysis', compact(
            'dmlChartData',
            'dmlActionData',
            'ddlChartData',
            'dmlLogs',
            'ddlLogs'
        ));
    }
}
