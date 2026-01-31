<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('id', 'desc')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:50','unique:roles,name'],
        ]);

        Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.roles.index')->with('ok', 'Rol creado.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => ['required','string','max:50', Rule::unique('roles','name')->ignore($role->id)],
        ]);

        $role->update(['name' => $data['name']]);

        return redirect()->route('admin.roles.index')->with('ok', 'Rol actualizado.');
    }

    public function destroy(Role $role)
    {
        // Para evitar borrar roles base
        if (in_array($role->name, ['Admin','Empleado','Auditor'])) {
            return back()->with('error', 'No puedes eliminar roles base.');
        }

        $role->delete();
        return back()->with('ok', 'Rol eliminado.');
    }
}
