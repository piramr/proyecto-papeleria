<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
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
            ->addColumn('acciones', function () {
                return '
                <div class="d-flex justify-content-center">
                    <button class="btn btn-sm btn-info mr-1"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-warning mr-1"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['direcciones', 'acciones'])
            ->make(true);
    }
}
