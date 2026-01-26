<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        return view('admin.inventario.productos.index', compact('categorias', 'proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductoRequest $request)
    {
        $validated = $request->validated();
        
        // Separar los RUCs de los proveedores antes de crear el producto
        $proveedoresRuc = $validated['proveedor_ruc'] ?? [];
        unset($validated['proveedor_ruc']);

        $producto = Producto::create($validated);
        
        // Guardar la relación con los proveedores
        if (!empty($proveedoresRuc)) {
            $producto->proveedores()->attach($proveedoresRuc);
        }

        return redirect()->route('admin.productos')->with('success', 'Producto registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::all();
        $proveedores = Proveedor::all();
        
        // Render the form partial indicating it will be used inside a modal
        $html = view('admin.inventario.productos.partials.form', [
            'producto' => $producto,
            'categorias' => $categorias,
            'proveedores' => $proveedores,
            'isModal' => true,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductoRequest $request, Producto $producto)
    {
        $validated = $request->validated();
        
        // Separar los RUCs de los proveedores antes de actualizar
        $proveedoresRuc = $validated['proveedor_ruc'] ?? [];
        unset($validated['proveedor_ruc']);

        $producto->update($validated);
        
        // Actualizar la relación con los proveedores
        $producto->proveedores()->sync($proveedoresRuc);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Producto actualizado correctamente',
                'producto' => $producto->load(['categoria','proveedores'])
            ]);
        }

        return redirect()->route('admin.productos')->with('success', 'Producto actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        $producto->proveedores()->detach();
        $producto->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['message' => 'Producto eliminado correctamente']);
        }

        return redirect()->route('admin.productos')->with('success', 'Producto eliminado correctamente');
    }

    public function datatables(Request $request)
    {
        $query = Producto::select([
            'id',
            'codigo_barras',
            'nombre',
            'cantidad_stock',
            'precio_unitario',
            'tiene_iva',
            'categoria_id',
            'created_at'
        ])->with(['categoria', 'proveedores']);

        if ($request->categoryid) {
            $query->where('categoria_id', $request->categoryid);
        }

        if ($request->provider_ruc) {
            $query->whereHas('proveedores', function ($q) use ($request) {
                $q->where('proveedores.ruc', $request->provider_ruc);
            });
        }

        return DataTables::eloquent($query)
            ->addColumn('categoria', function ($producto) {
                return $producto->categoria->nombre ?? '-';
            })
            ->addColumn('iva', function ($producto) {
                return $producto->tiene_iva ? 'Sí' : 'No';
            })
            ->addColumn('proveedores', function ($producto) {
                $proveedores = $producto->proveedores;
                
                if ($proveedores->isEmpty()) {
                    return '<span class="text-muted">Sin proveedores</span>';
                }

                $nombres = $proveedores->pluck('nombre')->toArray();
                $nombresCortado = implode(', ', array_slice($nombres, 0, 1));
                
                if (count($nombres) > 1) {
                    $nombresCortado .= ' ...';
                    $todosLosNombres = implode('<br>', $nombres);
                    
                    return '<span class="d-inline-block" data-toggle="tooltip" data-html="true" title="' . htmlspecialchars($todosLosNombres) . '">' . htmlspecialchars($nombresCortado) . '</span>';
                }
                
                return htmlspecialchars($nombresCortado);
            })
            ->addColumn('acciones', function ($producto) {
                return '
                <div class="d-flex justify-content-center">
                    <button class="btn btn-sm btn-info mr-1 btnEditProducto" data-id="' . $producto->id . '"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btnDeleteProducto" data-id="' . $producto->id . '" data-nombre="' . htmlspecialchars($producto->nombre) . '"><i class="fas fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['acciones', 'proveedores'])
            ->make(true);
    }
}

