@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Usuarios</h1>

        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
            + Nuevo Usuario
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
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Cédula</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th width="200">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->cedula }}</td>
                            <td>{{ $u->nombres }}</td>
                            <td>{{ $u->apellidos }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->getRoleNames()->first() ?? '-' }}</td>
                            <td>
                                @if($u->is_active)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Bloqueado</span>
                                @endif
                            </td>
                            <td>
                                @if(!$u->is_active)
                                    <form action="{{ route('admin.usuarios.unlock', $u->id) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="btn btn-sm btn-info" title="Desbloquear usuario" onclick="return confirm('¿Desbloquear a este usuario?')">
                                            <i class="fas fa-unlock"></i>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('admin.usuarios.edit', $u->id) }}" class="btn btn-sm btn-warning">
                                    Editar
                                </a>

                                <form action="{{ route('admin.usuarios.destroy', $u->id) }}"
                                      method="POST"
                                      style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Eliminar usuario?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@stop
