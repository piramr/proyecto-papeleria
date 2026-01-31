@extends('adminlte::page')

@section('title', 'Nuevo Usuario')

@section('content_header')
    <h1>Nuevo Usuario</h1>
@stop

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <label>Cédula</label>
                    <input name="cedula" value="{{ old('cedula') }}" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Nombres</label>
                    <input name="nombres" value="{{ old('nombres') }}" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Apellidos</label>
                    <input name="apellidos" value="{{ old('apellidos') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Teléfono</label>
                    <input name="telefono" value="{{ old('telefono') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Género</label>
                    <select name="genero" class="form-control" required>
                        <option value="">Seleccione</option>
                        <option value="Masculino" {{ old('genero')=='Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Femenino" {{ old('genero')=='Femenino' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="form-control" required>
                </div>

                <div class="col-md-6 mt-3">
                    <label>Dirección</label>
                    <input name="direccion" value="{{ old('direccion') }}" class="form-control" required>
                </div>

                <div class="col-md-6 mt-3">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                </div>

                <div class="col-md-6 mt-3">
                    <label>Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-md-6 mt-3">
                    <label>Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <div class="col-md-6 mt-3">
                    <label>Rol (Spatie)</label>
                    <select name="role_name" class="form-control" required>
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ old('role_name')==$r->name ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Volver</a>
                <button class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>
@stop
