@extends('adminlte::page')

@section('title', 'Editar Rol')

@section('content_header')
    <h1>Editar Rol</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                <label>Nombre del rol</label>
                <input name="name" class="form-control" value="{{ old('name', $role->name) }}" required>
            </div>

            <div class="card-footer">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Volver</a>
                <button class="btn btn-primary">Actualizar</button>
            </div>
        </div>
    </form>
@stop
