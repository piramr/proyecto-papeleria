@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
            <i class="fas fa-check-circle"></i> <strong>{{ session('success') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show no-print" role="alert">
            <i class="fas fa-exclamation-triangle"></i> <strong>{{ session('warning') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle"></i> Venta Registrada Exitosamente</h4>
                </div>
                <div class="card-body">
                    @php
                        $monedaSimbolo = $ajuste->moneda_simbolo ?? '$';
                        $monedaDecimales = $ajuste->moneda_decimales ?? 2;
                    @endphp
                    <!-- Encabezado de Factura -->
                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-md-6">
                            <h2>FACTURA</h2>
                            <p class="mb-1"><strong>Número:</strong> #{{ $factura->numero_factura }}</p>
                            <p class="mb-0"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($factura->fecha_hora)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            @if(!empty($ajuste->logo_url))
                                <img src="{{ $ajuste->logo_url }}" alt="Logo" style="max-height: 50px;" class="mb-2">
                            @endif
                            <h5>{{ $ajuste->empresa_nombre ?? 'Empresa' }}</h5>
                            @if(!empty($ajuste->empresa_telefono))
                                <p class="mb-0">Teléfono: {{ $ajuste->empresa_telefono }}</p>
                            @endif
                            @if(!empty($ajuste->empresa_email))
                                <p class="mb-0">Email: {{ $ajuste->empresa_email }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Datos del Cliente -->
                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-md-6">
                            <h6 class="text-muted">Cliente</h6>
                            <p class="mb-1"><strong>Cédula/RUC:</strong> {{ $factura->cliente->cedula }}</p>
                            <p class="mb-1"><strong>Nombre:</strong> {{ $factura->cliente->nombres }} {{ $factura->cliente->apellidos }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $factura->cliente->email ?? 'N/A' }}</p>
                            <p class="mb-0"><strong>Teléfono:</strong> {{ $factura->cliente->telefono ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Forma de Pago</h6>
                            <p class="mb-1">
                                <span class="badge bg-info fs-6">{{ $factura->tipoPago->nombre }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="table-responsive mb-4">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">Precio Unitario</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($factura->detalles as $detalle)
                                    <tr>
                                        <td>{{ $detalle->producto->nombre }}</td>
                                        <td class="text-end">{{ $detalle->cantidad }}</td>
                                        <td class="text-end">{{ $monedaSimbolo }}{{ number_format($detalle->precio_unitario, $monedaDecimales) }}</td>
                                        <td class="text-end"><strong>{{ $monedaSimbolo }}{{ number_format($detalle->subtotal, $monedaDecimales) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totales -->
                    <div class="row mb-4">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            @php
                                $ivaPorcentaje = $factura->iva_porcentaje;
                                if ($ivaPorcentaje === null && $factura->subtotal > 0) {
                                    $ivaPorcentaje = round(($factura->iva / $factura->subtotal) * 100, 2);
                                }
                            @endphp
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-end">Subtotal:</td>
                                    <td class="text-end">{{ $monedaSimbolo }}{{ number_format($factura->subtotal, $monedaDecimales) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end">IVA ({{ number_format($ivaPorcentaje ?? 0, 2, '.', '') }}%):</td>
                                    <td class="text-end">{{ $monedaSimbolo }}{{ number_format($factura->iva, $monedaDecimales) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td class="text-end"><strong>TOTAL:</strong></td>
                                    <td class="text-end"><h5 class="text-success">{{ $monedaSimbolo }}{{ number_format($factura->total, $monedaDecimales) }}</h5></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Pie de página -->
                    <div class="text-center py-3 border-top">
                        <p class="text-muted mb-0">{{ $ajuste->pie_factura ?? 'Gracias por su compra' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opciones de acción -->
        <div class="col-md-4 no-print">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Opciones</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.ventas.print', $factura->id) }}?autoprint=1" target="_blank" class="btn btn-warning w-100 mb-3">
                        <i class="fas fa-print"></i> Imprimir Factura
                    </a>
                    
                    <a href="{{ route('admin.ventas.print', $factura->id) }}" target="_blank" class="btn btn-info w-100 mb-3">
                        <i class="fas fa-file-pdf"></i> Ver PDF
                    </a>

                    <a href="{{ route('admin.ventas.create') }}" class="btn btn-success w-100 mb-3">
                        <i class="fas fa-plus"></i> Nueva Venta
                    </a>

                    <a href="{{ route('admin.ventas.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-list"></i> Historial de Ventas
                    </a>

                    <hr>
                    
                    <div class="alert alert-info text-center mb-0">
                        <small>Factura #{{ $factura->numero_factura }} registrada exitosamente</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
