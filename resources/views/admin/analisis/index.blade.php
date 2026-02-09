@extends('layouts.app')

@section('title', 'Análisis')

@section('content_header')
    <h1>Análisis y Estadísticas</h1>
@stop

@section('content')
<!-- KPIs Principales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>${{ number_format($totalVentas, 2) }}</h3>
                <p>Total de Ventas</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>${{ number_format($ventasHoy, 2) }}</h3>
                <p>Ventas Hoy</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalClientes }}</h3>
                <p>Total de Clientes</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $totalPedidos }}</h3>
                <p>Ventas Realizadas</p>
            </div>
            <div class="icon">
                <i class="fas fa-receipt"></i>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos principales en una sola fila -->
<div class="row mb-4">
    <!-- Gráfico de Ventas Últimos 7 Días -->
    <div class="col-lg-9">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Ventas Últimos 7 Días</h3>
            </div>
            <div class="card-body" style="position: relative; height: 380px;">
                <canvas id="ventasChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Estado del Inventario -->
    <div class="col-lg-3">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-boxes mr-2"></i>Estado del Inventario</h3>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong>Productos en Oferta:</strong></span>
                        <span class="badge badge-success badge-lg">{{ $productosEnOferta }}</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: {{ $productosEnOferta > 0 ? 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong>Stock Bajo:</strong></span>
                        <span class="badge badge-danger badge-lg">{{ $productosBajoStock }}</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-danger" style="width: {{ $productosBajoStock > 0 ? 100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><strong>Total en Stock:</strong></span>
                        <span class="badge badge-info badge-lg">{{ number_format($totalProductos) }}</span>
                    </div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-info" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ingresos por Mes - Ancho completo -->
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line mr-2"></i>Ingresos Últimos 12 Meses</h3>
            </div>
            <div class="card-body" style="position: relative; height: 350px;">
                <canvas id="ingresosMesesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tablas de datos organizadas -->
<div class="row mb-4">
    <!-- Productos Más Vendidos -->
    <div class="col-lg-4">
        <div class="card card-info card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-star mr-2"></i>Top Productos (30 días)</h3>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-valign-middle table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productosMásVendidos as $producto)
                            <tr>
                                <td style="font-size: 12px;">{{ $producto->producto_nombre }}</td>
                                <td><span class="badge badge-primary">{{ $producto->total_vendido }}</span></td>
                                <td style="font-size: 12px;">${{ number_format($producto->ingresos, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Sin datos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Categorías Más Vendidas -->
    <div class="col-lg-4">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tags mr-2"></i>Top Categorías (30 días)</h3>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-valign-middle table-sm">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Cantidad</th>
                            <th>Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoriasMásVendidas as $categoria)
                            <tr>
                                <td style="font-size: 12px;">{{ $categoria->categoria }}</td>
                                <td><span class="badge badge-success">{{ $categoria->total_vendido }}</span></td>
                                <td style="font-size: 12px;">${{ number_format($categoria->ingresos, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Sin datos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Clientes con Más Compras -->
    <div class="col-lg-4">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-users mr-2"></i>Top Clientes (30 días)</h3>
            </div>
            <div class="card-body table-responsive p-0" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-striped table-valign-middle table-sm">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Compras</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientesMásCompras as $cliente)
                            <tr>
                                <td style="font-size: 12px;">{{ $cliente->cliente_nombre }}</td>
                                <td><span class="badge badge-info">{{ $cliente->total_compras }}</span></td>
                                <td style="font-size: 12px;">${{ number_format($cliente->monto_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Sin datos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Ventas Últimos 7 Días - MEJORADO
        var ventasData = {!! json_encode($ventasÚltimos7Días) !!};
        
        if (ventasData && ventasData.length > 0) {
            var ventasLabels = ventasData.map(function(item) { return item.fecha; });
            var ventasTotales = ventasData.map(function(item) { return parseFloat(item.total); });
            
            var ctxVentas = document.getElementById('ventasChart');
            if (ctxVentas) {
                var gradient = ctxVentas.getContext('2d').createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(76, 175, 80, 0.6)');
                gradient.addColorStop(1, 'rgba(76, 175, 80, 0.1)');
                
                new Chart(ctxVentas, {
                    type: 'bar',
                    data: {
                        labels: ventasLabels,
                        datasets: [{
                            label: 'Ventas ($)',
                            data: ventasTotales,
                            backgroundColor: gradient,
                            borderColor: 'rgba(76, 175, 80, 1)',
                            borderWidth: 2,
                            borderRadius: 8,
                            hoverBackgroundColor: 'rgba(56, 142, 60, 0.8)',
                            hoverBorderColor: 'rgba(56, 142, 60, 1)',
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                display: true, 
                                position: 'top',
                                labels: {
                                    font: { size: 14, weight: 'bold' },
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 },
                                callbacks: {
                                    label: function(context) {
                                        return '$' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: Math.max(...ventasTotales) * 1.2,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: { size: 12 },
                                    callback: function(value) {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: { size: 13, weight: 'bold' }
                                }
                            }
                        }
                    }
                });
            }
        }

        // Gráfico de Ingresos por Mes
        var ingresosData = {!! json_encode($ingresosPorMes) !!};
        
        if (ingresosData && ingresosData.etiquetas && ingresosData.datos) {
            var ctxIngresos = document.getElementById('ingresosMesesChart');
            if (ctxIngresos) {
                new Chart(ctxIngresos, {
                    type: 'line',
                    data: {
                        labels: ingresosData.etiquetas,
                        datasets: [{
                            label: 'Ingresos Mensuales ($)',
                            data: ingresosData.datos.map(function(val) { return parseFloat(val); }),
                            fill: true,
                            backgroundColor: 'rgba(75, 192, 75, 0.2)',
                            borderColor: 'rgba(75, 192, 75, 1)',
                            borderWidth: 3,
                            tension: 0.4,
                            pointRadius: 6,
                            pointBackgroundColor: 'rgba(75, 192, 75, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                display: true, 
                                position: 'top',
                                labels: {
                                    font: { size: 13, weight: 'bold' },
                                    padding: 15
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 13, weight: 'bold' },
                                bodyFont: { size: 12 },
                                callbacks: {
                                    label: function(context) {
                                        return '$' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: { size: 11 },
                                    callback: function(value) {
                                        return '$' + value.toFixed(2);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: { size: 11, weight: 'bold' }
                                }
                            }
                        }
                    }
                });
            }
        }
    });
</script>
@stop