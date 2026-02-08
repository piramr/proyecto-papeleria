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
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-secondary rounded-0 h-100">
                <div class="card-header bg-light rounded-0">
                    <h5 class="mb-0 font-weight-bold small text-uppercase">Operaciones</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold small text-muted">TABLA</label>
                        <div class="bg-light border p-2 mb-3 text-monospace small">LOG_OPERACION</div>
                        <label class="font-weight-bold small text-muted">RETENCIÓN</label>
                        <select class="form-control rounded-0 mb-3">
                            <option>30 días</option>
                            <option selected>90 días</option>
                            <option>180 días</option>
                        </select>
                        <div class="custom-control custom-checkbox mt-4">
                            <input type="checkbox" class="custom-control-input" id="autoOp" checked>
                            <label class="custom-control-label small text-dark" for="autoOp">Eliminado automático activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-secondary rounded-0 h-100">
                <div class="card-header bg-light rounded-0">
                    <h5 class="mb-0 font-weight-bold small text-uppercase">Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold small text-muted">TABLA</label>
                        <div class="bg-light border p-2 mb-3 text-monospace small">LOG_SISTEMA</div>
                        <label class="font-weight-bold small text-muted">RETENCIÓN</label>
                        <select class="form-control rounded-0 mb-3">
                            <option>15 días</option>
                            <option selected>30 días</option>
                            <option>60 días</option>
                        </select>
                        <div class="custom-control custom-checkbox mt-4">
                            <input type="checkbox" class="custom-control-input" id="autoSys" checked>
                            <label class="custom-control-label small text-dark" for="autoSys">Eliminado automático activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card border-secondary rounded-0 h-100">
                <div class="card-header bg-light rounded-0">
                    <h5 class="mb-0 font-weight-bold small text-uppercase">Accesos</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold small text-muted">TABLA</label>
                        <div class="bg-light border p-2 mb-3 text-monospace small">LOG_LOGIN</div>
                        <label class="font-weight-bold small text-muted">RETENCIÓN</label>
                        <select class="form-control rounded-0 mb-3">
                            <option selected>15 días</option>
                            <option>30 días</option>
                            <option>45 días</option>
                        </select>
                        <div class="custom-control custom-checkbox mt-4">
                            <input type="checkbox" class="custom-control-input" id="autoLog" checked>
                            <label class="custom-control-label small text-dark" for="autoLog">Eliminado automático activo</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-danger rounded-0 shadow-sm">
                <div class="card-body bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="font-weight-bold text-danger mb-1">Limpieza manual</h6>
                            <p class="text-muted mb-0">Esta acción purga inmediatamente todos los registros que excedan los límites de tiempo configurados arriba.</p>
                        </div>
                        <div class="col-md-4 text-right">
                            <button class="btn btn-danger btn-block rounded-0 font-weight-bold">
                                <i class="fas fa-trash-alt mr-2"></i>Limpiar Ahora
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-5 pt-4 border-top">
        <div class="col-12 text-right">
            <button class="btn btn-secondary rounded-0 px-5 mr-3 font-weight-bold">Cancelar</button>
            <button class="btn btn-primary rounded-0 px-5 font-weight-bold shadow-sm">Guardar Cambios</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
@endpush