@extends('layouts.app')

@section('title', 'Compras')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Compras a Proveedores</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('compras.create') }}" class="btn btn-primary float-right">
                <i class="fas fa-plus"></i> Nueva Compra
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Compras</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($compras->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nº Compra</th>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Subtotal</th>
                                <th>IVA</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Usuario</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($compras as $compra)
                                <tr>
                                    <td>
                                        <strong>{{ $compra->numero_compra }}</strong>
                                    </td>
                                    <td>{{ $compra->fecha_compra->format('d/m/Y H:i') }}</td>
                                    <td>{{ $compra->proveedor->nombre }}</td>
                                    <td>${{ number_format($compra->subtotal, 2, ',', '.') }}</td>
                                    <td>${{ number_format($compra->iva, 2, ',', '.') }}</td>
                                    <td>
                                        <strong>${{ number_format($compra->total, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>
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
                                    </td>
                                    <td>{{ $compra->usuario->name }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('compras.show', $compra->id) }}" 
                                               class="btn btn-sm btn-info" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($compra->estado === 'pendiente')
                                                <a href="{{ route('compras.edit', $compra->id) }}" 
                                                   class="btn btn-sm btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="marcarRecibida({{ $compra->id }})"
                                                        title="Marcar como recibida">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center">
                    {{ $compras->links() }}
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    <i class="fas fa-info-circle"></i> No hay compras registradas aún. 
                    <a href="{{ route('compras.create') }}">Crear la primera compra</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para marcar compra como recibida -->
    <div class="modal fade" id="modalRecibir" tabindex="-1" role="dialog" aria-labelledby="modalRecibirLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRecibirLabel">Marcar Compra como Recibida</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formRecibir" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>¿Está seguro de que desea marcar esta compra como recibida?</p>
                        <p class="text-muted">Esto actualizará el stock de todos los productos.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Sí, Marcar como Recibida</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function marcarRecibida(compraId) {
            const form = document.getElementById('formRecibir');
            form.action = `/admin/compras/${compraId}/recibir`;
            $('#modalRecibir').modal('show');
        }
    </script>
@stop