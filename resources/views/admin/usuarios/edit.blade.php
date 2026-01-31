@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario</h1>
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
        <form action="{{ route('admin.usuarios.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <!-- Cédula (Agregado para validación, solo lectura) -->
                <div class="col-md-4">
                    <label>Cédula</label>
                    <input name="cedula" class="form-control" value="{{ old('cedula', $user->cedula) }}" readonly>
                </div>

                <div class="col-md-4">
                    <label>Nombres</label>
                    <input name="nombres" class="form-control" value="{{ old('nombres',$user->nombres) }}" readonly>
                </div>

                <div class="col-md-4">
                    <label>Apellidos</label>
                    <input name="apellidos" class="form-control" value="{{ old('apellidos',$user->apellidos) }}" readonly>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Teléfono</label>
                    <input name="telefono" class="form-control" value="{{ old('telefono',$user->telefono) }}" readonly>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Género</label>
                    <input type="hidden" name="genero" value="{{ $user->genero }}">
                    <select class="form-control" disabled>
                        <option value="Masculino" {{ old('genero',$user->genero)=='Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Femenino" {{ old('genero',$user->genero)=='Femenino' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>

                <div class="col-md-4 mt-3">
                    <label>Fecha Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" class="form-control"
                           value="{{ old('fecha_nacimiento',$user->fecha_nacimiento) }}" readonly>
                </div>

                <div class="col-md-12 mt-3">
                    <label>Rol (Editable)</label>
                    <select name="role_name" class="form-control bg-warning text-dark font-weight-bold">
                        @foreach($roles as $r)
                            <option value="{{ $r->name }}" {{ $user->hasRole($r->name) ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mt-3">
                    <label>Dirección</label>
                    <input name="direccion" class="form-control" value="{{ old('direccion',$user->direccion) }}" readonly>
                </div>

                <div class="col-md-6 mt-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email',$user->email) }}" readonly>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">Volver</a>
                <button class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>
@stop
