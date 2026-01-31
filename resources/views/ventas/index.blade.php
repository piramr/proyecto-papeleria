@extends('layouts.app')

@section('content_header')
    
@stop

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
        <div>
            <h1 class="mb-1">Historial de Ventas</h1>
            <p class="text-muted mb-0">Revisa movimientos recientes, totales y acceso rápido a la creación de facturas.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('ventas.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 20px; padding: 0.4rem 1rem; font-size: 0.9rem;">
                <i class="fas fa-plus"></i> Nueva Venta
            </a>
            <a href="{{ route('ventas.index') }}" class="btn btn-info" style="border-radius: 50%; width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center; font-size: 1rem; margin-left: 20px;" title="Actualizar tabla" data-bs-toggle="tooltip">
                <i class="fas fa-sync-alt"></i>
            </a>
        </div>
    </div>

    <!-- Resumen rápido -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0" style="background: linear-gradient(135deg, rgba(13, 110, 253, 0.08) 0%, rgba(13, 110, 253, 0.02) 100%);">
                <div class="card-body text-center py-4">
                    <div class="mb-2">
                        <i class="fas fa-receipt text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="mb-1 fw-bold text-primary">{{ $facturas->where('fecha_hora', '>=', now()->startOfDay())->count() }}</h2>
                    <p class="text-muted mb-0 small">Ventas de hoy</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0" style="background: linear-gradient(135deg, rgba(25, 135, 84, 0.08) 0%, rgba(25, 135, 84, 0.02) 100%);">
                <div class="card-body text-center py-4">
                    <div class="mb-2">
                        <i class="fas fa-dollar-sign text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h2 class="mb-1 fw-bold text-success">${{ number_format($facturas->where('fecha_hora', '>=', now()->startOfDay())->sum('total'), 2) }}</h2>
                    <p class="text-muted mb-0 small">Total facturado hoy</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0" style="background: linear-gradient(135deg, rgba(23, 162, 184, 0.08) 0%, rgba(23, 162, 184, 0.02) 100%);">
                <div class="card-body text-center py-4">
                    <div class="mb-2">
                        <i class="fas fa-clock text-info" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="mb-1 fw-bold text-info">{{ optional($facturas->first())->fecha_hora?->format('H:i') ?? '--:--' }}</h5>
                    <p class="text-muted mb-0 small">Última venta registrada</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros de búsqueda</h6>
                @if(request()->hasAny(['fecha_desde', 'fecha_hasta', 'cliente_cedula', 'tipo_pago_id', 'numero_factura']))
                    <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-outline-danger" title="Restaurar vista inicial">
                        <i class="fas fa-undo"></i> Limpiar
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('ventas.index') }}" method="GET" class="row g-2">
                <!-- Fechas -->
                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label small fw-semibold">📅 Desde</label>
                    <input type="date" class="form-control form-control-sm" id="fecha_desde" name="fecha_desde" 
                           value="{{ request('fecha_desde') }}">
                </div>

                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label small fw-semibold">📅 Hasta</label>
                    <input type="date" class="form-control form-control-sm" id="fecha_hasta" name="fecha_hasta" 
                           value="{{ request('fecha_hasta') }}">
                </div>

                <!-- Cliente -->
                <div class="col-md-2">
                    <label for="cliente_cedula" class="form-label small fw-semibold">👤 Cliente</label>
                    <input type="text" class="form-control form-control-sm" id="cliente_cedula" name="cliente_cedula" 
                           placeholder="Cédula o nombre..." value="{{ request('cliente_cedula') }}">
                </div>

                <!-- Tipo de pago -->
                <div class="col-md-2">
                    <label for="tipo_pago_id" class="form-label small fw-semibold">💳 Pago</label>
                    <select class="form-select form-select-sm" id="tipo_pago_id" name="tipo_pago_id">
                        <option value="">Todos</option>
                        @foreach($tiposPago as $tipo)
                            <option value="{{ $tipo->id }}" @selected(request('tipo_pago_id') == $tipo->id)>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nº Factura -->
                <div class="col-md-1">
                    <label for="numero_factura" class="form-label small fw-semibold">🔢 Fact.</label>
                    <input type="number" class="form-control form-control-sm" id="numero_factura" name="numero_factura" 
                           placeholder="#" value="{{ request('numero_factura') }}">
                </div>

                <!-- Botón Buscar -->
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted">
                        <th>Factura #</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Pago</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturas as $factura)
                        <tr>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary fw-semibold">#{{ $factura->id }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $factura->cliente->nombres }} {{ $factura->cliente->apellidos }}</div>
                                <div class="text-muted small">{{ $factura->cliente->cedula }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $factura->fecha_hora->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="text-end text-muted">${{ number_format($factura->subtotal, 2) }}</td>
                            <td class="text-end">
                                <span class="fw-bold text-success">${{ number_format($factura->total, 2) }}</span>
                            </td>
                            <td class="text-center">
                                @if($factura->tipoPago->nombre === 'Efectivo')
                                    <span class="badge bg-success-subtle text-success">{{ $factura->tipoPago->nombre }}</span>
                                @elseif($factura->tipoPago->nombre === 'Tarjeta')
                                    <span class="badge bg-info-subtle text-info">{{ $factura->tipoPago->nombre }}</span>
                                @elseif($factura->tipoPago->nombre === 'Cheque')
                                    <span class="badge bg-warning-subtle text-warning">{{ $factura->tipoPago->nombre }}</span>
                                @elseif($factura->tipoPago->nombre === 'Transferencia')
                                    <span class="badge bg-primary-subtle text-primary">{{ $factura->tipoPago->nombre }}</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $factura->tipoPago->nombre }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- Ver Detalle -->
                                    <a href="{{ route('ventas.show', $factura->id) }}" 
                                       class="btn btn-outline-primary" 
                                       title="Ver detalle de la factura"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <!-- Imprimir / PDF -->
                                    <a href="{{ route('ventas.print', $factura->id) }}" 
                                       class="btn btn-outline-warning" 
                                       title="Imprimir o descargar PDF"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       target="_blank">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    
                                    <!-- Anular Factura -->
                                    <form action="{{ route('ventas.destroy', $factura->id) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('⚠️ ¿Estás seguro de anular la factura #{{ $factura->id }}?\n\nEsta acción:\n✓ Eliminará la factura\n✓ Devolverá el stock de los productos\n✓ No se podrá deshacer');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="btn btn-outline-danger" 
                                                title="Anular factura"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center text-muted">
                                    <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                                    <p class="mb-1">No hay ventas registradas</p>
                                    <small class="mb-3">Crea la primera venta para comenzar a ver el historial.</small>
                                    <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Nueva Venta
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $facturas->links() }}
    </div>
</div>

<script>
    // Inicializar tooltips de Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
