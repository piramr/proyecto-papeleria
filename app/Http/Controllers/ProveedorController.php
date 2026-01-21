<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProveedorRequest $request) {
        $data = $request->validated();

        $proveedor = Proveedor::create([
            'ruc' => $data['ruc'],
            'nombre' => $data['nombre'],
            'telefono_principal' => $data['telefono_principal'],
            'telefono_secundario' => $data['telefono_secundario'] ?? null,
            'email' => $data['email'],
        ]);

        foreach ($data['direcciones'] as $dir) {
            $proveedor->direcciones()->create($dir);
        }

        return redirect()
            ->route('admin.proveedores.index')
            ->with('success', 'Proveedor registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedor) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor) {
        //
    }
}
