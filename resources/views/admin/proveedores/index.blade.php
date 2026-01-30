@extends('layouts.app')

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
                <button class="btn btn-primary px-4 shadow-sm" data-toggle="collapse" data-target="#formProveedor">
                    <i class="fas fa-plus mr-2"></i> Registrar proveedor
                </button>
            </div>
        </div>

        {{-- FORMULARIO --}}
        <div id="formProveedor" class="collapse {{ $errors->any() ? 'show' : '' }}">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header">
                    <p class="text-dark mb-0">Ingrese los datos del proveedor</p>
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

                {{-- CONTROLES SUPERIORES --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">

                    <div class="d-flex flex-column flex-sm-row align-items-center mb-3 mb-md-0">

                        {{-- BUSCADOR --}}
                        <div class="form-group mb-2 mb-sm-0 mr-sm-3 d-flex align-items-center">
                            <label class="mb-0 mr-2">Buscar:</label>
                            <input type="search" id="customSearch" class="form-control" placeholder="Filtro...">
                        </div>

                        <div class="dropdown mb-3 mb-sm-0 mr-sm-3">
                            <button id="btnExportarProveedores" class="btn btn-default dropdown-toggle" type="button"
                                data-toggle="dropdown">
                                <i class="fas fa-download"></i> Exportar
                            </button>
                            <div class="dropdown-menu shadow border-0">
                                <a href="" id="exportProveedoresExcel" class="dropdown-item" data-export='excel'>
                                    <i class="fas fa-file-excel text-success mr-1"></i> Excel
                                </a>
                                <a href="" id="exportProveedoresPdf" class="dropdown-item" data-export='pdf'>
                                    <i class="fas fa-file-pdf text-danger mr-1"></i> PDF
                                </a>
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

                {{-- INFO + PAGINACIÓN (ARRIBA) --}}
                <div class="d-flex justify-content-between mb-2">
                    <div id="dt-info-top"></div>
                    <div id="dt-paging-top"></div>
                </div>

                <div class="table-responsive">
                    <table id="tablaProveedores" class="table table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>RUC</th>
                                <th>Razón social</th>
                                <th>Email</th>
                                <th>Teléfono principal</th>
                                <th>Teléfono secundario</th>
                                <th>Direcciones</th>
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

<!-- MODAL PARA EDITAR PROVEEDOR -->
<div class="modal fade" id="modalEditarProveedor" tabindex="-1" role="dialog"
    aria-labelledby="modalEditarProveedorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="modalEditarProveedorLabel">Editar Proveedor</h5>
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
                const $form = $('#contenidoModal').find('form[action*="proveedores"]');
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
                                $('#modalEditarProveedor').modal('hide');
                                table.ajax.reload();
                                alert((resp && resp.message) ? resp.message : 'Proveedor actualizado correctamente');
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
                                    alert('Error al actualizar el proveedor');
                                }
                            }
                        });
                    }
                });
            }

            const table = $('#tablaProveedores').DataTable({
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

                ajax: "{{ route('proveedores.datatables') }}",

                columns: [{
                        data: 'ruc'
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'telefono_principal'
                    },
                    {
                        data: 'telefono_secundario'
                    },
                    {
                        data: 'direcciones',
                        orderable: false,
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

            $('#customSearch').on('input search', function() {
                table.search(this.value).draw();
            });

            $('#customLength').on('change', function() {
                table.page.len(this.value).draw();
            });

            // Exportar PDF
            $('#exportProveedoresPdf').on('click', function(e) {
                e.preventDefault();
                window.open('{{ route('proveedores.export-pdf') }}', '_blank');
            });

            // Exportar Excel
            $('#exportProveedoresExcel').on('click', function(e) {
                e.preventDefault();
                window.location.href = '{{ route('proveedores.export-excel') }}';
            });

            // Editar proveedor
            $(document).on('click', '.btnEditProveedor', function() {
                const proveedorId = $(this).data('id');

                $.ajax({
                    url: '/proveedores/' + proveedorId + '/edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#contenidoModal').html(data.html);
                        $('#modalEditarProveedor').modal('show');

                        setTimeout(() => {
                            attachEditFormAjax();
                        }, 100);
                    }
                });
            });

        });
    </script>
    <script>
        let indexDireccion = {{ count(old('direcciones', [0])) }};

        // Event delegation para agregar direcciones en ambos contextos (create y edit)
        $(document).on('click', '#btnAddDireccion', function(e) {
            e.preventDefault();

            // Buscar el contenedor de direcciones (puede estar en padre directo o más arriba)
            let $container = $(this).siblings('#direcciones-container');
            
            if ($container.length === 0) {
                $container = $(this).closest('.d-flex').nextAll('#direcciones-container').first();
            }
            
            if ($container.length === 0) {
                $container = $(this).closest('.col-md-12').siblings('#direcciones-container');
            }
            
            if ($container.length === 0) {
                console.error('No se encontró el contenedor de direcciones');
                return;
            }

            // Calcular el índice basado en las direcciones existentes en este contenedor
            const currentCount = $container.find('.direccion-item').length;
            const newIndex = currentCount;

            const html = `
            <div class="direccion-item border rounded p-2 mb-2 position-relative">
                <button type="button"
                    class="btn btn-sm btn-danger position-absolute"
                    style="top:5px; right:5px"
                    onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>

                <p class="text-muted d-block mb-1">Dirección adicional</p>

                <div class="form-row align-items-start">

                    <div class="form-group col-md-3">
                        <input type="text"
                            class="form-control"
                            name="direcciones[${newIndex}][provincia]"
                            placeholder="Provincia">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text"
                            class="form-control"
                            name="direcciones[${newIndex}][ciudad]"
                            placeholder="Ciudad">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text"
                            class="form-control"
                            name="direcciones[${newIndex}][calle]"
                            placeholder="Calle">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text"
                            class="form-control"
                            name="direcciones[${newIndex}][referencia]"
                            placeholder="Referencia">
                    </div>

                </div>
            </div>`;

            $container.append(html);
        });
    </script>
@stop
