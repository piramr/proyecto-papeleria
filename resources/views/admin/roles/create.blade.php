@extends('adminlte::page')

@section('title', 'Crear Rol')

@section('content_header')
    <h1>Crear Rol</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">
                <label>Nombre del rol</label>
                <input name="name" class="form-control" value="{{ old('name') }}" required>
                <small class="text-muted">Ej: Admin, Empleado, Auditor</small>
            </div>

            <div class="card-footer">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Volver</a>
                <button class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </form>
@stop
