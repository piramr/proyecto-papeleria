@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Roles</h1>

        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            + Nuevo Rol
        </a>
    </div>
@stop

@section('content')
    @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Nombre</th>
                        <th width="200">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->name }}</td>
                            <td>
                                <a class="btn btn-sm btn-warning"
                                   href="{{ route('admin.roles.edit', $r->id) }}">
                                    Editar
                                </a>

                                <form action="{{ route('admin.roles.destroy', $r->id) }}"
                                      method="POST"
                                      style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Eliminar rol?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No hay roles.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
