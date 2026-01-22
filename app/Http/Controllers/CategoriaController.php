<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoriaController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index() {
        return view('admin.inventario.categorias.index');
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
    public function store(StoreCategoriaRequest $request) {
        $data = $request->validated();

        Categoria::create($data);

        return redirect()->route('admin.categorias')->with('success', 'Categoría registrada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria) {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria) {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria) {
        //
    }

    public function datatables() {
        $query = Categoria::select([
            'id',
            'nombre',
            'descripcion',
            'created_at'
        ])->withCount('productos');

        return DataTables::eloquent($query)
            ->addColumn('productos_count', function ($categoria) {
                return $categoria->productos_count;
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
            ->rawColumns(['acciones'])
            ->make(true);
    }
}
