@extends('layouts.app')

@section('title', 'Detalle de Compra')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Compra {{ $compra->numero_compra }}</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('compras.index') }}" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    @if($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Información de la Compra -->
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Información de la Compra</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Número de Compra:</dt>
                                <dd><strong>{{ $compra->numero_compra }}</strong></dd>

                                <dt>Proveedor:</dt>
                                <dd>{{ $compra->proveedor->nombre }}</dd>

                                <dt>RUC Proveedor:</dt>
                                <dd>{{ $compra->proveedor->ruc }}</dd>

                                <dt>Fecha de Compra:</dt>
                                <dd>{{ $compra->fecha_compra->format('d/m/Y H:i') }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="dl-horizontal">
                                <dt>Estado:</dt>
                                <dd>
                                    @switch($compra->estado)
                                        @case('pendiente')
                                            <span class="badge badge-warning">Pendiente</span>
                                            @break
                                        @case('recibida')
                                            <span class="badge badge-success">Recibida</span>
                                            @break
                                        @case('cancelada')
                                            <span class="badge badge-secondary">Cancelada</span>
                                            @break
                                        @case('anulada')
                                            <span class="badge badge-danger">Anulada</span>
                                            @break
                                    @endswitch
                                </dd>

                                <dt>Usuario:</dt>
                                <dd>{{ $compra->usuario->name }}</dd>

                                <dt>Tipo de Pago:</dt>
                                <dd>{{ $compra->tipoPago?->descripcion ?? 'N/A' }}</dd>

                                @if($compra->fecha_recepcion)
                                    <dt>Fecha de Recepción:</dt>
                                    <dd>{{ $compra->fecha_recepcion->format('d/m/Y H:i') }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($compra->descripcion)
                        <hr>
                        <dt>Descripción:</dt>
                        <dd>{{ $compra->descripcion }}</dd>
                    @endif

                    @if($compra->observaciones)
                        <hr>
                        <dt>Observaciones:</dt>
                        <dd>{{ $compra->observaciones }}</dd>
                    @endif
                </div>
            </div>

            <!-- Detalles de Productos -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Detalle de Productos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Producto</th>
                                    <th>Código</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($compra->detalles as $detalle)
                                    <tr>
                                        <td>{{ $detalle->producto->nombre }}</td>
                                        <td>{{ $detalle->producto->codigo_barras }}</td>
                                        <td>{{ $detalle->cantidad }}</td>
                                        <td>${{ number_format($detalle->precio_unitario, 2, ',', '.') }}</td>
                                        <td>${{ number_format($detalle->subtotal, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Resumen de Compra</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Subtotal</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control text-right" 
                                   value="{{ number_format($compra->subtotal, 2, ',', '.') }}" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>IVA (12%)</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control text-right" 
                                   value="{{ number_format($compra->iva, 2, ',', '.') }}" readonly>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label><strong>Total</strong></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="text" class="form-control text-right" 
                                   value="{{ number_format($compra->total, 2, ',', '.') }}" readonly 
                                   style="font-size: 1.2rem; font-weight: bold;">
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label>Total Artículos</label>
                        <input type="text" class="form-control" 
                               value="{{ $compra->detalles->sum('cantidad') }}" readonly>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Acciones</h3>
                </div>
                <div class="card-body">
                    @if($compra->estado === 'pendiente')
                        <a href="{{ route('compras.edit', $compra->id) }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-edit"></i> Editar Compra
                        </a>
                        <button type="button" class="btn btn-success btn-block mb-2" onclick="marcarRecibida()">
                            <i class="fas fa-check"></i> Marcar como Recibida
                        </button>
                        <button type="button" class="btn btn-danger btn-block" onclick="cancelarCompra()">
                            <i class="fas fa-times"></i> Cancelar Compra
                        </button>
                    @elseif($compra->estado === 'recibida')
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle"></i> Esta compra ya ha sido recibida
                        </div>
                    @else
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i> Esta compra no puede ser modificada
                        </div>
                    @endif

                    <hr>

                    <a href="{{ route('compras.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-list"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para marcar como recibida -->
    <div class="modal fade" id="modalRecibir" tabindex="-1" role="dialog" aria-labelledby="modalRecibirLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('compras.recibir', $compra->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalRecibirLabel">Marcar Compra como Recibida</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Está seguro de que desea marcar esta compra como recibida?</p>
                        <p class="text-muted">Esto actualizará el stock de todos los productos en el inventario.</p>
                        <div class="alert alert-info" role="alert">
                            <strong>Total de artículos:</strong> {{ $compra->detalles->sum('cantidad') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Sí, Marcar como Recibida</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para cancelar compra -->
    <div class="modal fade" id="modalCancelar" tabindex="-1" role="dialog" aria-labelledby="modalCancelarLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('compras.cancelar', $compra->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCancelarLabel">Cancelar Compra</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="razon">Razón de la cancelación <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="razon" name="razon" rows="4" 
                                      placeholder="Ingrese la razón de la cancelación..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Volver</button>
                        <button type="submit" class="btn btn-danger">Sí, Cancelar Compra</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function marcarRecibida() {
            $('#modalRecibir').modal('show');
        }

        function cancelarCompra() {
            $('#modalCancelar').modal('show');
        }
    </script>
@stop
