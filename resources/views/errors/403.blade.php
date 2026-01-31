@extends('adminlte::page')

@section('title', 'Acceso Denegado')

@section('content_header')
    <h1>403 Acceso Denegado</h1>
@stop

@section('content')
    <div class="error-page">
        <h2 class="headline text-warning"> 403</h2>

        <div class="error-content">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Acceso restringido.</h3>

            <p>
                No tienes los permisos necesarios para acceder a esta sección.
                {{ $exception->getMessage() ?: 'Acceso denegado.' }}
            </p>

            <form class="search-form">
                <div class="input-group">
                    <a href="{{ route('dashboard') }}" class="btn btn-warning btn-flat btn-block">
                        <i class="fas fa-arrow-left"></i> Volver al Inicio
                    </a>
                </div>
            </form>
        </div>
        <!-- /.error-content -->
    </div>
@stop
