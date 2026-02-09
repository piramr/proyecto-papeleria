@extends('layouts.app')

@section('title', 'Auditoría')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bs-body-bg: #f4f7f6; }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); position: relative; }
        .table thead th { background-color: #2c3e50; color: white; font-size: 0.75rem; text-transform: uppercase; border: none; }
        .chart-controls {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
        }
        .btn-check:checked + .btn-light { background-color: #0d6efd; color: white; border-color: #0d6efd; }
        .badge-op { width: 75px; font-weight: 600; }
        .page-link { border: none; color: #2c3e50; margin: 0 2px; border-radius: 5px !important; }
        .page-item.active .page-link { background-color: #0d6efd; color: white; }
    </style>
@endpush

@section('content')
<div class="container-fluid pt-2">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold mb-0 text-dark">Actividad del Sistema</h3>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2 justify-content-end">
                <select class="form-select form-select-sm w-auto shadow-sm border-0" id="filter-recurso">
                    <option value="Todos">Recurso: Todos</option>
                    <option value="Producto">Productos</option>
                    <option value="Categoria">Categorías</option>
                    <option value="Proveedor">Proveedores</option>
                    <option value="Cliente">Clientes</option>
                    <option value="Compra">Compras</option>
                    <option value="Venta">Ventas</option>
                    <option value="User">Usuarios</option>
                </select>
                <select class="form-select form-select-sm w-auto shadow-sm border-0" id="filter-operacion">
                    <option value="Todas">Op: Todas</option>
                    <option value="CREATE">CREATE</option>
                    <option value="UPDATE">UPDATE</option>
                    <option value="DELETE">DELETE</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="chart-controls shadow-sm rounded border overflow-hidden">
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="vMode" id="lMode" value="line" checked>
                <label class="btn btn-light btn-sm px-3 mb-0" for="lMode"><i class="fas fa-chart-line"></i></label>
                <input type="radio" class="btn-check" name="vMode" id="bMode" value="bar">
                <label class="btn btn-light btn-sm px-3 mb-0" for="bMode"><i class="fas fa-chart-bar"></i></label>
            </div>
        </div>
        <div class="card-body pt-5">
            <div style="height: 300px;">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-white py-3 border-0">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small text-nowrap">Mostrar</span>
                        <select class="form-select form-select-sm w-auto" id="per-page-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-muted small">registros</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th class="ps-3 border-0">ID</th>
                            <th class="border-0">Timestamp</th>
                            <th class="border-0">Usuario / Sesión</th>
                            <th class="border-0">Operación</th>
                            <th class="border-0">Entidad</th>
                            <th class="border-0">Recurso_ID</th>
                            <th class="border-0">Padre_ID</th>
                            <th class="border-0">Campo</th>
                            <th class="border-0">Anterior</th>
                            <th class="border-0">Nuevo</th>
                        </tr>
                    </thead>
                    <tbody id="auditoria-tbody">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3 border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted small mb-0" id="pagination-info">Mostrando 0 a 0 de 0 registros</p>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Navegación de registros">
                        <ul class="pagination pagination-sm justify-content-end mb-0" id="pagination-container">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- AUDITORÍA CON PAGINACIÓN ---
        var currentPage = 1;
        var perPage = 10;
        var autoRefresh = true;
        
        function renderAuditoriaRow(a) {
            var sessionShort = a.session_id ? a.session_id.substring(0, 8) + '...' : '-';
            var sessionFull = a.session_id || '-';
            var opClass = 'bg-secondary';
            if (a.tipo_operacion === 'CREATE') opClass = 'bg-success';
            else if (a.tipo_operacion === 'UPDATE') opClass = 'bg-warning text-dark';
            else if (a.tipo_operacion === 'DELETE') opClass = 'bg-danger';
            
            return `<tr>
                <td class='ps-3 text-muted'>${a.id || ''}</td>
                <td>${a.timestamp || ''}</td>
                <td>ID: ${a.user_id || '-'}<br><small class='text-muted' title='${sessionFull}' style='cursor: pointer;'>S: ${sessionShort}</small></td>
                <td><span class='badge badge-op ${opClass}'>${a.tipo_operacion || '-'}</span></td>
                <td>${a.entidad || ''}</td>
                <td><code>${a.recurso_id || ''}</code></td>
                <td>${a.recurso_padre_id || '-'}</td>
                <td>${a.campo || '-'}</td>
                <td class='text-muted'>${a.valor_original || 'NULL'}</td>
                <td class='fw-bold text-success'>${a.valor_nuevo || ''}</td>
            </tr>`;
        }
        
        function renderPagination(data) {
            var container = document.getElementById('pagination-container');
            var info = document.getElementById('pagination-info');
            
            // Actualizar info
            info.textContent = `Mostrando ${data.from || 0} a ${data.to || 0} de ${data.total} registros`;
            
            // Generar paginación
            var html = '';
            
            // Botón anterior
            html += `<li class="page-item ${data.current_page <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${data.current_page - 1}"><i class="fas fa-angle-left"></i></a>
            </li>`;
            
            // Páginas
            var startPage = Math.max(1, data.current_page - 2);
            var endPage = Math.min(data.last_page, data.current_page + 2);
            
            if (startPage > 1) {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
                if (startPage > 2) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }
            
            for (var i = startPage; i <= endPage; i++) {
                html += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            }
            
            if (endPage < data.last_page) {
                if (endPage < data.last_page - 1) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${data.last_page}">${data.last_page}</a></li>`;
            }
            
            // Botón siguiente
            html += `<li class="page-item ${data.current_page >= data.last_page ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${data.current_page + 1}"><i class="fas fa-angle-right"></i></a>
            </li>`;
            
            container.innerHTML = html;
            
            // Event listeners para paginación
            container.querySelectorAll('a[data-page]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var page = parseInt(this.getAttribute('data-page'));
                    if (page >= 1 && page <= data.last_page) {
                        currentPage = page;
                        fetchAuditoria();
                    }
                });
            });
        }
        
        function fetchAuditoria() {
            var recurso = document.getElementById('filter-recurso').value;
            var operacion = document.getElementById('filter-operacion').value;
            
            var url = '/auditor/api/auditoria?page=' + currentPage + '&per_page=' + perPage;
            if (recurso !== 'Todos') url += '&recurso=' + encodeURIComponent(recurso);
            if (operacion !== 'Todas') url += '&operacion=' + encodeURIComponent(operacion);
            
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('auditoria-tbody').innerHTML = data.auditoria.map(renderAuditoriaRow).join('');
                    renderPagination(data);
                });
        }
        
        // Event listeners para filtros y paginación
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('per-page-select').addEventListener('change', function() {
                perPage = parseInt(this.value);
                currentPage = 1;
                fetchAuditoria();
            });
            
            document.getElementById('filter-recurso').addEventListener('change', function() {
                currentPage = 1;
                fetchAuditoria();
            });
            
            document.getElementById('filter-operacion').addEventListener('change', function() {
                currentPage = 1;
                fetchAuditoria();
            });
            
            fetchAuditoria();
        });
        
        // Auto-refresh solo en la primera página
        setInterval(function() {
            if (autoRefresh && currentPage === 1) {
                fetchAuditoria();
            }
        }, 5000);

        // --- GRÁFICO CON DATOS REALES ---
        var chartData = { labels: [], create: [], update: [], delete: [] };
        var myChart = null;
        
        function fetchChartData() {
            return fetch('/auditor/api/auditoria/chart?dias=7')
                .then(r => r.json())
                .then(data => {
                    chartData = data;
                    return data;
                })
                .catch(err => {
                    console.error('Error fetching chart data:', err);
                    return chartData;
                });
        }
        
        function initAuditoriaChart() {
            const canvas = document.getElementById('mainChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            const theme = { create: '#198754', update: '#ffc107', delete: '#dc3545' };
            
            function getGradient(color) {
                const g = ctx.createLinearGradient(0, 0, 0, 300);
                g.addColorStop(0, color + '66');
                g.addColorStop(1, 'rgba(255,255,255,0)');
                return g;
            }
            
            function render(type) {
                if (myChart) myChart.destroy();
                myChart = new Chart(ctx, {
                    type: type,
                    data: {
                        labels: chartData.labels,
                        datasets: [
                            {
                                label: 'CREATE',
                                data: chartData.create,
                                borderColor: theme.create,
                                backgroundColor: type === 'line' ? getGradient(theme.create) : theme.create,
                                fill: type === 'line',
                                tension: 0.4
                            },
                            {
                                label: 'UPDATE',
                                data: chartData.update,
                                borderColor: theme.update,
                                backgroundColor: type === 'line' ? getGradient(theme.update) : theme.update,
                                fill: type === 'line',
                                tension: 0.4
                            },
                            {
                                label: 'DELETE',
                                data: chartData.delete,
                                borderColor: theme.delete,
                                backgroundColor: type === 'line' ? getGradient(theme.delete) : theme.delete,
                                fill: type === 'line',
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } },
                        scales: {
                            x: { grid: { display: false } },
                            y: { beginAtZero: true, grid: { color: '#f0f0f0' } }
                        }
                    }
                });
            }
            
            // Cargar datos y renderizar
            fetchChartData().then(() => {
                render('line');
            });
            
            // Event listeners para cambiar tipo de gráfico
            document.querySelectorAll('input[name="vMode"]').forEach(r => {
                r.addEventListener('change', (e) => render(e.target.value));
            });
        }
        
        document.addEventListener('DOMContentLoaded', initAuditoriaChart);
        
        // Actualizar gráfico cada 30 segundos
        setInterval(function() {
            fetchChartData().then(() => {
                var activeMode = document.querySelector('input[name="vMode"]:checked');
                if (activeMode && myChart) {
                    myChart.data.labels = chartData.labels;
                    myChart.data.datasets[0].data = chartData.create;
                    myChart.data.datasets[1].data = chartData.update;
                    myChart.data.datasets[2].data = chartData.delete;
                    myChart.update();
                }
            });
        }, 30000);
    </script>
@endpush