@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle"></i> Venta Registrada Exitosamente</h4>
                </div>
                <div class="card-body">
                    <!-- Encabezado de Factura -->
                    <div class="row mb-4 pb-4 border-bottom">
                        <div class="col-md-6">
                            <h2>FACTURA</h2>
                            <p class="mb-1"><strong>Número:</strong> #{{ $factura->id }}</p>
                            <p class="mb-0"><strong>Fecha:</strong> {{ $factura->fecha_hora->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5>Papelería XYZ</h5>
                            <p class="mb-0">Teléfono: +58 XXX-XXXX-XX</p>
                            <p class="mb-0">Email: info@papeleria.com</p>
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
                                        <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="text-end"><strong>${{ number_format($detalle->subtotal, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totales -->
                    <div class="row mb-4">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="text-end">Subtotal:</td>
                                    <td class="text-end">${{ number_format($factura->subtotal, 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td class="text-end"><strong>TOTAL:</strong></td>
                                    <td class="text-end"><h5 class="text-success">${{ number_format($factura->total, 2) }}</h5></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Pie de página -->
                    <div class="text-center py-3 border-top">
                        <p class="text-muted mb-0">Gracias por su compra</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Opciones de acción -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Opciones</h5>
                </div>
                <div class="card-body">
                    <button onclick="window.print()" class="btn btn-warning w-100 mb-3">
                        <i class="fas fa-print"></i> Imprimir Factura
                    </button>
                    
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
                        <small>Factura #{{ $factura->id }} registrada exitosamente</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
