@extends('layouts.app')

@section('title', 'Categorías')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12">
                <h1 class="m-0 text-dark font-weight-bold">Lista de categorias</h1>
                <p class="text-muted mb-0">Se muestra las categorías disponibles para organizar los productos del inventario
                </p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <button class="btn btn-primary px-4 shadow-sm" data-toggle="collapse" data-target="#formProveedor">
                    <i class="fas fa-plus mr-2"></i> Registrar categoría
                </button>
            </div>
        </div>

        {{-- FORMULARIO --}}
        <div id="formProveedor" class="collapse {{ $errors->any() ? 'show' : '' }}">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <p class="text-dark mb-0">Ingrese la información correspondiente</p>
                </div>
                <div class="card-body">
                    @include('admin.inventario.categorias.partials.form')
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
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">

                    <div class="d-flex flex-column flex-sm-row align-items-center mb-3 mb-md-0">

                        {{-- BUSCADOR --}}
                        <div class="form-group mb-2 mb-sm-0 mr-sm-3 d-flex align-items-center">
                            <label class="mb-0 mr-2">Buscar:</label>
                            <input type="search" id="customSearch" class="form-control" placeholder="Filtro...">
                        </div>

                        {{-- EXPORTACIONES
                        <div class="form-group mb-2 mb-sm-0 mr-sm-3">
                            <button class="btn btn-default mr-2">
                                <i class="fas fa-file-excel text-success"></i> Excel
                            </button>
                            <button class="btn btn-default">
                                <i class="fas fa-file-pdf text-danger"></i> PDF
                            </button>
                        </div> --}}
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

                {{-- INFO + PAGINACIÓN (ARRIBA) --}}
                <div class="d-flex justify-content-between mb-2">
                    <div id="dt-info-top"></div>
                    <div id="dt-paging-top"></div>
                </div>

                <div class="table-responsive">
                    <table id="tablaCategorias" class="table table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Productos</th>
                                <th>Fecha de registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>

                </div>
                {{-- TABLA --}}

                {{-- INFO + PAGINACIÓN (ABAJO) --}}
                <div class="d-flex justify-content-between mt-2">
                    <div id="dt-info-bottom"></div>
                    <div id="dt-paging-bottom"></div>
                </div>

            </div>
        </div>
    </div>
@stop

<!-- MODAL PARA EDITAR CATEGORÍA -->
<div class="modal fade" id="modalEditarCategoria" tabindex="-1" role="dialog"
    aria-labelledby="modalEditarCategoriaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalEditarCategoriaLabel">Editar Categoría</h5>
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

            // Helper: attach AJAX submit to edit form inside modal
            function attachEditFormAjax() {
                const $form = $('#contenidoModal').find('form[action*="categorias"]');
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
                        $form.find('.invalid-feedback.dynamic-error').remove();

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
                                $('#modalEditarCategoria').modal('hide');
                                window.categoriasTable.ajax.reload();
                                alert((resp && resp.message) ? resp.message : 'Categoría actualizada correctamente');
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                                    const errors = xhr.responseJSON.errors;

                                    Object.keys(errors).forEach(function(field) {
                                        const message = errors[field][0];
                                        const $input = $form.find('[name="' + field + '"]');

                                        if ($input.length) {
                                            $input.addClass('is-invalid');
                                            $('<span class="invalid-feedback dynamic-error"></span>').text(message).insertAfter($input);
                                        } else {
                                            $('<div class="alert alert-danger dynamic-error mb-2"></div>').text(message).prependTo('#contenidoModal');
                                        }
                                    });
                                } else {
                                    alert('Error al actualizar la categoría');
                                }
                            }
                        });
                    }
                });
            }

            let table = $('#tablaCategorias').DataTable({
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

                ajax: "{{ route('admin.categorias.datatables') }}",

                columns: [{
                        data: 'nombre'
                    },
                    {
                        data: 'descripcion'
                    },
                    {
                        data: 'productos_count',
                        orderable: true,
                        searchable: false
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
                    url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
                },

                drawCallback: function() {
                    $('#dt-info-top').html($('.dataTables_info'));
                    $('#dt-info-bottom').html($('.dataTables_info'));

                    $('#dt-paging-top').html($('.dataTables_paginate'));
                    $('#dt-paging-bottom').html($('.dataTables_paginate'));
                }

            });

            // Búsqueda personalizada
            $('#customSearch').on('input search', function() {
                table.search($(this).val()).draw();
            });

            // Cambiar cantidad de registros
            $('#customLength').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Editar categoría
            $(document).on('click', '.btnEditCategoria', function() {
                const categoriaId = $(this).data('id');

                $.ajax({
                    url: 'categorias/' + categoriaId + '/edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#contenidoModal').html(data.html);
                        $('#modalEditarCategoria').modal('show');

                        setTimeout(() => {
                            attachEditFormAjax();
                        }, 100);
                    }
                });
            });

            // Eliminar categoría (soft delete)
            $(document).on('click', '.btnDeleteCategoria', function() {
                const categoriaId = $(this).data('id');
                const categoriaNombre = $(this).data('name') || 'esta categoría';

                if (!confirm('¿Estás seguro de que deseas eliminar "' + categoriaNombre + '"? Esta acción la desactivará.')) {
                    return;
                }

                $.ajax({
                    url: "{{ url('admin/categorias') }}/" + categoriaId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json'
                    },
                    success: function(resp) {
                        window.categoriasTable.ajax.reload();
                        alert((resp && resp.message) ? resp.message : 'Categoría desactivada correctamente');
                    },
                    error: function(xhr) {
                        let errorMsg = 'Error al eliminar la categoría';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    }
                });
            });

            // Exponer tabla globalmente para refresh después de actualizar
            window.categoriasTable = table;

        });
    </script>
@stop