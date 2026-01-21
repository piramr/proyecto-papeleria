@extends('adminlte::page')

@section('title', 'Proveedores')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12">
                <h1 class="m-0 text-dark font-weight-bold">Lista de proveedores</h1>
                <p class="text-muted mb-0">Se muestra los proveedores registrados dentro del sistema.</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-primary px-4 shadow-sm" data-toggle="collapse" data-target="#formProveedor"
                    aria-expanded="false" aria-controls="formProveedor">
                    <i class="fas fa-plus mr-2"></i> Registrar proveedor
                </button>

            </div>
        </div>

        <div id="formProveedor" class="collapse {{ $errors->any() ? 'show' : '' }}">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <p class="text-dark">
                        Ingrese los datos del proveedor
                    </p>
                </div>

                <div class="card-body">
                    @include('admin.proveedores.partials.form')
                </div>
            </div>
        </div>

    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
                    <div class="d-flex flex-column flex-sm-row align-items-center mb-3 mb-md-0">
                        <div class="form-group mb-2 mb-sm-0 mr-sm-3 d-flex align-items-center">
                            <label class="mb-0 mr-2 font-weight-normal">Buscar:</label>
                            <input type="search" id="customSearch" class="form-control"
                                placeholder="Nombre del producto ...">
                        </div>

                        <div class="dropdown mb-sm-0 mb-0">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fas fa-filter mr-1 text-muted"></i> Todas las categorías
                            </button>
                            <div class="dropdown-menu shadow border-0">
                                <a class="dropdown-item" href="#">Papelería</a>
                                <a class="dropdown-item" href="#">Oficina</a>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-center ml-3">
                            <button class="btn btn-default mr-1">
                                <i class="fas text-success fa-file"></i> Excel
                            </button>
                            <button class="btn btn-default">
                                <i class="fas text-danger fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="text-muted">Mostrar</span>
                        <select id="customLength" class="form-control mx-2 w-auto">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="text-muted">registros</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        Mostrando x a y registros de z(total)
                    </div>
                    <div>
                        Botones de paginacion
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tablaProductos" class="table table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th>Categoría</th>
                                <th>Stock</th>
                                <th>Precio</th>
                                <th>IVA</th>
                                <th>Proveedor/es</th>
                                <th>Fecha registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>PAP-001</td>
                                <td>Cuaderno</td>
                                <td>Papelería</td>
                                <td>150</td>
                                <td>$1.50</td>
                                <td>No</td>
                                <td>Lista de proveedor/es</td>
                                <td>10-01-2026 10:09am</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center">
                                        <button class="btn btn-sm btn-info mr-1" title="Ver"><i
                                                class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-warning mr-1" title="Editar"><i
                                                class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger" title="Eliminar"><i
                                                class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        Mostrando x a y registros de z(total)
                    </div>
                    <div>
                        Botones de paginacion
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        let indexDireccion = 1;

        document.getElementById('btnAddDireccion').addEventListener('click', function() {

            const container = document.getElementById('direcciones-container');

            const html = `
        <div class="direccion-item border rounded p-2 mb-2 position-relative">
            <button type="button" class="btn btn-sm btn-danger position-absolute"
                    style="top:5px; right:5px"
                    onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i> Quitar
            </button>

            <small class="text-muted">Dirección adicional</small>

            <div class="form-row mt-2">
                <div class="form-group col-md-6">
                    <input type="text" class="form-control "
                           name="direcciones[${indexDireccion}][provincia]" placeholder="Provincia">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" class="form-control "
                           name="direcciones[${indexDireccion}][ciudad]" placeholder="Ciudad">
                </div>
            </div>

            <div class="form-group">
                <input type="text" class="form-control "
                       name="direcciones[${indexDireccion}][calle]" placeholder="Calle">
            </div>

            <div class="form-group mb-0">
                <input type="text" class="form-control"
                       name="direcciones[${indexDireccion}][referencia]" placeholder="Referencia">
            </div>
        </div>`;

            container.insertAdjacentHTML('beforeend', html);
            indexDireccion++;
        });
    </script>

@stop
