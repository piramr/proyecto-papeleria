@extends('adminlte::page')

@section('title', 'Editar Producto')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-12">
                <h1 class="m-0 text-dark font-weight-bold">Editar producto</h1>
                <p class="text-muted mb-0">Actualiza la información del producto</p>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                @include('admin.inventario.productos.partials.form')
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function togglePrecioOferta() {
            const enOferta = document.getElementById('en_oferta');
            if (enOferta) {
                document.getElementById('precioOfertaRow').style.display = enOferta.checked ? 'block' : 'none';
            }
        }

        function actualizarSelectProveedores() {
            const proveedoresSeleccionados = [];
            document.querySelectorAll('#tablaProveedores .proveedor-row').forEach(row => {
                proveedoresSeleccionados.push(row.dataset.ruc);
            });

            const select = document.getElementById('selectProveedor');
            if (select) {
                Array.from(select.options).forEach(option => {
                    if (option.value) {
                        option.style.display = proveedoresSeleccionados.includes(option.value) ? 'none' : 'block';
                    }
                });
            }
        }

        function inicializarProveedores() {
            togglePrecioOferta();
            actualizarSelectProveedores();

            const selectProveedor = document.getElementById('selectProveedor');
            const btnAgregar = document.getElementById('btnAgregarProveedor');
            const tabla = document.getElementById('tablaProveedores');

            if (!tabla) return;

            const tbody = tabla.querySelector('tbody');

            if (btnAgregar) {
                btnAgregar.addEventListener('click', function(e) {
                    e.preventDefault();
                    const ruc = selectProveedor.value;
                    const nombre = selectProveedor.options[selectProveedor.selectedIndex].text;

                    if (!ruc) {
                        alert('Selecciona un proveedor');
                        return;
                    }

                    if (document.querySelector(`[data-ruc="${ruc}"]`)) {
                        alert('Este proveedor ya está agregado');
                        return;
                    }

                    const row = document.createElement('tr');
                    row.className = 'proveedor-row';
                    row.dataset.ruc = ruc;
                    row.innerHTML = `
                        <td>${nombre.split('(')[0].trim()}</td>
                        <td>${ruc}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger btnEliminarProveedor">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'proveedor_ruc[]';
                    input.value = ruc;
                    document.querySelector('form').appendChild(input);

                    selectProveedor.value = '';
                    actualizarSelectProveedores();
                });
            }

            document.addEventListener('click', function(e) {
                if (e.target.closest('.btnEliminarProveedor')) {
                    const row = e.target.closest('.proveedor-row');
                    const ruc = row.dataset.ruc;
                    
                    row.remove();
                    const input = document.querySelector(`input[name="proveedor_ruc[]"][value="${ruc}"]`);
                    if (input) input.remove();
                    
                    actualizarSelectProveedores();
                }
            });
        }

        // Manejador para botón de cancelar
        document.addEventListener('click', function(e) {
            if (e.target.closest('#btnCancelar')) {
                e.preventDefault();
                
                // Si estamos en un modal, cerrarlo
                if (document.getElementById('modalEditarProducto') && $('#modalEditarProducto').hasClass('show')) {
                    $('#modalEditarProducto').modal('hide');
                } else {
                    // Si es un formulario colapsado, ocultarlo o volver a la página
                    const formProducto = document.getElementById('formProducto');
                    if (formProducto) {
                        formProducto.classList.remove('show');
                    } else {
                        // Volver a la lista de productos
                        window.location.href = '{{ route('admin.productos') }}';
                    }
                }
            }
        });

        // Manejador para envío del formulario en modal
        document.addEventListener('DOMContentLoaded', function() {
            inicializarProveedores();
            
            // Si estamos en un modal, manejamos el envío con AJAX
            const form = document.querySelector('form[action*="productos"]');
            if (form && document.getElementById('modalEditarProducto')) {
                form.addEventListener('submit', function(e) {
                    // Solo si es un UPDATE (contiene el método PUT)
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput && methodInput.value === 'PUT') {
                        e.preventDefault();
                        
                        const formData = new FormData(form);
                        const url = form.action;
                        
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#modalEditarProducto').modal('hide');
                                table.ajax.reload();
                                // Mostrar alerta de éxito
                                alert('Producto actualizado correctamente');
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    const errors = xhr.responseJSON.errors;
                                    let errorMsg = 'Errores de validación:\n';
                                    for (let field in errors) {
                                        errorMsg += errors[field][0] + '\n';
                                    }
                                    alert(errorMsg);
                                } else {
                                    alert('Error al actualizar el producto');
                                }
                            }
                        });
                    }
                });
            }
        });

        // Re-ejecutar al cargar en modal
        window.addEventListener('contenidoModalActualizado', function() {
            inicializarProveedores();
        });
    </script>
@stop

