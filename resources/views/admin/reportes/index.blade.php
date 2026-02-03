@extends('layouts.app')

@section('title', 'Reportes')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12">
                <h1 class="m-0 text-dark font-weight-bold">Reportes del Sistema</h1>
                <p class="text-muted mb-0">Genera reportes detallados de ventas, inventario, compras y ganancias.</p>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Reporte de Ventas -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-chart-line text-primary" style="font-size: 1.5rem; margin-right: 10px;"></i>
                        <h5 class="m-0 text-dark font-weight-bold">Reporte de Ventas</h5>
                    </div>
                    <p class="text-muted small mb-3">Filtrar ventas por fecha, cliente o tipo de pago.</p>
                    
                    <form class="needs-validation">
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Fecha Desde</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_desde" 
                                   value="{{ request('fecha_desde') }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Fecha Hasta</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_hasta" 
                                   value="{{ request('fecha_hasta') }}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Cliente</label>
                            <input type="text" class="form-control form-control-sm" name="cliente" 
                                   placeholder="Cédula o nombre..." value="{{ request('cliente') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small font-weight-bold">Tipo de Pago</label>
                            <select class="form-select form-select-sm" name="tipo_pago_id">
                                <option value="">Todos</option>
                                @foreach($tiposPago as $tipo)
                                    <option value="{{ $tipo->id }}" @selected(request('tipo_pago_id') == $tipo->id)>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </form>

                    <div class="d-grid gap-2 pt-2">
                        <a href="{{ route('admin.reportes.ventas.pdf') }}" class="btn btn-outline-danger btn-sm" target="_blank">
                            <i class="fas fa-file-pdf"></i> Descargar PDF
                        </a>
                        <a href="{{ route('admin.reportes.ventas.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel"></i> Descargar Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Inventario -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-boxes text-info" style="font-size: 1.5rem; margin-right: 10px;"></i>
                        <h5 class="m-0 text-dark font-weight-bold">Reporte de Inventario</h5>
                    </div>
                    <p class="text-muted small mb-3">Análisis del estado de stock por categoría.</p>
                    
                    <form class="needs-validation">
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Categoría</label>
                            <select class="form-select form-select-sm" name="categoria_id">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small font-weight-bold">Filtro de Stock</label>
                            <select class="form-select form-select-sm" name="stock_filter">
                                <option value="">Todos los productos</option>
                                <option value="bajo">Solo stock bajo</option>
                                <option value="maximo">Solo stock máximo</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </form>

                    <div class="d-grid gap-2 pt-2">
                        <a href="{{ route('admin.reportes.inventario.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel"></i> Descargar Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Compras -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-shopping-cart text-warning" style="font-size: 1.5rem; margin-right: 10px;"></i>
                        <h5 class="m-0 text-dark font-weight-bold">Reporte de Compras</h5>
                    </div>
                    <p class="text-muted small mb-3">Historial de compras a proveedores en un período.</p>
                    
                    <form class="needs-validation">
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Fecha Desde</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_desde">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small font-weight-bold">Fecha Hasta</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_hasta">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </form>

                    <div class="d-grid gap-2 pt-2">
                        <a href="{{ route('admin.reportes.compras.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel"></i> Descargar Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte de Ganancias -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-dollar-sign text-success" style="font-size: 1.5rem; margin-right: 10px;"></i>
                        <h5 class="m-0 text-dark font-weight-bold">Reporte de Ganancias</h5>
                    </div>
                    <p class="text-muted small mb-3">Análisis de ganancias por período, categoría y producto.</p>
                    
                    <form class="needs-validation">
                        <div class="mb-2">
                            <label class="form-label small font-weight-bold">Fecha Desde</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_desde">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small font-weight-bold">Fecha Hasta</label>
                            <input type="date" class="form-control form-control-sm" name="fecha_hasta">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                    </form>

                    <div class="d-grid gap-2 pt-2">
                        <a href="{{ route('admin.reportes.ganancias.excel') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel"></i> Descargar Excel (3 hojas)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        window.location.href = window.location.pathname + '?' + params.toString();
    });
});
</script>
@stop