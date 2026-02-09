<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Services\Auditoria\AuditoriaService;

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

        $categoria = Categoria::create($data);
        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'crear',
            'entidad' => 'Categoria',
            'recurso_id' => $categoria->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
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
        $html = view('admin.inventario.categorias.partials.form', [
            'categoria' => $categoria,
            'isModal' => true,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoriaRequest $request, Categoria $categoria) {
        $validated = $request->validated();
        $categoria->update($validated);
        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'actualizar',
            'entidad' => 'Categoria',
            'recurso_id' => $categoria->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Categoría actualizada correctamente',
                'categoria' => $categoria
            ]);
        }
        return redirect()->route('admin.categorias')->with('success', 'Categoría actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria) {
        $categoria->delete();
        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'eliminar',
            'entidad' => 'Categoria',
            'recurso_id' => $categoria->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['message' => 'Categoría eliminada correctamente']);
        }
        return redirect()->route('admin.categorias')->with('success', 'Categoría eliminada correctamente');
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
            ->addColumn('acciones', function ($categoria) {
                return '
                <div class="d-flex justify-content-center">
                    <button class="btn btn-sm btn-warning mr-1 btnEditCategoria" data-id="' . $categoria->id . '"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btnDeleteCategoria" data-id="' . $categoria->id . '"><i class="fas fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['acciones'])
            ->make(true);
    }
}
