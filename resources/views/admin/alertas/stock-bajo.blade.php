@extends('adminlte::page')

@section('title', 'Alertas de Stock Bajo')

@section('content_header')
    <h1>
        <i class="fas fa-exclamation-triangle text-warning"></i> Alertas de Stock Bajo
    </h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Productos con Stock Bajo</h3>
                </div>
                <div class="card-body">
                    @if($productos->isEmpty())
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Todos los productos tienen stock adecuado.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Stock Actual</th>
                                        <th>Stock Mínimo</th>
                                        <th>Categoría</th>
                                       
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($productos as $producto)
                                        <tr class="@if($producto->cantidad_stock == 0) table-danger @elseif($producto->cantidad_stock < $stockMinimo) table-warning @endif">
                                            <td>
                                                <span class="badge badge-primary">{{ $producto->codigo }}</span>
                                            </td>
                                            <td>{{ $producto->nombre }}</td>
                                            <td>
                                                <strong class="@if($producto->cantidad_stock == 0) text-danger @else text-warning @endif">
                                                    {{ $producto->cantidad_stock }}
                                                </strong>
                                            </td>
                                            <td>{{ $stockMinimo }}</td>
                                            <td>{{ $producto->categoria->nombre ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i> <strong>Total de productos con stock bajo:</strong> {{ $productos->count() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table-warning {
            background-color: #fff3cd;
        }
        .table-danger {
            background-color: #f8d7da;
        }
    </style>
@endsection
