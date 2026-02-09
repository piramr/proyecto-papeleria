<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Services\Auditoria\AuditoriaService;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id','desc')->paginate(10);
        return view('admin.usuarios.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cedula' => ['required','string','max:10','unique:users,cedula'],
            'nombres' => ['required','string','max:120'],
            'apellidos' => ['required','string','max:120'],
            'telefono' => ['required','string','max:20'],
            'genero' => ['required', Rule::in(['Masculino', 'Femenino', 'Otro'])],
            'fecha_nacimiento' => ['required','date'],
            'direccion' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
            'role_name' => ['required','exists:roles,name'],
        ]);

        $roleName = $data['role_name'];

        $user = User::create([
            'cedula' => $data['cedula'],
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'telefono' => $data['telefono'],
            'genero' => $data['genero'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'direccion' => $data['direccion'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            // si quieres mantener tu columna role:
            'role' => strtolower($roleName) === 'admin' ? 'admin' : (strtolower($roleName) === 'empleado' ? 'empleado' : 'auditor'),
        ]);

        // ✅ rol spatie
        $user->syncRoles([$roleName]);

        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'crear',
            'entidad' => 'Usuario',
            'recurso_id' => $user->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        AuditoriaService::registrarLogSistema('INFO', '[SISTEMA] Nuevo usuario registrado en el sistema: ' . $user->email . ' con rol ' . $roleName);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado.');
    }

    public function edit(User $usuario)
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.usuarios.edit', ['user' => $usuario, 'roles' => $roles]);
    }

    public function update(Request $request, User $usuario)
    {
        $data = $request->validate([
            'cedula' => ['required','string','max:10', Rule::unique('users','cedula')->ignore($usuario->id)],
            'nombres' => ['required','string','max:120'],
            'apellidos' => ['required','string','max:120'],
            'telefono' => ['required','string','max:20'],
            'genero' => ['required', Rule::in(['Masculino', 'Femenino', 'Otro'])],
            'fecha_nacimiento' => ['required','date'],
            'direccion' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users','email')->ignore($usuario->id)],
            'password' => ['nullable','string','min:8','confirmed'],
            'role_name' => ['required','exists:roles,name'],
        ]);

        $usuario->fill([
            'cedula' => $data['cedula'],
            'nombres' => $data['nombres'],
            'apellidos' => $data['apellidos'],
            'telefono' => $data['telefono'],
            'genero' => $data['genero'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'direccion' => $data['direccion'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $usuario->password = Hash::make($data['password']);
        }

        $roleName = $data['role_name'];

        // opcional: mantener columna role
        $usuario->role = strtolower($roleName) === 'admin' ? 'admin' : (strtolower($roleName) === 'empleado' ? 'empleado' : 'auditor');

        $usuario->save();

        // ✅ cambiar rol spatie
        $usuario->syncRoles([$roleName]);

        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'actualizar',
            'entidad' => 'Usuario',
            'recurso_id' => $usuario->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $usuario)
    {
        $usuario->delete();
        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'eliminar',
            'entidad' => 'Usuario',
            'recurso_id' => $usuario->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario eliminado.');
    }

    public function unlock(User $usuario)
    {
        $usuario->forceFill([
            'is_active' => true,
            'inactivated_at' => null,
        ])->save();
        // Limpiar intentos de login en caché (Clave sin IP)
        RateLimiter::clear('login_lock|'.$usuario->email);
        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'desbloquear',
            'entidad' => 'Usuario',
            'recurso_id' => $usuario->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        AuditoriaService::registrarLogSistema('WARNING', '[SEGURIDAD] Usuario desbloqueado manualmente: ' . $usuario->email . '. Acción realizada por administrador.');
        return redirect()->back()->with('success', 'Usuario desbloqueado correctamente. Contraseña restablecida a 12345678');
    }
}
