@extends('layouts.app')

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
<div class="container-fluid">
    <h1 class="mb-4">Nueva Venta</h1>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Errores de validación:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <form id="ventasForm" action="{{ route('admin.ventas.store') }}" method="POST">
                @csrf

                <!-- Sección: Datos del Cliente -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Datos del Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cliente_cedula" class="form-label">Cédula/RUC *</label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('cliente_cedula') is-invalid @enderror" 
                                           id="cliente_cedula" name="cliente_cedula" value="{{ old('cliente_cedula') }}" required
                                           placeholder="Ingresa cédula y presiona Tab o Enter">
                                    <button type="button" class="btn btn-outline-secondary" id="btnBuscarCliente" title="Buscar cliente">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div id="avisoCliente" class="small mt-2"></div>
                                @error('cliente_cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cliente_nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control @error('cliente_nombres') is-invalid @enderror" 
                                       id="cliente_nombres" name="cliente_nombres" value="{{ old('cliente_nombres') }}" required>
                                @error('cliente_nombres')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cliente_apellidos" class="form-label">Apellidos *</label>
                                <input type="text" class="form-control @error('cliente_apellidos') is-invalid @enderror" 
                                       id="cliente_apellidos" name="cliente_apellidos" value="{{ old('cliente_apellidos') }}" required>
                                @error('cliente_apellidos')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cliente_email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('cliente_email') is-invalid @enderror" 
                                       id="cliente_email" name="cliente_email" value="{{ old('cliente_email') }}">
                                @error('cliente_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cliente_telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="cliente_telefono" name="cliente_telefono" 
                                       value="{{ old('cliente_telefono') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cliente_fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                                <input type="date" class="form-control" id="cliente_fecha_nacimiento" name="cliente_fecha_nacimiento" 
                                       value="{{ old('cliente_fecha_nacimiento') }}">
                                <div id="avisoMenor" class="form-text"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <input type="hidden" id="consumidor_final" name="consumidor_final" value="0">
                                <button type="button" class="btn btn-outline-secondary" id="btnConsumidorFinal">
                                    <i class="fas fa-user-tag"></i> Consumidor Final
                                </button>
                                <button type="button" class="btn btn-outline-danger ms-2 d-none" id="btnRestaurarCliente">
                                    <i class="fas fa-undo"></i> Restaurar datos
                                </button>
                                <div class="form-text">Usa Consumidor Final cuando el cliente no desea factura con datos.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numero_factura" class="form-label">Número de Factura *</label>
                                    <input type="text" class="form-control @error('numero_factura') is-invalid @enderror" 
                                        id="numero_factura" name="numero_factura" value="{{ old('numero_factura') }}" 
                                        placeholder="{{ $sugerenciaFactura ?? 'Ej. 001-001-000000001' }}"
                                        data-sugerencia="{{ $sugerenciaFactura ?? '' }}" required>
                                <small class="form-text text-muted">Número secuencial único para el SRI</small>
                                @if(!empty($ultimaFactura))
                                    <small class="form-text text-info">Última factura registrada: {{ $ultimaFactura }}</small>
                                @endif
                                @if(!empty($sugerenciaFactura))
                                    <small class="form-text text-info">Sugerencia: {{ $sugerenciaFactura }}</small>
                                @endif
                                @error('numero_factura')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección: Productos -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-box"></i> Productos</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="producto_select" class="form-label">Seleccionar Producto</label>
                                <select class="form-select" id="producto_select">
                                    <option value="">-- Selecciona un producto --</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_unitario }}" 
                                                data-stock="{{ $producto->cantidad_stock }}" data-nombre="{{ $producto->nombre }}">
                                            {{ $producto->nombre }} - Stock: {{ $producto->cantidad_stock }} - ${{ number_format($producto->precio_unitario, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" min="1" value="1">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-success w-100" id="agregarProducto">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm" id="tablaProdutos">
                                <thead class="table-light">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="detallesVenta">
                                    <tr id="sinProductos" class="text-center text-muted">
                                        <td colspan="5">Sin productos agregados</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Campos ocultos para los productos -->
                <div id="productosContainer"></div>

                <!-- Sección: Forma de Pago -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-credit-card"></i> Forma de Pago</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tipo_pago_id" class="form-label">Tipo de Pago *</label>
                            <select class="form-select @error('tipo_pago_id') is-invalid @enderror" 
                                    id="tipo_pago_id" name="tipo_pago_id" required>
                                <option value="">-- Selecciona una forma de pago --</option>
                                @foreach($tiposPago as $tipo)
                                    <option value="{{ $tipo->id }}" @selected(old('tipo_pago_id', $ajuste->tipo_pago_default_id) == $tipo->id)>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_pago_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row mt-4 mb-5">
                    <div class="col-md-6">
                        <a href="{{ route('admin.ventas.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary w-100" id="btnGuardar">
                            <i class="fas fa-check"></i> Registrar Venta
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Resumen de compra -->
        <div class="col-md-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-receipt"></i> Resumen de Compra</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <label class="form-label text-muted">Cantidad de Artículos</label>
                        <h4 id="cantidadArticulos">0</h4>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <label class="form-label text-muted">Subtotal</label>
                        <h5 id="subtotalMonto">$0.00</h5>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <label class="form-label text-muted">IVA ({{ number_format($ivaPorcentaje, 2, '.', '') }}%)</label>
                        <h5 id="ivaMonto" class="text-info">$0.00</h5>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted">Total</label>
                        <h3 class="text-success" id="totalMonto">$0.00</h3>
                    </div>

                    <div id="avisoProductos" class="alert alert-warning">
                        <small>Agrega productos para completar la venta</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let productos = [];
const ivaPorcentaje = Number(@json($ivaPorcentaje));
const ivaRate = ivaPorcentaje / 100;
const numeroFacturaInput = document.getElementById('numero_factura');
const sugerenciaFactura = numeroFacturaInput?.dataset?.sugerencia || '';
const monedaSimbolo = @json($ajuste->moneda_simbolo ?? '$');
const monedaDecimales = Number(@json($ajuste->moneda_decimales ?? 2));

function formatMoney(valor) {
    return monedaSimbolo + Number(valor).toFixed(monedaDecimales);
}

// Validar formulario antes de enviar
document.getElementById('ventasForm').addEventListener('submit', function(e) {
    console.log('Formulario enviándose...');
    console.log('Productos:', productos);
    
    // Validar que haya productos
    if (productos.length === 0) {
        e.preventDefault();
        alert('⚠️ Debes agregar al menos un producto para registrar la venta');
        console.log('ERROR: No hay productos');
        return false;
    }

    // Validar tipo de pago
    const tipoPago = document.getElementById('tipo_pago_id').value;
    console.log('Tipo de pago:', tipoPago);
    if (!tipoPago) {
        e.preventDefault();
        alert('⚠️ Debes seleccionar una forma de pago');
        console.log('ERROR: No hay tipo de pago');
        return false;
    }

    // Validar datos del cliente
    const cedula = document.getElementById('cliente_cedula').value.trim();
    const nombres = document.getElementById('cliente_nombres').value.trim();
    const apellidos = document.getElementById('cliente_apellidos').value.trim();

    console.log('Cliente:', { cedula, nombres, apellidos });

    if (!cedula || !nombres || !apellidos) {
        e.preventDefault();
        alert('⚠️ Debes completar los datos del cliente (Cédula, Nombres y Apellidos)');
        console.log('ERROR: Faltan datos del cliente');
        return false;
    }

    // Mostrar confirmación
    const total = document.getElementById('totalMonto').textContent;
    if (!confirm(`¿Confirmar registro de venta por ${total}?`)) {
        e.preventDefault();
        console.log('Usuario canceló la confirmación');
        return false;
    }

    console.log('Validación OK - Enviando formulario...');
    // Deshabilitar botón para evitar doble envío
    document.getElementById('btnGuardar').disabled = true;
    document.getElementById('btnGuardar').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    
    // Permitir que el formulario se envíe
    return true;
});

// Autocompletar número de factura con sugerencia al salir del campo
if (numeroFacturaInput && sugerenciaFactura) {
    numeroFacturaInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.value = sugerenciaFactura;
        }
    });
}

// Buscar cliente por cédula
document.getElementById('cliente_cedula').addEventListener('blur', function() {
    const cedula = this.value.trim();
    if (cedula.length > 0) {
        buscarCliente(cedula);
    }
});

document.getElementById('btnBuscarCliente').addEventListener('click', function() {
    const cedula = document.getElementById('cliente_cedula').value.trim();
    if (cedula.length > 0) {
        buscarCliente(cedula);
    } else {
        alert('Ingresa una cédula para buscar');
    }
});

function buscarCliente(cedula) {
    const aviso = document.getElementById('avisoCliente');
    aviso.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Buscando...';

    fetch(`{{ url('admin/ventas/api/cliente') }}/${cedula}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Rellenar datos del cliente
                document.getElementById('cliente_nombres').value = data.cliente.nombres;
                document.getElementById('cliente_apellidos').value = data.cliente.apellidos;
                document.getElementById('cliente_email').value = data.cliente.email || '';
                document.getElementById('cliente_telefono').value = data.cliente.telefono || '';
                document.getElementById('cliente_fecha_nacimiento').value = data.cliente.fecha_nacimiento || '';
                mostrarAvisoEdad();
                
                aviso.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> Cliente encontrado y datos cargados</span>';
                
                // Limpiar aviso después de 3 segundos
                setTimeout(() => {
                    aviso.innerHTML = '';
                }, 3000);
            } else {
                aviso.innerHTML = '<span class="text-info"><i class="fas fa-info-circle"></i> Cliente nuevo - Ingresa sus datos</span>';
                // Limpiar campos
                document.getElementById('cliente_nombres').value = '';
                document.getElementById('cliente_apellidos').value = '';
                document.getElementById('cliente_email').value = '';
                document.getElementById('cliente_telefono').value = '';
                document.getElementById('cliente_fecha_nacimiento').value = '';
                mostrarAvisoEdad();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            aviso.innerHTML = '<span class="text-danger"><i class="fas fa-exclamation-circle"></i> Error al buscar cliente</span>';
        });
}

// Mostrar aviso si es menor de edad
const fechaNacimientoInput = document.getElementById('cliente_fecha_nacimiento');
const avisoMenor = document.getElementById('avisoMenor');

fechaNacimientoInput.addEventListener('change', mostrarAvisoEdad);
fechaNacimientoInput.addEventListener('blur', mostrarAvisoEdad);

function mostrarAvisoEdad() {
    const valor = fechaNacimientoInput.value;
    if (!valor) {
        avisoMenor.innerHTML = '';
        return;
    }

    const fecha = new Date(valor);
    const hoy = new Date();
    let edad = hoy.getFullYear() - fecha.getFullYear();
    const m = hoy.getMonth() - fecha.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < fecha.getDate())) {
        edad--;
    }

    if (edad < 18) {
        avisoMenor.innerHTML = '<span class="text-danger"><i class="fas fa-user-shield"></i> Menor de edad: se facturará como Consumidor Final</span>';
    } else {
        avisoMenor.innerHTML = '<span class="text-success"><i class="fas fa-user-check"></i> Mayor de edad</span>';
    }
}

// Consumidor Final (manual)
const btnConsumidorFinal = document.getElementById('btnConsumidorFinal');
const btnRestaurarCliente = document.getElementById('btnRestaurarCliente');
const consumidorFinalInput = document.getElementById('consumidor_final');

const consumidorFinalDatos = {
    cedula: '9999999999',
    nombres: 'Consumidor',
    apellidos: 'Final',
    email: '',
    telefono: '',
    fecha: ''
};

function aplicarConsumidorFinal() {
    document.getElementById('cliente_cedula').value = consumidorFinalDatos.cedula;
    document.getElementById('cliente_nombres').value = consumidorFinalDatos.nombres;
    document.getElementById('cliente_apellidos').value = consumidorFinalDatos.apellidos;
    document.getElementById('cliente_email').value = consumidorFinalDatos.email;
    document.getElementById('cliente_telefono').value = consumidorFinalDatos.telefono;
    document.getElementById('cliente_fecha_nacimiento').value = consumidorFinalDatos.fecha;
    consumidorFinalInput.value = '1';
    avisoMenor.innerHTML = '<span class="text-info"><i class="fas fa-info-circle"></i> Se facturará como Consumidor Final</span>';
    btnRestaurarCliente.classList.remove('d-none');
}

function restaurarConsumidorFinal() {
    consumidorFinalInput.value = '0';
    btnRestaurarCliente.classList.add('d-none');
}

btnConsumidorFinal.addEventListener('click', function() {
    aplicarConsumidorFinal();
});

btnRestaurarCliente.addEventListener('click', function() {
    restaurarConsumidorFinal();
});

// Si el usuario edita datos manualmente, se desactiva el flag
['cliente_cedula','cliente_nombres','cliente_apellidos','cliente_email','cliente_telefono','cliente_fecha_nacimiento']
    .forEach(id => {
        const el = document.getElementById(id);
        el.addEventListener('input', function() {
            if (consumidorFinalInput.value === '1' && this.value !== '') {
                restaurarConsumidorFinal();
            }
        });
    });

document.getElementById('agregarProducto').addEventListener('click', function() {
    const select = document.getElementById('producto_select');
    const cantidad = parseInt(document.getElementById('cantidad').value) || 1;
    
    if (!select.value) {
        alert('Selecciona un producto');
        return;
    }

    const productoId = select.value;
    const option = select.options[select.selectedIndex];
    const nombre = option.getAttribute('data-nombre');
    const precio = parseFloat(option.getAttribute('data-precio'));
    const stock = parseInt(option.getAttribute('data-stock'));

    if (cantidad > stock) {
        alert('No hay suficiente stock. Stock disponible: ' + stock);
        return;
    }

    // Verificar si ya existe el producto
    const productoExistente = productos.find(p => p.id === productoId);
    if (productoExistente) {
        if (productoExistente.cantidad + cantidad > stock) {
            alert('No hay suficiente stock para esta cantidad adicional. Stock disponible: ' + stock);
            return;
        }
        productoExistente.cantidad += cantidad;
    } else {
        productos.push({
            id: productoId,
            nombre: nombre,
            precio: precio,
            cantidad: cantidad,
            stock: stock
        });
    }

    actualizarTabla();
    select.value = '';
    document.getElementById('cantidad').value = '1';
});

function eliminarProducto(indice) {
    productos.splice(indice, 1);
    actualizarTabla();
}

function actualizarTabla() {
    const tbody = document.getElementById('detallesVenta');
    const sinProductos = document.getElementById('sinProductos');
    const container = document.getElementById('productosContainer');

    tbody.innerHTML = '';
    container.innerHTML = '';

    if (productos.length === 0) {
        tbody.innerHTML = '<tr id="sinProductos" class="text-center text-muted"><td colspan="5">Sin productos agregados</td></tr>';
        document.getElementById('cantidadArticulos').textContent = '0';
        document.getElementById('subtotalMonto').textContent = formatMoney(0);
        document.getElementById('totalMonto').textContent = formatMoney(0);
        document.getElementById('btnGuardar').disabled = true;
        return;
    }

    let subtotal = 0;
    let cantidadTotal = 0;

    productos.forEach((producto, indice) => {
        const subtotalProducto = producto.cantidad * producto.precio;
        subtotal += subtotalProducto;
        cantidadTotal += producto.cantidad;

        const row = `
            <tr>
                <td>${producto.nombre}</td>
                <td>${producto.cantidad}</td>
                <td>$${producto.precio.toFixed(2)}</td>
                <td>$${subtotalProducto.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarProducto(${indice})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;

        // Agregar campos ocultos
        container.innerHTML += `
            <input type="hidden" name="productos[${indice}][id]" value="${producto.id}">
            <input type="hidden" name="productos[${indice}][cantidad]" value="${producto.cantidad}">
            <input type="hidden" name="productos[${indice}][precio]" value="${producto.precio}">
        `;
    });

    document.getElementById('cantidadArticulos').textContent = cantidadTotal;
    
    // Calcular IVA desde ajustes y total
    const iva = subtotal * ivaRate;
    const totalConIva = subtotal + iva;
    
    document.getElementById('subtotalMonto').textContent = formatMoney(subtotal);
    document.getElementById('ivaMonto').textContent = formatMoney(iva);
    document.getElementById('totalMonto').textContent = formatMoney(totalConIva);
    document.getElementById('avisoProductos').style.display = 'none';
    document.getElementById('btnGuardar').disabled = false;
}

// Inicializar el botón como deshabilitado al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnGuardar').disabled = true;
});
</script>
@endsection
