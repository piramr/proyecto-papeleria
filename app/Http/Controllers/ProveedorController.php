<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
        $proveedor->load('direcciones');
        
        $html = view('admin.proveedores.partials.form-edit', [
            'proveedor' => $proveedor,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProveedorRequest $request, Proveedor $proveedor) {
        $data = $request->validated();

        $proveedor->update([
            'ruc' => $data['ruc'],
            'nombre' => $data['nombre'],
            'telefono_principal' => $data['telefono_principal'],
            'telefono_secundario' => $data['telefono_secundario'] ?? null,
            'email' => $data['email'],
        ]);

        if (isset($data['direcciones'])) {
            $proveedor->direcciones()->delete();
            foreach ($data['direcciones'] as $dir) {
                $proveedor->direcciones()->create($dir);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Proveedor actualizado correctamente',
                'proveedor' => $proveedor->load('direcciones')
            ]);
        }

        return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor) {
        $proveedor->direcciones()->delete();
        $proveedor->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['message' => 'Proveedor eliminado correctamente']);
        }

        return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor eliminado correctamente');
    }

    public function datatables() {
        $query = Proveedor::select([
            'ruc',
            'nombre',
            'email',
            'telefono_principal',
            'telefono_secundario',
            'created_at'
        ]);

        return DataTables::eloquent($query)
            ->addColumn('direcciones', function ($proveedor) {
                $direcciones = $proveedor->direcciones()->get();

                if ($direcciones->isEmpty()) {
                    return '<span class="text-muted">- - -</span>';
                }

                $html = '<ul class="pl-3 mb-0">';
                foreach ($direcciones as $dir) {
                    $html .= "<li>{$dir->calle}, {$dir->ciudad}, {$dir->provincia}</li>";
                }
                $html .= '</ul>';

                return $html;
            })
            ->addColumn('acciones', function ($proveedor) {
                return '
                <div class="d-flex justify-content-center">
                    <button class="btn btn-sm btn-warning mr-1 btnEditProveedor" data-id="' . $proveedor->ruc . '"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['direcciones', 'acciones'])
            ->make(true);
    }
}
