@extends('layouts.app')

@section('title', 'Ajustes de Retención - Auditoría')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="container py-5">
    <div class="border-bottom pb-3 mb-5">
        <h2 class="font-weight-bold text-dark">Ajustes</h2>
        <p class="text-secondary">Configuración de persistencia y limpieza automática para las tablas relacionadas con logs.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-0" role="alert">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-0" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i>
            @foreach($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('auditor.ajustes.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- LOG_OPERACION -->
            <div class="col-md-4 mb-4">
                <div class="card border-secondary rounded-0 h-100">
                    <div class="card-header bg-light rounded-0">
                        <h5 class="mb-0 font-weight-bold small text-uppercase">Operaciones</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-muted">TABLA</label>
                            <div class="bg-light border p-2 mb-3 text-monospace small">LOG_OPERACION</div>
                            
                            <label class="font-weight-bold small text-muted">REGISTROS</label>
                            <div class="bg-info text-white p-2 mb-3 text-center" id="stats-log_operacion">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </div>
                            
                            <label class="font-weight-bold small text-muted">RETENCIÓN</label>
                            <select name="log_operacion_retencion" class="form-control rounded-0 mb-3">
                                <option value="30" {{ ($ajuste->log_operacion_retencion ?? 90) == 30 ? 'selected' : '' }}>30 días</option>
                                <option value="60" {{ ($ajuste->log_operacion_retencion ?? 90) == 60 ? 'selected' : '' }}>60 días</option>
                                <option value="90" {{ ($ajuste->log_operacion_retencion ?? 90) == 90 ? 'selected' : '' }}>90 días</option>
                                <option value="180" {{ ($ajuste->log_operacion_retencion ?? 90) == 180 ? 'selected' : '' }}>180 días</option>
                                <option value="365" {{ ($ajuste->log_operacion_retencion ?? 90) == 365 ? 'selected' : '' }}>365 días</option>
                            </select>
                            
                            <div class="custom-control custom-checkbox mt-4 mb-3">
                                <input type="checkbox" class="custom-control-input" id="autoOp" 
                                       name="log_operacion_auto_delete" {{ ($ajuste->log_operacion_auto_delete ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label small text-dark" for="autoOp">Eliminado automático activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-2">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-block rounded-0" 
                                onclick="limpiarLog('log_operacion', 'LOG_OPERACION')">
                            <i class="fas fa-trash-alt mr-1"></i> Limpiar Ahora
                        </button>
                    </div>
                </div>
            </div>

            <!-- LOG_SISTEMA -->
            <div class="col-md-4 mb-4">
                <div class="card border-secondary rounded-0 h-100">
                    <div class="card-header bg-light rounded-0">
                        <h5 class="mb-0 font-weight-bold small text-uppercase">Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-muted">TABLA</label>
                            <div class="bg-light border p-2 mb-3 text-monospace small">LOG_SISTEMA</div>
                            
                            <label class="font-weight-bold small text-muted">REGISTROS</label>
                            <div class="bg-warning text-dark p-2 mb-3 text-center" id="stats-log_sistema">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </div>
                            
                            <label class="font-weight-bold small text-muted">RETENCIÓN</label>
                            <select name="log_sistema_retencion" class="form-control rounded-0 mb-3">
                                <option value="7" {{ ($ajuste->log_sistema_retencion ?? 30) == 7 ? 'selected' : '' }}>7 días</option>
                                <option value="15" {{ ($ajuste->log_sistema_retencion ?? 30) == 15 ? 'selected' : '' }}>15 días</option>
                                <option value="30" {{ ($ajuste->log_sistema_retencion ?? 30) == 30 ? 'selected' : '' }}>30 días</option>
                                <option value="60" {{ ($ajuste->log_sistema_retencion ?? 30) == 60 ? 'selected' : '' }}>60 días</option>
                                <option value="90" {{ ($ajuste->log_sistema_retencion ?? 30) == 90 ? 'selected' : '' }}>90 días</option>
                            </select>
                            
                            <div class="custom-control custom-checkbox mt-4 mb-3">
                                <input type="checkbox" class="custom-control-input" id="autoSys" 
                                       name="log_sistema_auto_delete" {{ ($ajuste->log_sistema_auto_delete ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label small text-dark" for="autoSys">Eliminado automático activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-2">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-block rounded-0" 
                                onclick="limpiarLog('log_sistema', 'LOG_SISTEMA')">
                            <i class="fas fa-trash-alt mr-1"></i> Limpiar Ahora
                        </button>
                    </div>
                </div>
            </div>

            <!-- LOG_LOGIN -->
            <div class="col-md-4 mb-4">
                <div class="card border-secondary rounded-0 h-100">
                    <div class="card-header bg-light rounded-0">
                        <h5 class="mb-0 font-weight-bold small text-uppercase">Accesos</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="font-weight-bold small text-muted">TABLA</label>
                            <div class="bg-light border p-2 mb-3 text-monospace small">LOG_LOGIN</div>
                            
                            <label class="font-weight-bold small text-muted">REGISTROS</label>
                            <div class="bg-success text-white p-2 mb-3 text-center" id="stats-log_login">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </div>
                            
                            <label class="font-weight-bold small text-muted">RETENCIÓN</label>
                            <select name="log_login_retencion" class="form-control rounded-0 mb-3">
                                <option value="7" {{ ($ajuste->log_login_retencion ?? 15) == 7 ? 'selected' : '' }}>7 días</option>
                                <option value="15" {{ ($ajuste->log_login_retencion ?? 15) == 15 ? 'selected' : '' }}>15 días</option>
                                <option value="30" {{ ($ajuste->log_login_retencion ?? 15) == 30 ? 'selected' : '' }}>30 días</option>
                                <option value="45" {{ ($ajuste->log_login_retencion ?? 15) == 45 ? 'selected' : '' }}>45 días</option>
                                <option value="60" {{ ($ajuste->log_login_retencion ?? 15) == 60 ? 'selected' : '' }}>60 días</option>
                            </select>
                            
                            <div class="custom-control custom-checkbox mt-4 mb-3">
                                <input type="checkbox" class="custom-control-input" id="autoLog" 
                                       name="log_login_auto_delete" {{ ($ajuste->log_login_auto_delete ?? true) ? 'checked' : '' }}>
                                <label class="custom-control-label small text-dark" for="autoLog">Eliminado automático activo</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-2">
                        <button type="button" class="btn btn-outline-danger btn-sm btn-block rounded-0" 
                                onclick="limpiarLog('log_login', 'LOG_LOGIN')">
                            <i class="fas fa-trash-alt mr-1"></i> Limpiar Ahora
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5 pt-4 border-top">
            <div class="col-12 text-right">
                <a href="{{ route('auditor.dashboard') }}" class="btn btn-secondary rounded-0 px-5 mr-3 font-weight-bold">Cancelar</a>
                <button type="submit" class="btn btn-primary rounded-0 px-5 font-weight-bold shadow-sm">
                    <i class="fas fa-save mr-2"></i>Guardar Cambios
                </button>
            </div>
        </div>
    </form>

    <!-- Formulario oculto para limpieza individual -->
    <form id="formLimpiarLog" action="" method="POST" style="display: none;">
        @csrf
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar estadísticas
        cargarStats();
    });

    function cargarStats() {
        fetch('{{ route('auditor.api.ajustes.stats') }}')
            .then(response => response.json())
            .then(data => {
                // LOG_OPERACION
                const opStats = data.log_operacion;
                document.getElementById('stats-log_operacion').innerHTML = 
                    `<strong>${opStats.total}</strong> registros ` +
                    (opStats.antiguos > 0 ? `<small>(${opStats.antiguos} a eliminar)</small>` : '');

                // LOG_SISTEMA
                const sysStats = data.log_sistema;
                document.getElementById('stats-log_sistema').innerHTML = 
                    `<strong>${sysStats.total}</strong> registros ` +
                    (sysStats.antiguos > 0 ? `<small>(${sysStats.antiguos} a eliminar)</small>` : '');

                // LOG_LOGIN
                const logStats = data.log_login;
                document.getElementById('stats-log_login').innerHTML = 
                    `<strong>${logStats.total}</strong> registros ` +
                    (logStats.antiguos > 0 ? `<small>(${logStats.antiguos} a eliminar)</small>` : '');
            })
            .catch(err => {
                console.error('Error cargando stats:', err);
                document.getElementById('stats-log_operacion').innerHTML = '<span class="text-danger">Error</span>';
                document.getElementById('stats-log_sistema').innerHTML = '<span class="text-danger">Error</span>';
                document.getElementById('stats-log_login').innerHTML = '<span class="text-danger">Error</span>';
            });
    }

    function limpiarLog(tipo, nombre) {
        if (confirm(`¿Está seguro de que desea limpiar ${nombre}? Esta acción eliminará los registros antiguos de forma permanente.`)) {
            const form = document.getElementById('formLimpiarLog');
            form.action = '{{ url('auditor/ajustes/limpiar') }}/' + tipo;
            form.submit();
        }
    }
</script>
@endpush