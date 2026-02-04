@extends('layouts.app')

@section('title', 'Análisis de Auditoría')

@section('content_header')
    <h1>Análisis Profundo de Datos (DML/DDL)</h1>
@stop

@section('content')
    <!-- Charts Section -->
    <div class="row">
        <!-- Activity by Table Chart -->
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Actividad por Tabla (Top 10)</h3>
                </div>
                <div class="card-body">
                    <canvas id="dmlTableChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <!-- Actions Distribution Chart -->
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-1"></i> Distribución de Acciones</h3>
                </div>
                <div class="card-body">
                    <canvas id="dmlActionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed DML Table -->
    <div class="card card-indigo card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-database mr-1"></i> Registro Detallado de Cambios (DML)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Tabla</th>
                        <th>Fila ID</th>
                        <th>Detalles (JSON)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dmlLogs as $log)
                        <tr>
                            <td>{{ $log->timestamp }}</td>
                            <td>{{ $log->user->nombres ?? 'System' }}</td>
                            <td>
                                <span class="badge {{ $log->accion == 'INSERT' ? 'bg-success' : ($log->accion == 'UPDATE' ? 'bg-warning' : 'bg-danger') }}">
                                    {{ $log->accion }}
                                </span>
                            </td>
                            <td>{{ $log->tabla }}</td>
                            <td>{{ $log->fila_id }}</td>
                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                @if($log->accion == 'UPDATE')
                                    <span class="text-danger">Old: {{ \Illuminate\Support\Str::limit($log->valor_anterior, 50) }}</span><br>
                                    <span class="text-success">New: {{ \Illuminate\Support\Str::limit($log->valor_nuevo, 50) }}</span>
                                @else
                                    {{ \Illuminate\Support\Str::limit($log->valor_nuevo, 100) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $dmlLogs->appends(['ddl_page' => request('ddl_page')])->links() }}
        </div>
    </div>

    <!-- Detailed DDL Table -->
    <div class="card card-purple card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-code mr-1"></i> Registro de Estructura (DDL)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Evento</th>
                        <th>Objeto</th>
                        <th>Nombre</th>
                        <th>SQL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ddlLogs as $log)
                        <tr>
                            <td>{{ $log->ddl_fecha }}</td>
                            <td>{{ $log->user->nombres ?? 'System' }}</td>
                            <td><span class="badge bg-purple">{{ $log->evento }}</span></td>
                            <td>{{ $log->objeto_tipo }}</td>
                            <td>{{ $log->objeto_nombre }}</td>
                            <td><code>{{ \Illuminate\Support\Str::limit($log->sql_command, 80) }}</code></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
             {{ $ddlLogs->appends(['dml_page' => request('dml_page')])->links() }}
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // DML Table Chart
            const tableCtx = document.getElementById('dmlTableChart').getContext('2d');
            new Chart(tableCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dmlChartData->pluck('tabla')) !!},
                    datasets: [{
                        label: 'Número de Cambios',
                        data: {!! json_encode($dmlChartData->pluck('total')) !!},
                        backgroundColor: 'rgba(60, 141, 188, 0.8)',
                        borderColor: 'rgba(60, 141, 188, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true } },
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // DML Action Chart
            const actionCtx = document.getElementById('dmlActionChart').getContext('2d');
            const actionData = {!! json_encode($dmlActionData) !!};
            
            new Chart(actionCtx, {
                type: 'doughnut',
                data: {
                    labels: actionData.map(d => d.accion),
                    datasets: [{
                        data: actionData.map(d => d.total),
                        backgroundColor: [
                            '#28a745', // Success/Insert
                            '#ffc107', // Warning/Update
                            '#dc3545', // Danger/Delete
                            '#17a2b8'  // Info/Restore
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                         legend: { position: 'bottom' }
                    }
                }
            });
        });
    </script>
@stop
