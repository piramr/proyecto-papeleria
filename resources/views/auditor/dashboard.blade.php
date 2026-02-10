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
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
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
        .loading-spinner { display: inline-block; width: 20px; height: 20px; border: 2px solid #f3f3f3; border-top: 2px solid #007bff; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
@endpush

@section('content')
<div class="container-fluid p-4">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="font-weight-bold mb-0 text-dark">Dashboard</h2>
            <p class="text-muted">Resumen de actividad del sistema - <span id="fecha-actual"></span></p>
        </div>
        <button class="btn btn-outline-secondary btn-sm" onclick="loadDashboardData()">
            <i class="fas fa-sync-alt mr-1"></i> Actualizar
        </button>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-danger shadow-sm">
                <span class="stat-title"><i class="fas fa-exclamation-circle mr-1"></i> Errores (Hoy)</span>
                <div class="stat-value" id="stat-errores">-</div>
                <small id="stat-errores-cambio" class="text-muted">Cargando...</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-warning shadow-sm">
                <span class="stat-title"><i class="fas fa-shield-alt mr-1"></i> Alertas de Login</span>
                <div class="stat-value" id="stat-alertas">-</div>
                <small id="stat-bloqueados" class="text-muted">Cargando...</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-info shadow-sm">
                <span class="stat-title"><i class="fas fa-check-circle mr-1"></i> Operaciones OK</span>
                <div class="stat-value" id="stat-operaciones">-</div>
                <small class="text-muted">Procesadas hoy</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success shadow-sm">
                <span class="stat-title"><i class="fas fa-sign-in-alt mr-1"></i> Logins Exitosos</span>
                <div class="stat-value" id="stat-logins">-</div>
                <small class="text-muted">Accesos hoy</small>
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
                    <i class="fas fa-history mr-2 text-info"></i>Actividad Reciente
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-audit mb-0">
                            <thead>
                                <tr>
                                    <th>Entidad</th>
                                    <th>Acción</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="actividades-tbody">
                                <tr><td colspan="4" class="text-center text-muted py-3"><div class="loading-spinner"></div></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('auditor.auditoria') }}" class="small font-weight-bold text-primary text-decoration-none">
                        Ver Auditoría Completa <i class="fas fa-arrow-right ml-1"></i>
                    </a>
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
        var trendChart = null;
        
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
        
        function renderActividad(a) {
            var accionClass = 'text-info';
            var accionText = a.tipo_operacion ? a.tipo_operacion.toUpperCase() : '-';
            if (accionText === 'CREATE' || accionText === 'CREAR') { accionClass = 'text-success'; accionText = 'CREATE'; }
            else if (accionText === 'UPDATE' || accionText === 'ACTUALIZAR') { accionClass = 'text-warning'; accionText = 'UPDATE'; }
            else if (accionText === 'DELETE' || accionText === 'ELIMINAR') { accionClass = 'text-danger'; accionText = 'DELETE'; }
            
            var estadoClass = a.resultado === 'OK' || a.resultado === 'exitoso' ? 'badge-success' : 'badge-danger';
            var estadoText = a.resultado === 'OK' || a.resultado === 'exitoso' ? 'OK' : 'ERROR';
            
            return `<tr>
                <td><span class="db-code">${a.entidad || '-'}</span></td>
                <td><span class="${accionClass} font-weight-bold">${accionText}</span></td>
                <td>ID: ${a.user_id || '-'}</td>
                <td><span class="badge ${estadoClass}">${estadoText}</span></td>
            </tr>`;
        }
        
        function loadDashboardData() {
            document.getElementById('fecha-actual').textContent = new Date().toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            fetch('/auditor/api/dashboard')
                .then(r => r.json())
                .then(data => {
                    // Actualizar estadísticas
                    document.getElementById('stat-errores').textContent = formatNumber(data.errores_hoy);
                    var cambioHtml = '';
                    if (data.errores_cambio > 0) {
                        cambioHtml = '<i class="fas fa-arrow-up"></i> ' + data.errores_cambio + '% vs ayer';
                        document.getElementById('stat-errores-cambio').className = 'text-danger font-weight-bold';
                    } else if (data.errores_cambio < 0) {
                        cambioHtml = '<i class="fas fa-arrow-down"></i> ' + Math.abs(data.errores_cambio) + '% vs ayer';
                        document.getElementById('stat-errores-cambio').className = 'text-success font-weight-bold';
                    } else {
                        cambioHtml = 'Sin cambios vs ayer';
                        document.getElementById('stat-errores-cambio').className = 'text-muted';
                    }
                    document.getElementById('stat-errores-cambio').innerHTML = cambioHtml;
                    
                    document.getElementById('stat-alertas').textContent = formatNumber(data.alertas_login);
                    document.getElementById('stat-bloqueados').textContent = data.usuarios_bloqueados + ' usuario(s) bloqueado(s)';
                    
                    document.getElementById('stat-operaciones').textContent = formatNumber(data.operaciones_exitosas);
                    document.getElementById('stat-logins').textContent = formatNumber(data.logins_exitosos);
                    
                    // Actualizar tabla de actividades
                    if (data.actividades_recientes && data.actividades_recientes.length > 0) {
                        document.getElementById('actividades-tbody').innerHTML = data.actividades_recientes.map(renderActividad).join('');
                    } else {
                        document.getElementById('actividades-tbody').innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Sin actividad reciente</td></tr>';
                    }
                    
                    // Actualizar gráfico
                    updateChart(data.chart);
                })
                .catch(err => {
                    console.error('Error loading dashboard:', err);
                });
        }
        
        function updateChart(chartData) {
            var canvas = document.getElementById('trendChart');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            
            if (trendChart) {
                trendChart.data.labels = chartData.labels;
                trendChart.data.datasets[0].data = chartData.errores;
                trendChart.data.datasets[1].data = chartData.operaciones;
                trendChart.data.datasets[2].data = chartData.logins;
                trendChart.update();
            } else {
                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Errores',
                            data: chartData.errores,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            borderWidth: 2,
                            pointRadius: 4
                        }, {
                            label: 'Operaciones OK',
                            data: chartData.operaciones,
                            borderColor: '#007bff',
                            backgroundColor: 'transparent',
                            borderWidth: 2,
                            pointRadius: 4
                        }, {
                            label: 'Logins',
                            data: chartData.logins,
                            borderColor: '#28a745',
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
                            yAxes: [{ gridLines: { color: '#f1f3f5' }, ticks: { fontSize: 10, beginAtZero: true } }],
                            xAxes: [{ gridLines: { display: false }, ticks: { fontSize: 10 } }]
                        }
                    }
                });
            }
        }
        
        $(document).ready(function() {
            loadDashboardData();
        });
        
        // Auto-refresh cada 30 segundos
        setInterval(loadDashboardData, 30000);
    </script>
@endpush