@extends('layouts.app')

@section('title', 'Dashboard Auditor')

@section('content_header')
    <h1>Panel de Auditoría</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Bienvenido, Auditor</h3>
                </div>
                <div class="card-body">
                    <p>Desde aquí puede acceder a los registros de auditoría y reportes del sistema.</p>
                    <a href="{{ route('auditor.auditoria') }}" class="btn btn-primary">
                        <i class="fas fa-list-alt"></i> Ver Logs de Auditoría
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
