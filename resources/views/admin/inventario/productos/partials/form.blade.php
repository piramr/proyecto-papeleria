<form action="{{ isset($producto) ? route('productos.update', $producto->id) : route('productos.store') }}" method="POST">
    @csrf
    @if(isset($producto))
        @method('PUT')
    @endif

    <!-- SECCIÓN 1: INFORMACIÓN DEL PRODUCTO -->
    <div class="mb-4 pb-4 border-bottom">
        <h5 class="mb-3 text-dark font-weight-bold">Información del Producto</h5>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="codigo_barras">Código de barras</label>
                <input type="text" class="form-control @error('codigo_barras') is-invalid @enderror" 
                    id="codigo_barras" name="codigo_barras" placeholder="Ej: 1234567890123" value="{{ old('codigo_barras', isset($producto) ? $producto->codigo_barras : '') }}">
                @error('codigo_barras')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label for="nombre">Nombre del producto</label>
                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                    id="nombre" name="nombre" placeholder="Nombre del producto" value="{{ old('nombre', isset($producto) ? $producto->nombre : '') }}">
                @error('nombre')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label for="categoria_id">Categoría</label>
                <select class="form-control @error('categoria_id') is-invalid @enderror" 
                    id="categoria_id" name="categoria_id">
                    <option value="">-- Seleccionar categoría --</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ old('categoria_id', isset($producto) ? $producto->categoria_id : '') == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('categoria_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="marca">Marca</label>
                <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                    id="marca" name="marca" placeholder="Marca del producto" value="{{ old('marca', isset($producto) ? $producto->marca : '') }}">
                @error('marca')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label for="caracteristicas">Características</label>
                <input type="text" class="form-control @error('caracteristicas') is-invalid @enderror" 
                    id="caracteristicas" name="caracteristicas" placeholder="Características" value="{{ old('caracteristicas', isset($producto) ? $producto->caracteristicas : '') }}">
                @error('caracteristicas')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-4">
                <label for="ubicacion">Ubicación</label>
                <input type="text" class="form-control @error('ubicacion') is-invalid @enderror" 
                    id="ubicacion" name="ubicacion" placeholder="Ubicación en almacén" value="{{ old('ubicacion', isset($producto) ? $producto->ubicacion : '') }}">
                @error('ubicacion')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <!-- SECCIÓN 2: PRECIO E INVENTARIO -->
    <div class="mb-4 pb-4 border-bottom">
        <h5 class="mb-3 text-dark font-weight-bold">Precio e Inventario</h5>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="precio_unitario">Precio unitario</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01" class="form-control @error('precio_unitario') is-invalid @enderror" 
                        id="precio_unitario" name="precio_unitario" placeholder="0.00" value="{{ old('precio_unitario', isset($producto) ? $producto->precio_unitario : '') }}">
                    @error('precio_unitario')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group col-md-3">
                <label for="cantidad_stock">Cantidad stock</label>
                <input type="number" class="form-control @error('cantidad_stock') is-invalid @enderror" 
                    id="cantidad_stock" name="cantidad_stock" placeholder="0" value="{{ old('cantidad_stock', isset($producto) ? $producto->cantidad_stock : '') }}">
                @error('cantidad_stock')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-3">
                <label for="stock_minimo">Stock mínimo</label>
                <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror" 
                    id="stock_minimo" name="stock_minimo" placeholder="0" value="{{ old('stock_minimo', isset($producto) ? $producto->stock_minimo : '') }}">
                @error('stock_minimo')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-3">
                <label>¿Tiene IVA?</label>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="tiene_iva" name="tiene_iva" 
                        value="1" {{ (old('tiene_iva') || (isset($producto) && $producto->tiene_iva)) ? 'checked' : '' }}>
                    <label class="form-check-label" for="tiene_iva">
                        Aplica IVA
                    </label>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>¿En oferta?</label>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="en_oferta" name="en_oferta" 
                        value="1" {{ (old('en_oferta') || (isset($producto) && $producto->en_oferta)) ? 'checked' : '' }} onchange="togglePrecioOferta()">
                    <label class="form-check-label" for="en_oferta">
                        Producto en oferta
                    </label>
                </div>
            </div>
        </div>

        <div class="form-row" id="precioOfertaRow" style="display: none;">
            <div class="form-group col-md-6">
                <label for="precio_oferta">Precio de oferta</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="number" step="0.01" class="form-control @error('precio_oferta') is-invalid @enderror" 
                        id="precio_oferta" name="precio_oferta" placeholder="0.00" value="{{ old('precio_oferta', isset($producto) ? $producto->precio_oferta : '') }}" @if(!(old('en_oferta') || (isset($producto) && $producto->en_oferta))) disabled @endif>
                    @error('precio_oferta')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- SECCIÓN 3: PROVEEDORES -->
    <div class="mb-4 pb-4 border-bottom">
        <h5 class="mb-3 text-dark font-weight-bold">Proveedores</h5>

        <div class="form-group">
            <label>Agregar proveedor</label>
            <div class="d-flex align-items-end">
                <div class="form-group col-md-6 pl-0">
                    <select class="form-control" id="selectProveedor">
                        <option value="">-- Seleccionar proveedor --</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->ruc }}">{{ $proveedor->nombre }} ({{ $proveedor->ruc }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" id="btnAgregarProveedor" class="btn btn-primary ml-2 mb-0">Agregar</button>
            </div>
        </div>

        <div class="form-group">
            <label>Proveedores asignados</label>
            <table class="table table-bordered table-sm" id="tablaProveedores">
                <thead>
                    <tr>
                        <th width="70%">Proveedor</th>
                        <th width="15%">RUC</th>
                        <th width="15%">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($producto) && $producto->proveedores->count() > 0)
                        @foreach($producto->proveedores as $proveedor)
                            <tr class="proveedor-row" data-ruc="{{ $proveedor->ruc }}">
                                <td>{{ $proveedor->nombre }}</td>
                                <td>{{ $proveedor->ruc }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger btnEliminarProveedor">Eliminar</button>
                                </td>
                            </tr>
                            <input type="hidden" name="proveedor_ruc[]" value="{{ $proveedor->ruc }}">
                        @endforeach
                    @endif
                </tbody>
            </table>
            @error('proveedor_ruc')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- BOTONES DE ACCIÓN -->
    <div class="form-group pt-3">
        <button type="submit" class="btn btn-primary px-4">{{ isset($producto) ? 'Actualizar' : 'Guardar' }} producto</button>
        <button type="reset" class="btn btn-secondary px-4">Limpiar</button>
        <button type="button" class="btn btn-outline-danger px-4" id="btnCancelar"
            @if(!empty($isModal)) data-dismiss="modal" @else data-toggle="collapse" data-target="#formProducto" aria-expanded="false" aria-controls="formProducto" @endif>
            Cancelar
        </button>
    </div>
</form>
</form>

<script>
function togglePrecioOferta() {
    const enOferta = document.getElementById('en_oferta');
    const row = document.getElementById('precioOfertaRow');
    const input = document.getElementById('precio_oferta');
    if (!enOferta || !row || !input) return;

    if (enOferta.checked) {
        row.style.display = 'block';
        input.disabled = false;
    } else {
        row.style.display = 'none';
        input.disabled = true;
        // limpiar valor para evitar envío accidental
        input.value = '';
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

    if (btnAgregar && !btnAgregar.dataset.addListener) {
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
                    <button type="button" class="btn btn-sm btn-danger btnEliminarProveedor">Eliminar</button>
                </td>
            `;
            tbody.appendChild(row);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'proveedor_ruc[]';
            input.value = ruc;
            const enclosingForm = btnAgregar.closest('form') || document.querySelector('form');
            if (enclosingForm) enclosingForm.appendChild(input);

            selectProveedor.value = '';
            actualizarSelectProveedores();
        });
        btnAgregar.dataset.addListener = '1';
    }

    // Delegated delete handler attached once to this table body
    if (!tbody.dataset.deleteListener) {
        tbody.addEventListener('click', function(e) {
            const btn = e.target.closest('.btnEliminarProveedor');
            if (!btn) return;

            const row = btn.closest('.proveedor-row');
            const ruc = row ? row.dataset.ruc : null;
            if (row) row.remove();

            const enclosingForm = btn.closest('form') || document.querySelector('form');
            if (enclosingForm && ruc) {
                const input = enclosingForm.querySelector(`input[name="proveedor_ruc[]"][value="${ruc}"]`);
                if (input) input.remove();
            }

            actualizarSelectProveedores();
        });
        tbody.dataset.deleteListener = '1';
    }
}

// El botón cancelar ahora usa atributos de colapso HTML o data-dismiss según el contexto (modal vs página)

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
