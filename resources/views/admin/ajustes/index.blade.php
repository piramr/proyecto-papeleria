@extends('layouts.app')

@section('title', 'Ajustes')

@section('content_header')
    <h1>Ajustes</h1>
@stop

@section('content')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header bg-dark text-white">
                <h3 class="mb-0"><i class="fas fa-cog"></i> Configuración del Sistema</h3>
            </div>
            
            <div class="card-body">
                <form method="POST" action="{{ route('admin.ajustes.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Tabs de navegación -->
                    <ul class="nav nav-tabs mb-4" id="ajustesTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                                <i class="fas fa-sliders-h"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="empresa-tab" data-toggle="tab" href="#empresa" role="tab">
                                <i class="fas fa-building"></i> Datos de Empresa
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="facturacion-tab" data-toggle="tab" href="#facturacion" role="tab">
                                <i class="fas fa-file-invoice"></i> Facturación
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="diseno-tab" data-toggle="tab" href="#diseno" role="tab">
                                <i class="fas fa-palette"></i> Logo y Diseño
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="inventario-tab" data-toggle="tab" href="#inventario" role="tab">
                                <i class="fas fa-boxes"></i> Inventario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="notificaciones-tab" data-toggle="tab" href="#notificaciones" role="tab">
                                <i class="fas fa-bell"></i> Notificaciones
                            </a>
                        </li>
                    </ul>

                    <!-- Contenido de tabs -->
                    <div class="tab-content" id="ajustesTabsContent">
                        <!-- Tab General -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <h5 class="mb-4"><i class="fas fa-percentage"></i> Impuestos</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="iva_porcentaje">IVA (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" class="form-control" 
                                               id="iva_porcentaje" name="iva_porcentaje" 
                                               value="{{ old('iva_porcentaje', $ajuste->iva_porcentaje) }}" required>
                                        <small class="text-muted">Se aplicará en ventas y facturas nuevas.</small>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-4"><i class="fas fa-dollar-sign"></i> Moneda y Formato</h5>
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="moneda_simbolo">Símbolo de moneda</label>
                                    <input class="form-control" id="moneda_simbolo" name="moneda_simbolo" 
                                           value="{{ old('moneda_simbolo', $ajuste->moneda_simbolo ?? '$') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="moneda_decimales">Decimales</label>
                                    <input type="number" min="0" max="4" class="form-control" id="moneda_decimales" 
                                           name="moneda_decimales" value="{{ old('moneda_decimales', $ajuste->moneda_decimales ?? 2) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tipo_pago_default_id">Forma de pago por defecto</label>
                                    <select class="form-select" id="tipo_pago_default_id" name="tipo_pago_default_id">
                                        <option value="">-- Sin defecto --</option>
                                        @foreach($tiposPago as $tipo)
                                            <option value="{{ $tipo->id }}" @selected(old('tipo_pago_default_id', $ajuste->tipo_pago_default_id) == $tipo->id)>
                                                {{ $tipo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Empresa -->
                        <div class="tab-pane fade" id="empresa" role="tabpanel">
                            <h5 class="mb-4"><i class="fas fa-building"></i> Información de la Empresa</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="empresa_nombre">Nombre comercial *</label>
                                    <input class="form-control" id="empresa_nombre" name="empresa_nombre" 
                                           value="{{ old('empresa_nombre', $ajuste->empresa_nombre) }}" placeholder="Nombre de tu negocio">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="empresa_ruc">RUC / Cédula</label>
                                    <input class="form-control" id="empresa_ruc" name="empresa_ruc" 
                                           value="{{ old('empresa_ruc', $ajuste->empresa_ruc) }}" placeholder="Ej. 1234567890001">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="empresa_direccion">Dirección</label>
                                    <input class="form-control" id="empresa_direccion" name="empresa_direccion" 
                                           value="{{ old('empresa_direccion', $ajuste->empresa_direccion) }}" placeholder="Calle Principal, Local 123">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="empresa_telefono">Teléfono</label>
                                    <input class="form-control" id="empresa_telefono" name="empresa_telefono" 
                                           value="{{ old('empresa_telefono', $ajuste->empresa_telefono) }}" placeholder="+593 99 999 9999">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="empresa_email">Email</label>
                                    <input type="email" class="form-control" id="empresa_email" name="empresa_email" 
                                           value="{{ old('empresa_email', $ajuste->empresa_email) }}" placeholder="contacto@empresa.com">
                                </div>
                            </div>
                        </div>

                        <!-- Tab Facturación -->
                        <div class="tab-pane fade" id="facturacion" role="tabpanel">
                            <h5 class="mb-4"><i class="fas fa-hashtag"></i> Numeración de Comprobantes</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Configura cómo se generarán los números de factura automáticamente.
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="prefijo_factura">Prefijo / Serie</label>
                                    <input class="form-control" id="prefijo_factura" name="prefijo_factura" 
                                           value="{{ old('prefijo_factura', $ajuste->prefijo_factura) }}" placeholder="Ej. 001-001-">
                                    <small class="text-muted">Parte fija del número de factura.</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="siguiente_factura">Siguiente secuencial</label>
                                    <input type="number" min="1" class="form-control" id="siguiente_factura" 
                                           name="siguiente_factura" value="{{ old('siguiente_factura', $ajuste->siguiente_factura) }}" placeholder="1">
                                    <small class="text-muted">Se incrementa automáticamente.</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="secuencial_digitos">Dígitos del secuencial</label>
                                    <input type="number" min="1" max="12" class="form-control" id="secuencial_digitos" 
                                           name="secuencial_digitos" value="{{ old('secuencial_digitos', $ajuste->secuencial_digitos ?? 9) }}" required>
                                    <small class="text-muted">Cantidad de ceros a rellenar.</small>
                                </div>
                            </div>
                            @if(!empty($ajuste->prefijo_factura) && !empty($ajuste->siguiente_factura))
                                <div class="alert alert-success">
                                    <strong>Vista previa:</strong> {{ $ajuste->prefijo_factura }}{{ str_pad($ajuste->siguiente_factura, $ajuste->secuencial_digitos ?? 9, '0', STR_PAD_LEFT) }}
                                </div>
                            @endif
                        </div>

                        <!-- Tab Logo y Diseño -->
                        <div class="tab-pane fade" id="diseno" role="tabpanel">
                            <h5 class="mb-4"><i class="fas fa-image"></i> Logo de la Empresa</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="logo_file" class="form-label">Subir Logo</label>
                                        <input type="file" class="form-control" id="logo_file" name="logo_file" accept="image/*">
                                        <small class="text-muted">Formato: JPG, PNG, GIF (máx. 2MB). Se mostrará en facturas.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="logo_url">O ingresar URL de imagen</label>
                                        <input type="text" class="form-control" id="logo_url" name="logo_url" 
                                               value="{{ old('logo_url', $ajuste->logo_url) }}" placeholder="https://ejemplo.com/logo.png">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    @if(!empty($ajuste->logo_url))
                                        <label>Vista previa actual:</label>
                                        <div class="border p-2 text-center">
                                            <img src="{{ $ajuste->logo_url }}" alt="Logo" style="max-width: 100%; max-height: 100px;">
                                        </div>
                                    @else
                                        <div class="alert alert-secondary">Sin logo configurado</div>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-4"><i class="fas fa-align-center"></i> Pie de Factura</h5>
                            <div class="form-group">
                                <label for="pie_factura">Mensaje al pie de la factura</label>
                                <textarea class="form-control" id="pie_factura" name="pie_factura" rows="3" 
                                          placeholder="Gracias por su compra. Esta factura es válida como comprobante.">{{ old('pie_factura', $ajuste->pie_factura) }}</textarea>
                                <small class="text-muted">Aparecerá al final de cada factura impresa.</small>
                            </div>
                        </div>

                        <!-- Tab Inventario -->
                        <div class="tab-pane fade" id="inventario" role="tabpanel">
                            <h5 class="mb-4"><i class="fas fa-warehouse"></i> Control de Stock</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="stock_minimo">Stock mínimo (general)</label>
                                    <input type="number" min="0" class="form-control" id="stock_minimo" name="stock_minimo" 
                                           value="{{ old('stock_minimo', $ajuste->stock_minimo ?? 5) }}">
                                    <small class="text-muted">Cantidad base para alertas.</small>
                                </div>
                                <div class="col-md-8 mb-3 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="stock_alerta_habilitada" 
                                               name="stock_alerta_habilitada" value="1" @checked(old('stock_alerta_habilitada', $ajuste->stock_alerta_habilitada))>
                                        <label class="form-check-label" for="stock_alerta_habilitada">
                                            <strong>Habilitar alertas de stock bajo</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Notificaciones -->
                        <div class="tab-pane fade" id="notificaciones" role="tabpanel">
                            <h5 class="mb-4"><i class="fas fa-bell"></i> Gestión de Notificaciones</h5>
                            <p class="text-muted mb-4">Selecciona qué notificaciones deseas recibir en el sistema.</p>
                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="notif_stock_bajo" 
                                               name="notif_stock_bajo" value="1" @checked(old('notif_stock_bajo', $ajuste->notif_stock_bajo ?? true))>
                                        <label class="form-check-label" for="notif_stock_bajo">
                                            <strong><i class="fas fa-box-open text-warning"></i> Stock bajo</strong>
                                            <br><small class="text-muted">Recibir alerta cuando un producto tenga stock bajo.</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="notif_venta_realizada" 
                                               name="notif_venta_realizada" value="1" @checked(old('notif_venta_realizada', $ajuste->notif_venta_realizada ?? true))>
                                        <label class="form-check-label" for="notif_venta_realizada">
                                            <strong><i class="fas fa-shopping-cart text-success"></i> Venta realizada</strong>
                                            <br><small class="text-muted">Notificar cada vez que se registre una venta.</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="notif_compra_recibida" 
                                               name="notif_compra_recibida" value="1" @checked(old('notif_compra_recibida', $ajuste->notif_compra_recibida ?? true))>
                                        <label class="form-check-label" for="notif_compra_recibida">
                                            <strong><i class="fas fa-truck text-info"></i> Compra recibida</strong>
                                            <br><small class="text-muted">Notificar cuando se reciba mercancía de proveedores.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Las notificaciones aparecerán en el sistema y se mostrarán en la parte superior del dashboard.
                            </div>
                        </div>
                    </div>

                    <!-- Botón de guardar -->
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Guardar todos los cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop