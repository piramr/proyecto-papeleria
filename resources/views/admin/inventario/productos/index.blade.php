@extends('adminlte::page')

@section('title', 'Productos')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12">
                <h1 class="m-0 text-dark font-weight-bold">Lista de productos</h1>
                <p class="text-muted mb-0">Se muestra los productos registrados dentro del inventario.</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <button type="button" class="btn btn-primary px-4 shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Agregar producto
                </button>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                {{-- CONTROLES SUPERIORES --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">

                    <div class="d-flex flex-column flex-sm-row align-items-center mb-3 mb-md-0">
                        {{-- BUSCADOR --}}
                        <div class="form-group mb-2 mb-sm-0 mr-sm-3 d-flex align-items-center">
                            <label class="mb-0 mr-2 font-weight-normal">Buscar:</label>
                            <input type="search" id="customSearch" class="form-control"
                                placeholder="Nombre del producto...">
                        </div>

                        {{-- FILTRO CATEGORÍA --}}
                        <div class="dropdown">
                            <button id="btnCategoria" class="btn btn-default dropdown-toggle" type="button"
                                data-toggle="dropdown">
                                <i class="fas fa-filter mr-1 text-muted"></i> Todas las categorías
                            </button>
                            <div class="dropdown-menu shadow border-0">
                                <a class="dropdown-item categoria-item" data-id="">Todas</a>
                                <a class="dropdown-item categoria-item" data-id="1">Papelería</a>
                                <a class="dropdown-item categoria-item" data-id="2">Oficina</a>
                            </div>
                        </div>
                    </div>

                    {{-- REGISTROS POR PÁGINA --}}
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

                {{-- TABLA --}}
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
                    </table>
                </div>

            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(function() {

            let categoryId = '';

            const table = $('#tablaProductos').DataTable({
                processing: true,
                serverSide: true,
                responsive: false,
                scrollX: true,
                autoWidth: false,

                ajax: {
                    url: '{{ route('productos.datatable') }}',
                    data: function(d) {
                        d.categoryid = categoryId;
                    }
                },

                columns: [{
                        data: 'codigo'
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'categoria'
                    },
                    {
                        data: 'stock'
                    },
                    {
                        data: 'precio'
                    },
                    {
                        data: 'iva'
                    },
                    {
                        data: 'proveedores'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],

                dom: '<"row mb-2"' +
                    '<"col-sm-12 col-md-6"i>' +
                    '<"col-sm-12 col-md-6 text-md-right"p>' +
                    '>' +
                    '<"row"' +
                    '<"col-sm-12"tr>' +
                    '>' +
                    '<"row mt-2"' +
                    '<"col-sm-12 col-md-5"i>' +
                    '<"col-sm-12 col-md-7 text-md-right"p>' +
                    '>',

                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
                }
            });

            $('#customSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            $('#customLength').on('change', function() {
                table.page.len(this.value).draw();
            });

            $('.categoria-item').on('click', function() {
                categoryId = $(this).data('id');
                $('#btnCategoria').html('<i class="fas fa-filter mr-1 text-muted"></i> ' + $(this).text());
                table.ajax.reload();
            });

        });
    </script>
@stop
