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
                <select class="form-select form-select-sm w-auto shadow-sm border-0">
                    <option>Recurso: Todos</option>
                    <option>Productos</option>
                    <option>Categorías</option>
                </select>
                <select class="form-select form-select-sm w-auto shadow-sm border-0">
                    <option>Op: Todas</option>
                    <option>CREATE</option>
                    <option>UPDATE</option>
                    <option>DELETE</option>
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
                        <select class="form-select form-select-sm w-auto">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
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
                    <tbody>
                        <tr>
                            <td class="ps-3 text-muted">1052</td>
                            <td>2026-02-07 22:10</td>
                            <td>j_perez<br><small class="text-muted">ID: 15 | S: 4k2</small></td>
                            <td><span class="badge bg-success badge-op">CREATE</span></td>
                            <td>Producto</td>
                            <td><code>PRD-23</code></td>
                            <td>-</td>
                            <td>-</td>
                            <td class="text-muted">NULL</td>
                            <td>Pintura Satinada</td>
                        </tr>
                        <tr>
                            <td class="ps-3 text-muted">1053</td>
                            <td>2026-02-07 22:45</td>
                            <td>admin_user<br><small class="text-muted">ID: 01 | S: 9z1</small></td>
                            <td><span class="badge bg-warning text-dark badge-op">UPDATE</span></td>
                            <td>Categoria</td>
                            <td><code>CAT-05</code></td>
                            <td><span class="text-primary fw-bold">CAT-M</span></td>
                            <td>Descuento</td>
                            <td class="text-decoration-line-through">5%</td>
                            <td class="fw-bold text-success">10%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white py-3 border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted small mb-0">Mostrando 1 a 10 de 50 registros</p>
                </div>
                <div class="col-md-6">
                    <nav aria-label="Navegación de registros">
                        <ul class="pagination pagination-sm justify-content-end">
                            <li class="page-item disabled">
                                <a class="page-link" href="#"><i class="fas fa-angle-left"></i></a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#"><i class="fas fa-angle-right"></i></a>
                            </li>
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
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('mainChart');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let myChart;
            const dataLabels = ['01 Feb', '02 Feb', '03 Feb', '04 Feb', '05 Feb', '06 Feb', '07 Feb'];
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
                        labels: dataLabels,
                        datasets: [
                            {
                                label: 'CREATE',
                                data: [10, 25, 12, 30, 20, 40, 35],
                                borderColor: theme.create,
                                backgroundColor: type === 'line' ? getGradient(theme.create) : theme.create,
                                fill: type === 'line',
                                tension: 0.4
                            },
                            {
                                label: 'UPDATE',
                                data: [30, 45, 35, 60, 50, 70, 65],
                                borderColor: theme.update,
                                backgroundColor: type === 'line' ? getGradient(theme.update) : theme.update,
                                fill: type === 'line',
                                tension: 0.4
                            },
                            {
                                label: 'DELETE',
                                data: [2, 8, 4, 10, 5, 12, 6],
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
            document.querySelectorAll('input[name="vMode"]').forEach(r => {
                r.addEventListener('change', (e) => render(e.target.value));
            });
            render('line');
        });
    </script>
@endpush