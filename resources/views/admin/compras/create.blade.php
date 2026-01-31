@extends('layouts.app')

@section('title', 'Nueva Compra')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Nueva Compra</h1>
        </div>
        <div class="col-sm-6">
            <a href="{{ route('compras.index') }}" class="btn btn-secondary float-right">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h4><i class="icon fas fa-ban"></i> Errores de validación</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
        @csrf

        <div class="row">
            <!-- Información General -->
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Información de la Compra</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="proveedor_ruc">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-control @error('proveedor_ruc') is-invalid @enderror" 
                                    id="proveedor_ruc" name="proveedor_ruc" required onchange="cargarProductosProveedor()">
                                <option value="">-- Seleccionar Proveedor --</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->ruc }}" {{ old('proveedor_ruc') == $proveedor->ruc ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }} ({{ $proveedor->ruc }})
                                    </option>
                                @endforeach
                            </select>
                            @error('proveedor_ruc')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fecha_compra">Fecha de Compra <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('fecha_compra') is-invalid @enderror" 
                                   id="fecha_compra" name="fecha_compra" required value="{{ old('fecha_compra', now()->format('Y-m-d\TH:i')) }}">
                            @error('fecha_compra')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tipo_pago_id">Tipo de Pago</label>
                            <select class="form-control @error('tipo_pago_id') is-invalid @enderror" 
                                    id="tipo_pago_id" name="tipo_pago_id">
                                <option value="">-- Seleccionar Tipo de Pago --</option>
                                @foreach($tiposPago as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_pago_id') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_pago_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                      id="descripcion" name="descripcion" rows="3" placeholder="Notas sobre la compra...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen -->
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">Resumen de Compra</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Subtotal</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control text-right" id="resumenSubtotal" 
                                       readonly value="0,00">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>IVA (12%)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control text-right" id="resumenIva" 
                                       readonly value="0,00">
                            </div>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label><strong>Total</strong></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control text-right" id="resumenTotal" 
                                       readonly value="0,00" style="font-size: 1.2rem; font-weight: bold;">
                            </div>
                        </div>
                        
                        <small class="text-muted">Los valores se actualizan automáticamente</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de Compra -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Detalle de Productos</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tablaDetalles">
                        <thead class="thead-light">
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="detallesBody">
                            <!-- Se llenarán dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-success mt-3" id="btnAgregarDetalle" onclick="agregarDetalle()">
                    <i class="fas fa-plus"></i> Agregar Producto
                </button>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar Compra
            </button>
            <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancelar
            </a>
        </div>
    </form>

    <script>
        let detalles = [];
        let productosProveedor = [];
        let contadorFilas = 0;

        // Cargar productos del proveedor seleccionado
        function cargarProductosProveedor() {
            const proveedorRuc = document.getElementById('proveedor_ruc').value;
            
            if (!proveedorRuc) {
                productosProveedor = [];
                limpiarDetalles();
                return;
            }

            fetch(`/admin/compras/productos-proveedor/${proveedorRuc}`)
                .then(response => response.json())
                .then(data => {
                    productosProveedor = data;
                    limpiarDetalles();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar los productos del proveedor');
                });
        }

        // Agregar fila de detalle
        function agregarDetalle() {
            const proveedorRuc = document.getElementById('proveedor_ruc').value;
            
            if (!proveedorRuc) {
                alert('Debe seleccionar un proveedor primero');
                return;
            }

            if (productosProveedor.length === 0) {
                alert('Este proveedor no tiene productos disponibles');
                return;
            }

            const filaId = 'detalle-' + (contadorFilas++);
            const html = `
                <tr id="${filaId}">
                    <td>
                        <select class="form-control producto-select" name="detalles[${contadorFilas - 1}][producto_id]" 
                                onchange="actualizarPrecio('${filaId}')" required>
                            <option value="">-- Seleccionar --</option>
                            ${productosProveedor.map(p => `<option value="${p.id}" data-precio="${p.precio_costo}" data-iva="${p.tiene_iva}">${p.nombre}</option>`).join('')}
                        </select>
                    </td>
                    <td style="width: 120px;">
                        <input type="number" class="form-control cantidad-input" name="detalles[${contadorFilas - 1}][cantidad]" 
                               min="1" value="1" onchange="calcularSubtotal('${filaId}')" required>
                    </td>
                    <td style="width: 140px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control precio-unitario" name="detalles[${contadorFilas - 1}][precio_unitario]" 
                                   step="0.01" min="0" value="0" onchange="calcularSubtotal('${filaId}')" required>
                        </div>
                    </td>
                    <td style="width: 120px;">
                        <input type="text" class="form-control text-right subtotal-display" readonly value="$0,00">
                    </td>
                    <td style="width: 80px;">
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDetalle('${filaId}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            document.getElementById('detallesBody').insertAdjacentHTML('beforeend', html);
        }

        // Actualizar precio cuando se selecciona un producto
        function actualizarPrecio(filaId) {
            const fila = document.getElementById(filaId);
            const select = fila.querySelector('.producto-select');
            const opcionSeleccionada = select.options[select.selectedIndex];
            
            if (opcionSeleccionada.value) {
                const precio = parseFloat(opcionSeleccionada.dataset.precio) || 0;
                fila.querySelector('.precio-unitario').value = precio.toFixed(2);
                calcularSubtotal(filaId);
            }
        }

        // Calcular subtotal
        function calcularSubtotal(filaId) {
            const fila = document.getElementById(filaId);
            const cantidad = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
            const precio = parseFloat(fila.querySelector('.precio-unitario').value) || 0;
            const subtotal = cantidad * precio;
            
            fila.querySelector('.subtotal-display').value = '$' + subtotal.toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            actualizarTotales();
        }

        // Actualizar totales generales
        function actualizarTotales() {
            let subtotal = 0;
            let iva = 0;

            document.querySelectorAll('#detallesBody tr').forEach(fila => {
                const cantidad = parseFloat(fila.querySelector('.cantidad-input').value) || 0;
                const precio = parseFloat(fila.querySelector('.precio-unitario').value) || 0;
                const select = fila.querySelector('.producto-select');
                const opcionSeleccionada = select.options[select.selectedIndex];
                const tieneIva = opcionSeleccionada.dataset.iva === '1';

                const subtotalFila = cantidad * precio;
                subtotal += subtotalFila;

                if (tieneIva) {
                    iva += subtotalFila * 0.12;
                }
            });

            const total = subtotal + iva;

            document.getElementById('resumenSubtotal').value = subtotal.toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('resumenIva').value = iva.toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('resumenTotal').value = total.toLocaleString('es-EC', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Eliminar fila de detalle
        function eliminarDetalle(filaId) {
            document.getElementById(filaId).remove();
            actualizarTotales();
        }

        // Limpiar todos los detalles
        function limpiarDetalles() {
            document.getElementById('detallesBody').innerHTML = '';
            contadorFilas = 0;
            actualizarTotales();
        }

        // Validar al enviar el formulario
        document.getElementById('formCompra').addEventListener('submit', function(e) {
            const detalles = document.querySelectorAll('#detallesBody tr');
            
            if (detalles.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto a la compra');
                return false;
            }

            // Validar que todos los detalles tengan datos
            let valido = true;
            detalles.forEach((fila, index) => {
                const producto = fila.querySelector('.producto-select').value;
                const cantidad = fila.querySelector('.cantidad-input').value;
                const precio = fila.querySelector('.precio-unitario').value;

                if (!producto || !cantidad || !precio) {
                    alert(`Fila ${index + 1}: Complete todos los campos`);
                    valido = false;
                }
            });

            if (!valido) {
                e.preventDefault();
            }
        });

        // Inicializar
        window.addEventListener('load', function() {
            const proveedorRuc = document.getElementById('proveedor_ruc').value;
            if (proveedorRuc) {
                cargarProductosProveedor();
            }
        });
    </script>
@stop
