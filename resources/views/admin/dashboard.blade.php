@extends('layouts.app')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Tablero Principal</h1>
@stop

@section('content')
    <div class="row">
        {{-- Ingresos del Mes --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none border">
                <span class="info-box-icon bg-success-gradient"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Ingresos del Mes</span>
                    <span class="info-box-number text-lg">${{ number_format($ingresosMes, 2) }}</span>
                    @if($cambioIngresos >= 0)
                        <span class="text-success text-sm"><i class="fas fa-arrow-up"></i> {{ number_format(abs($cambioIngresos), 1) }}%</span>
                    @else
                        <span class="text-danger text-sm"><i class="fas fa-arrow-down"></i> {{ number_format(abs($cambioIngresos), 1) }}%</span>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Clientes Registrados --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none border">
                <span class="info-box-icon bg-info-gradient"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Clientes Registrados</span>
                    <span class="info-box-number text-lg">{{ number_format($clientesActivos) }}</span>
                    @if($cambioClientes >= 0)
                        <span class="text-success text-sm"><i class="fas fa-arrow-up"></i> {{ number_format(abs($cambioClientes), 1) }}%</span>
                    @else
                        <span class="text-danger text-sm"><i class="fas fa-arrow-down"></i> {{ number_format(abs($cambioClientes), 1) }}%</span>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Ventas del Mes --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none border">
                <span class="info-box-icon bg-primary-gradient"><i class="fas fa-shopping-cart"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Ventas del Mes</span>
                    <span class="info-box-number text-lg">{{ number_format($ventasMes) }}</span>
                    @if($cambioVentas >= 0)
                        <span class="text-success text-sm"><i class="fas fa-arrow-up"></i> {{ number_format(abs($cambioVentas), 1) }}%</span>
                    @else
                        <span class="text-danger text-sm"><i class="fas fa-arrow-down"></i> {{ number_format(abs($cambioVentas), 1) }}%</span>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Stock Total --}}
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box shadow-none border">
                <span class="info-box-icon bg-warning-gradient"><i class="fas fa-boxes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Stock Total</span>
                    <span class="info-box-number text-lg">{{ number_format($stockTotal) }}</span>
                    <span class="text-danger text-sm"><i class="fas fa-exclamation-triangle"></i> {{ $productosBajoStock }} productos bajos</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        {{-- Ingresos vs Gastos --}}
        <div class="col-md-8">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Ingresos Últimos 6 Meses</h3>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"
                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        
        {{-- Resumen Financiero --}}
        <div class="col-md-4">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Resumen Financiero</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Ingresos del Mes:</span>
                            <span class="text-success font-weight-bold">${{ number_format($ingresosMes, 2) }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Gastos del Mes:</span>
                            <span class="text-danger font-weight-bold">${{ number_format($gastosMes, 2) }}</span>
                        </div>
                    </div>
                    <hr>
                    <div>
                        <div class="d-flex justify-content-between">
                            <span class="text-bold">Utilidad del Mes:</span>
                            <span class="text-{{ $utilidadMes >= 0 ? 'success' : 'danger' }} font-weight-bold">
                                ${{ number_format($utilidadMes, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Margen de utilidad: 
                            @if($ingresosMes > 0)
                                {{ number_format(($utilidadMes / $ingresosMes) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        {{-- Últimas Ventas --}}
        <div class="col-md-7">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Últimas Ventas</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th class="text-right">Total</th>
                                <th>Pago</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimasVentas as $venta)
                                <tr>
                                    <td>
                                        <strong>{{ $venta->cliente->nombres }} {{ $venta->cliente->apellidos }}</strong><br>
                                        <small class="text-muted">{{ $venta->cliente->cedula }}</small>
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td class="text-right">
                                        <strong>${{ number_format($venta->total, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($venta->tipoPago->nombre === 'Efectivo')
                                            <span class="badge badge-success">{{ $venta->tipoPago->nombre }}</span>
                                        @elseif($venta->tipoPago->nombre === 'Tarjeta')
                                            <span class="badge badge-info">{{ $venta->tipoPago->nombre }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $venta->tipoPago->nombre }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No hay ventas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Productos Más Vendidos --}}
        <div class="col-md-5">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Top Productos del Mes</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($productosMasVendidos as $producto)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $producto->nombre }}</strong><br>
                                    <small class="text-muted">{{ number_format($producto->total_vendido) }} unidades vendidas</small>
                                </div>
                                <div class="text-right">
                                    <strong class="text-success">${{ number_format($producto->ingresos, 2) }}</strong>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted">
                                No hay datos disponibles
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de ingresos por mes
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        const meses = {!! json_encode($ingresosPorMes->pluck('mes')->toArray()) !!};
        const totales = {!! json_encode($ingresosPorMes->pluck('total')->toArray()) !!};
        
        const nombresMeses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        const labels = meses.map(mes => nombresMeses[mes - 1]);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ingresos ($)',
                    data: totales,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.parsed.y.toLocaleString('es-ES', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString('es-ES');
                            }
                        }
                    }
                }
            }
        });
    </script>
@stop
