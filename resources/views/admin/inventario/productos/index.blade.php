@extends('layouts.app')

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
                <button type="button" class="btn btn-primary px-4 shadow-sm" data-toggle="collapse"
                    data-target="#formProducto">
                    <i class="fas fa-plus mr-2"></i> Agregar producto
                </button>
            </div>
        </div>
        {{-- FORMULARIO --}}
        <div id="formProducto" class="collapse {{ $errors->any() ? 'show' : '' }}">
            <div class="card card-outline shadow-sm">
                <div class="card-body">
                    @include('admin.inventario.productos.partials.form')
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                {{-- CONTROLES SUPERIORES --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-sm-3">

                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start mb-0 mb-md-0">
                        {{-- BUSCADOR --}}
                        <div class="form-group mb-3 mb-sm-0 mr-sm-3 d-flex align-items-center">
                            <label class="mb-0 mr-2 font-weight-normal">Buscar:</label>
                            <input type="search" id="customSearch" class="form-control" placeholder="Filtro...">
                            <span class="glyphicon glyphicon-search form-control-feedback"></span>
                        </div>

                        {{-- FILTRO CATEGORÍA --}}
                        <div class="dropdown mb-3 mb-sm-0 mr-sm-3">
                            <button id="btnCategoria" class="btn btn-default dropdown-toggle" type="button"
                                data-toggle="dropdown">
                                <i class="fas fa-filter mr-1 text-muted"></i> Categoría
                            </button>
                            <div class="dropdown-menu shadow border-0" style="max-height: 300px; overflow-y: auto;">
                                <a class="dropdown-item categoria-item" data-id="">Todas</a>
                                @foreach ($categorias as $categoria)
                                    <a class="dropdown-item categoria-item"
                                        data-id="{{ $categoria->id }}">{{ $categoria->nombre }}</a>
                                @endforeach
                            </div>
                        </div>

                        {{-- FILTRO PROVEEDORES --}}
                        <div class="dropdown mb-3 mb-sm-0 mr-sm-3">
                            <button id="btnProveedores" class="btn btn-default dropdown-toggle" type="button"
                                data-toggle="dropdown">
                                <i class="fas fa-filter mr-1 text-muted"></i> Proveedor
                            </button>
                            <div class="dropdown-menu shadow border-0" style="max-height: 300px; overflow-y: auto;">
                                <a class="dropdown-item proveedor-item" data-ruc="">Todos</a>
                                @foreach ($proveedores as $proveedor)
                                    <a class="dropdown-item proveedor-item"
                                        data-ruc="{{ $proveedor->ruc }}">{{ $proveedor->nombre }}</a>
                                @endforeach
                            </div>
                        </div>

                        <div class="dropdown mb-3 mb-sm-0 mr-sm-3">
                            <button id="btnExportar" class="btn btn-default dropdown-toggle" type="button"
                                data-toggle="dropdown">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                            <div class="dropdown-menu shadow border-0">
                                <a href="" id="exportExcel" class="dropdown-item" data-export='excel'>
                                    <i class="fas fa-file-excel text-success mr-1"></i> Excel
                                </a>
                                <a href="" id="exportPdf" class="dropdown-item" data-export='pdf'>
                                    <i class="fas fa-file-pdf text-danger mr-1"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="d-flex align-items-center justify-content-center justify-content-sm-start">
                    <span class="text-muted">Mostrar</span>
                    <select id="customLength" class="form-control mx-2 h-auto w-auto">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span class="text-muted">registros</span>
                </div>
                {{-- INFO + PAGINACIÓN (ARRIBA) --}}
                <div class="d-flex justify-content-between mb-2">
                    <div id="dt-info-top"></div>
                    <div id="dt-paging-top"></div>
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

                {{-- INFO + PAGINACIÓN (ABAJO) --}}
                <div class="d-flex justify-content-between mt-2">
                    <div id="dt-info-bottom"></div>
                    <div id="dt-paging-bottom"></div>
                </div>

            </div>
        </div>
    </div>
@stop

<!-- MODAL PARA EDITAR PRODUCTO -->
<div class="modal fade" id="modalEditarProducto" tabindex="-1" role="dialog"
    aria-labelledby="modalEditarProductoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalEditarProductoLabel">Editar Producto</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contenidoModal">
                <!-- Se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

@section('js')
    <script>
        $(function() {

            let categoryId = '';
            let providerRuc = '';

            // Helper: attach AJAX submit to edit form inside modal
            function attachEditFormAjax() {
                const $form = $('#contenidoModal').find('form[action*="productos"]');
                if ($form.length === 0) return;

                // Avoid double-binding
                if ($form.data('ajax-bound')) return;
                $form.data('ajax-bound', true);

                $form.on('submit', function(e) {
                    const method = $form.find('input[name="_method"]').val();
                    if (method && method.toUpperCase() === 'PUT') {
                        e.preventDefault();

                        // Clear previous errors
                        $form.find('.is-invalid').removeClass('is-invalid');
                        $form.find('.invalid-feedback.dynamic-error, .text-danger.dynamic-error').remove();

                        const formData = new FormData(this);
                        const actionUrl = $form.attr('action');

                        $.ajax({
                            url: actionUrl,
                            type: 'PUT',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            success: function(resp) {
                                $('#modalEditarProducto').modal('hide');
                                table.ajax.reload();
                                alert((resp && resp.message) ? resp.message : 'Producto actualizado correctamente');
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                    const errors = xhr.responseJSON.errors;

                                    Object.keys(errors).forEach(function(field) {
                                        const baseField = field.replace(/\..*$/, '');
                                        const message = errors[field][0];
                                        let $input = $form.find('[name="' + field + '"]');
                                        
                                        if ($input.length === 0) {
                                            $input = $form.find('[name="' + baseField + '"]');
                                        }
                                        if ($input.length === 0 && baseField.includes('_')) {
                                            $input = $form.find('[name="' + baseField + '[]"]');
                                        }

                                        if ($input.length) {
                                            $input.addClass('is-invalid');
                                            
                                            // Determinar tipo de elemento de error según el campo (igual que Blade)
                                            if (baseField === 'precio_unitario' || baseField === 'precio_oferta') {
                                                // Para precios: <small class="text-danger"> después del input-group o input
                                                const $target = $input.closest('.input-group').length ? $input.closest('.input-group') : $input;
                                                $('<small class="text-danger dynamic-error d-block"></small>').text(message).insertAfter($target);
                                            } else if (baseField === 'proveedor_ruc') {
                                                // Para proveedores: <span class="text-danger small font-italic"> después de la tabla
                                                const $table = $form.find('#tablaProveedores');
                                                if ($table.length) {
                                                    $('<span class="text-danger small font-italic dynamic-error d-block"></span>').text(message).insertAfter($table);
                                                }
                                            } else {
                                                // Para campos normales: <span class="invalid-feedback"> después del input
                                                $('<span class="invalid-feedback dynamic-error"></span>').text(message).insertAfter($input);
                                            }
                                        } else {
                                            // Fallback general error at top of modal body
                                            $('<div class="alert alert-danger dynamic-error mb-2"></div>').text(message).prependTo('#contenidoModal');
                                        }
                                    });
                                } else {
                                    alert('Error al actualizar el producto');
                                }
                            }
                        });
                    }
                });
            }

            const table = $('#tablaProductos').DataTable({
                processing: true,
                serverSide: false,
                autoWidth: false,
                paging: true,
                info: true,
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },

                pageLength: 10,

                ajax: {
                    url: '{{ route('productos.datatables') }}',
                    data: function(d) {
                        d.categoryid = categoryId;
                        d.provider_ruc = providerRuc;
                    }
                },

                columns: [{
                        data: 'codigo_barras'
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'categoria'
                    },
                    {
                        data: 'cantidad_stock'
                    },
                    {
                        data: 'precio_unitario',
                        render: function(data) {
                            return parseFloat(data).toFixed(2);
                        }
                    },
                    {
                        data: 'iva'
                    },
                    {
                        data: 'proveedores'
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            if (!data) return '';

                            let f = new Date(data);

                            let d = String(f.getDate()).padStart(2, '0');
                            let m = String(f.getMonth() + 1).padStart(2, '0');
                            let y = f.getFullYear();

                            let h = f.getHours();
                            let min = String(f.getMinutes()).padStart(2, '0');
                            let ampm = h >= 12 ? 'pm' : 'am';
                            h = h % 12 || 12;

                            return `${d}-${m}-${y} ${h}:${min}${ampm}`;
                        }
                    },
                    {
                        data: 'acciones',
                        orderable: false,
                        searchable: false
                    }
                ],

                dom: 'rtip',

                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json'
                },

                drawCallback: function() {
                    $('#dt-info-top').html($('.dataTables_info'));
                    $('#dt-info-bottom').html($('.dataTables_info'));

                    $('#dt-paging-top').html($('.dataTables_paginate'));
                    $('#dt-paging-bottom').html($('.dataTables_paginate'));

                    // Inicializar tooltips
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            $('#customSearch').on('input search', function() {
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

            $('.proveedor-item').on('click', function() {
                providerRuc = $(this).data('ruc');
                $('#btnProveedores').html('<i class="fas fa-filter mr-1 text-muted"></i> ' + $(this)
                    .text());
                table.ajax.reload();
            });

            // Exportar PDF con filtros
            $('#exportPdf').on('click', function(e) {
                e.preventDefault();
                
                // Construir la URL con los parámetros de filtro
                let url = '{{ route('productos.export-pdf') }}';
                let params = [];
                
                if (categoryId !== '') {
                    params.push('categoria_id=' + categoryId);
                }
                
                if (providerRuc !== '') {
                    params.push('proveedor_ruc=' + providerRuc);
                }
                
                if (params.length > 0) {
                    url += '?' + params.join('&');
                }
                
                // Abrir en nueva pestaña
                window.open(url, '_blank');
            });

            // Exportar Excel con filtros
            $('#exportExcel').on('click', function(e) {
                e.preventDefault();
                
                // Construir la URL con los parámetros de filtro
                let url = '{{ route('productos.export-excel') }}';
                let params = [];
                
                if (categoryId !== '') {
                    params.push('categoria_id=' + categoryId);
                }
                
                if (providerRuc !== '') {
                    params.push('proveedor_ruc=' + providerRuc);
                }
                
                if (params.length > 0) {
                    url += '?' + params.join('&');
                }
                
                // Redirigir para descargar el archivo
                window.location.href = url;
            });

            // Editar producto
            $(document).on('click', '.btnEditProducto', function() {
                const productoId = $(this).data('id');

                $.ajax({
                    url: '/productos/' + productoId + '/edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#contenidoModal').html(data.html);
                        $('#modalEditarProducto').modal('show');

                        // Re-inicializar scripts del formulario
                        setTimeout(() => {
                            inicializarProveedores();
                            togglePrecioOferta();
                            attachEditFormAjax();
                        }, 100);
                    },
                    error: function() {
                        alert('Error al cargar el formulario de edición');
                    }
                });
            });

            // Eliminar producto
            $(document).on('click', '.btnDeleteProducto', function() {
                const productoId = $(this).data('id');
                const productoNombre = $(this).data('nombre');

                if (confirm('¿Estás seguro de que deseas eliminar el producto "' + productoNombre +
                        '"? Esta acción no se puede deshacer.')) {
                    $.ajax({
                        url: '/productos/' + productoId,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function() {
                            table.ajax.reload();
                            // Mostrar alerta de éxito
                            alert('Producto eliminado correctamente');
                        },
                        error: function() {
                            alert('Error al eliminar el producto');
                        }
                    });
                }
            });

            // Inicializar proveedores cuando se carga la página
            inicializarProveedores();

        });
    </script>
@stop
