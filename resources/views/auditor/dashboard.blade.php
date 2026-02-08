@extends('layouts.app')

@section('title', 'Dashboard')

@push('styles')
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f9; font-family: 'Segoe UI', sans-serif; }
        .card, .btn, .badge { border-radius: 0 !important; }
        .stat-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-left: 4px solid #343a40;
            padding: 20px;
        }
        .stat-danger { border-left-color: #dc3545; }
        .stat-warning { border-left-color: #ffc107; }
        .stat-success { border-left-color: #28a745; }
        .stat-info { border-left-color: #17a2b8; }
        .stat-title { font-size: 0.7rem; font-weight: 800; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; }
        .stat-value { font-size: 1.8rem; font-weight: 700; color: #212529; }
        .table-audit thead th {
            background-color: #f8f9fa;
            font-size: 0.75rem;
            text-transform: uppercase;
            border-bottom: 2px solid #dee2e6;
        }
        .table-audit td { font-size: 0.85rem; vertical-align: middle; }
        .db-code { font-family: 'Consolas', monospace; background: #f1f3f5; padding: 2px 5px; color: #e83e8c; font-size: 0.8rem; }
    </style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="font-weight-bold mb-0 text-dark">Dashboard</h2>
            <p class="text-muted">Resumen de cumplimiento y seguridad del sistema</p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-danger shadow-sm">
                <span class="stat-title">Errores Críticos (24h)</span>
                <div class="stat-value">12</div>
                <small class="text-danger font-weight-bold"><i class="fas fa-arrow-up"></i> 8% vs ayer</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-warning shadow-sm">
                <span class="stat-title">Alertas de Login</span>
                <div class="stat-value">45</div>
                <small class="text-muted">3 usuarios bloqueados</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-info shadow-sm">
                <span class="stat-title">Operaciones Totales</span>
                <div class="stat-value">1,240</div>
                <small class="text-muted">Procesadas con éxito</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success shadow-sm">
                <span class="stat-title">Uptime Auditor</span>
                <div class="stat-value">99.9%</div>
                <small class="text-success font-weight-bold">Motor estable</small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card border shadow-sm">
                <div class="card-header bg-white font-weight-bold small text-uppercase py-3">
                    <i class="fas fa-chart-line mr-2 text-primary"></i>Tendencia de Eventos (7 Días)
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5 mb-4">
            <div class="card border shadow-sm">
                <div class="card-header bg-white font-weight-bold small text-uppercase py-3">
                    <i class="fas fa-exclamation-triangle mr-2 text-warning"></i>Eventos de Alto Riesgo
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-audit mb-0">
                            <thead>
                                <tr>
                                    <th>Recurso</th>
                                    <th>Acción</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="db-code">PRECIOS_PINTURA</span></td>
                                    <td><span class="text-danger font-weight-bold">UPDATE</span></td>
                                    <td>admin_01</td>
                                </tr>
                                <tr>
                                    <td><span class="db-code">CONFIG_IVA</span></td>
                                    <td><span class="text-danger font-weight-bold">UPDATE</span></td>
                                    <td>j_perez</td>
                                </tr>
                                <tr>
                                    <td><span class="db-code">USER_PERMISSIONS</span></td>
                                    <td><span class="text-warning font-weight-bold">GRANT</span></td>
                                    <td>root_sys</td>
                                </tr>
                                <tr>
                                    <td><span class="db-code">STOCK_PINTURAS</span></td>
                                    <td><span class="text-info font-weight-bold">REORDER</span></td>
                                    <td>operador_3</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="#" class="small font-weight-bold text-primary text-decoration-none">Ver Bitácora Completa</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <script>
        $(document).ready(function() {
            var canvas = document.getElementById('trendChart');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'],
                    datasets: [{
                        label: 'Errores',
                        data: [12, 19, 3, 5, 2, 3, 15],
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 2,
                        pointRadius: 4
                    }, {
                        label: 'Operaciones OK',
                        data: [150, 230, 180, 200, 190, 100, 210],
                        borderColor: '#007bff',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { position: 'bottom', labels: { boxWidth: 12, fontSize: 11 } },
                    scales: {
                        yAxes: [{ gridLines: { color: '#f1f3f5' }, ticks: { fontSize: 10 } }],
                        xAxes: [{ gridLines: { display: false }, ticks: { fontSize: 10 } }]
                    }
                }
            });
        });
    </script>
@endpush