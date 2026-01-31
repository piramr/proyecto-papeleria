<form action="{{ isset($producto) ? route('admin.productos.update', $producto->id) : route('admin.productos.store') }}"
    method="POST">
    @csrf
    @if (isset($producto))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <p class="card-header h5">Información general</p>
                <div class="card-body">
                    <div class="form-group">
                        <p class="mb-1">Nombre del producto <span class="text-danger">*</span></p>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre"
                            name="nombre" placeholder="Ej: Cuaderno 100 hojas"
                            value="{{ old('nombre', isset($producto) ? $producto->nombre : '') }}">
                        @error('nombre')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <p class="mb-1">Código de barras <span class="text-danger">*</span></p>
                            <input type="text" class="form-control @error('codigo_barras') is-invalid @enderror"
                                id="codigo_barras" name="codigo_barras" placeholder="SKU-001"
                                value="{{ old('codigo_barras', isset($producto) ? $producto->codigo_barras : '') }}">
                            @error('codigo_barras')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-md-6">
                            <p class="mb-1">Categoría <span class="text-danger">*</span></p>
                            <select class="form-control @error('categoria_id') is-invalid @enderror" id="categoria_id"
                                name="categoria_id">
                                <option value="">-- Seleccionar categoría --</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ old('categoria_id', isset($producto) ? $producto->categoria_id : '') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-md-6">
                            <p class="mb-1">Marca <span class="text-danger">*</span></p>
                            <input type="text" class="form-control @error('marca') is-invalid @enderror"
                                id="marca" name="marca"
                                value="{{ old('marca', isset($producto) ? $producto->marca : '') }}">
                            @error('marca')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <p class="mb-1">Ubicación en almacén</p>
                            <input type="text" class="form-control @error('ubicacion') is-invalid @enderror"
                                id="ubicacion" name="ubicacion"
                                value="{{ old('ubicacion', isset($producto) ? $producto->ubicacion : '') }}">
                            @error('ubicacion')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <p class="mb-1">Características</p>
                        <textarea class="form-control @error('caracteristicas') is-invalid @enderror" id="caracteristicas" maxlength="255" placeholder="Máximo 250 carácteres"
                            name="caracteristicas" rows="8">{{ old('caracteristicas', isset($producto) ? $producto->caracteristicas : '') }}</textarea>
                        @error('caracteristicas')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <p class="card-header h5">Precio producto</p>
                <div class="card-body">
                    <div class="form-group">
                        <p class="mb-1">Precio unitario <span class="text-danger">*</span></p>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text font-weight-bold">$</span>
                            </div>
                            <input type="number" step="0.01"
                                class="form-control @error('precio_unitario') is-invalid @enderror" id="precio_unitario"
                                name="precio_unitario"
                                value="{{ old('precio_unitario', isset($producto) ? $producto->precio_unitario : '') }}">
                        </div>
                        @error('precio_unitario')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="hidden" name="tiene_iva" value="0">
                        <input class="form-check-input" type="checkbox" id="tiene_iva" name="tiene_iva" value="1"
                            {{ old('tiene_iva', isset($producto) ? $producto->tiene_iva : false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="tiene_iva">Aplica IVA</label>
                    </div>
                    <hr>
                    <div class="bg-light p-3 border rounded">
                        <div class="mb-2">
                            <input type="hidden" name="en_oferta" value="0">
                            <input class="form-input" type="checkbox" id="en_oferta" name="en_oferta" value="1"
                                {{ old('en_oferta', isset($producto) ? $producto->en_oferta : false) ? 'checked' : '' }}
                                onchange="togglePrecioOferta()">
                            <label for="en_oferta" style="font-weight: normal">En oferta</label>
                        </div>

                        <div class="form-group mb-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white">Precio $</span>
                                </div>
                                <input type="number" step="0.01"
                                    class="form-control @error('precio_oferta') is-invalid @enderror"
                                    id="precio_oferta" name="precio_oferta"
                                    value="{{ old('precio_oferta', isset($producto) ? $producto->precio_oferta : '') }}"
                                    {{ old('en_oferta', isset($producto) ? $producto->en_oferta : false) ? '' : 'disabled' }}>
                            </div>
                            @error('precio_oferta')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <p class="card-header h5">Inventario stock</p>
                <div class="card-body">
                    <div class="row">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <p class="input-group-text bg-white" style="min-width: 90px">Actual<span class="text-danger"> *</span></p>
                            </div>
                            <input type="number" class="form-control @error('cantidad_stock') is-invalid @enderror"
                                id="cantidad_stock" name="cantidad_stock"
                                value="{{ old('cantidad_stock', isset($producto) ? $producto->cantidad_stock : '') }}">
                            @error('cantidad_stock')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <p class="input-group-text bg-white" style="min-width: 90px">Mínimo <span class="text-danger">*</span></p>
                            </div>
                            <input type="number" class="form-control @error('stock_minimo') is-invalid @enderror"
                                id="stock_minimo" name="stock_minimo"
                                value="{{ old('stock_minimo', isset($producto) ? $producto->stock_minimo : '') }}">
                            @error('stock_minimo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <p class="input-group-text bg-white" style="min-width: 90px">Máximo <span class="text-danger">*</span></p>
                            </div>
                            <input type="number" class="form-control @error('stock_maximo') is-invalid @enderror"
                                id="stock_maximo" name="stock_maximo"
                                value="{{ old('stock_maximo', isset($producto) ? $producto->stock_maximo : '') }}">
                            @error('stock_maximo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <p class="card-header h5">Proveedores</p>
                <div class="card-body">
                    <div class="row align-items-end mb-4">
                        <div class="form-group col-md-5 mb-0">
                            <select class="form-control" id="selectProveedor">
                                <option value="">-- Seleccione proveedor --</option>
                                @foreach ($proveedores as $proveedor)
                                    <option value="{{ $proveedor->ruc }}">{{ $proveedor->nombre }}
                                        ({{ $proveedor->ruc }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="btnAgregarProveedor"
                                class="btn btn-info btn-block">Asociar</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tablaProveedores">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>Proveedor</th>
                                    <th>RUC</th>
                                    <th width="100px" class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($producto) && $producto->proveedores->count() > 0)
                                    @foreach ($producto->proveedores as $proveedor)
                                        <tr class="proveedor-row" data-ruc="{{ $proveedor->ruc }}">
                                            <td>{{ $proveedor->nombre }}</td>
                                            <td>{{ $proveedor->ruc }}</td>
                                            <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btnEliminarProveedor">Eliminar</button>
                                            </td>
                                        </tr>
                                        <input type="hidden" name="proveedor_ruc[]" value="{{ $proveedor->ruc }}">
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        @error('proveedor_ruc')
                            <span class="text-danger small font-italic">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-right">
            <button type="button" class="btn btn-secondary px-3 mr-2"
                @if (!empty($isModal)) data-dismiss="modal" @else data-toggle="collapse" data-target="#formProducto" @endif>
                Cancelar
            </button>
            <button type="submit" class="btn btn-primary px-3 shadow-sm">
                <i class="fas fa-save mr-2"></i> {{ isset($producto) ? 'Actualizar' : 'Guardar' }}
            </button>
        </div>
    </div>
</form>

<script>
    function togglePrecioOferta() {
        const enOferta = document.getElementById('en_oferta');
        const input = document.getElementById('precio_oferta');

        if (!enOferta || !input) return;

        if (enOferta.checked) {
            input.disabled = false;
            input.focus();
        } else {
            input.disabled = true;
            input.value = ''; // Limpia el valor si se desactiva
        }
    }

    function actualizarSelectProveedores(form) {
        const tbody = form.querySelector('#tablaProveedores tbody');
        const proveedoresSeleccionados = [];

        tbody.querySelectorAll('.proveedor-row').forEach(row => {
            proveedoresSeleccionados.push(row.dataset.ruc);
        });

        const select = form.querySelector('#selectProveedor');
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

        // Soportar múltiples formularios (create y modal) sin cruzar estados
        const forms = document.querySelectorAll('form[action*="productos"]');

        forms.forEach(form => {
            const selectProveedor = form.querySelector('#selectProveedor');
            const btnAgregar = form.querySelector('#btnAgregarProveedor');
            const tabla = form.querySelector('#tablaProveedores');

            if (!tabla || !selectProveedor || !btnAgregar) return;

            const tbody = tabla.querySelector('tbody');

            actualizarSelectProveedores(form);

            if (btnAgregar && !btnAgregar.dataset.addListener) {
                btnAgregar.addEventListener('click', function(e) {
                    e.preventDefault();
                    const ruc = selectProveedor.value;
                    const nombre = selectProveedor.options[selectProveedor.selectedIndex].text;

                    if (!ruc) {
                        alert('Selecciona un proveedor');
                        return;
                    }

                    if (form.querySelector(`[data-ruc="${ruc}"]`)) {
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
                    form.appendChild(input);

                    selectProveedor.value = '';
                    actualizarSelectProveedores(form);
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

                    if (ruc) {
                        const input = form.querySelector(`input[name="proveedor_ruc[]"][value="${ruc}"]`);
                        if (input) input.remove();
                    }

                    actualizarSelectProveedores(form);
                });
                tbody.dataset.deleteListener = '1';
            }
        });
    }

    // El botón cancelar ahora usa atributos de colapso HTML o data-dismiss según el contexto (modal vs página)

    // Inicializar proveedores cuando se carga el formulario (tanto en página como en modal)
    document.addEventListener('DOMContentLoaded', function() {
        inicializarProveedores();
    });
</script>
