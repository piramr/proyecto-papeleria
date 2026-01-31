@extends('layouts.app')

@section('title', 'Dashboard Empleado')

@section('content_header')
    <h1>Panel de Empleado</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-none border">
                <div class="card-header border-0">
                    <h3 class="card-title text-bold">Bienvenido, {{ Auth::user()->nombres }}</h3>
                </div>
                <div class="card-body">
                    <p>Bienvenido al panel de empleados. Aquí podrás gestionar tus ventas y ver productos.</p>
                    <a href="{{ route('empleado.ventas') }}" class="btn btn-success">
                        <i class="fas fa-shopping-cart"></i> Nueva Venta
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
