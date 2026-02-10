<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\TipoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Auditoria\AuditoriaService;

class CompraController extends Controller {
    /**
     * Display a listing of compras
     */
    public function index(Request $request) {
        $compras = Compra::with(['proveedor', 'usuario', 'tipoPago'])
            ->latest('fecha_compra')
            ->paginate(15);

        return view('admin.compras.index', compact('compras'));
    }

    /**
     * Show the form for creating a new compra
     */
    public function create() {
        $proveedores = Proveedor::orderBy('nombre')->get();
        $tiposPago = TipoPago::all();

        return view('admin.compras.create', compact('proveedores', 'tiposPago'));
    }

    /**
     * Store a newly created compra in storage
     */
    public function store(Request $request) {
        try {
            $validated = $request->validate([
                'proveedor_ruc' => 'required|exists:proveedores,ruc',
                'fecha_compra' => 'required|date',
                'tipo_pago_id' => 'required|exists:tipo_pagos,id',
                'descripcion' => 'nullable|string',
                'detalles' => 'required|array|min:1',
                'detalles.*.producto_id' => 'required|exists:productos,id',
                'detalles.*.cantidad' => 'required|integer|min:1',
                'detalles.*.precio_unitario' => 'required|numeric|min:0.01',
            ]);

            DB::beginTransaction();

            // Validar que todos los productos pertenecen al mismo proveedor
            $this->validarProductosDelProveedor(
                $request->input('proveedor_ruc'),
                $request->input('detalles')
            );

            // Validar que la cantidad no supere el stock máximo
            $this->validarStockMaximo($request->input('detalles'));

            // Crear compra
            $compra = Compra::create([
                'numero_compra' => Compra::generarNumeroCompra(),
                'fecha_compra' => $request->fecha_compra,
                'proveedor_ruc' => $request->proveedor_ruc,
                'descripcion' => $request->descripcion,
                'tipo_pago_id' => $request->tipo_pago_id,
                'usuario_id' => Auth::id(),
                'estado' => 'pendiente',
            ]);

            // Crear detalles de compra
            foreach ($request->input('detalles') as $detalle) {
                $producto = Producto::findOrFail($detalle['producto_id']);
                $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $subtotal,
                ]);
            }
            // Calcular totales
            $compra->calcularTotal();
            DB::commit();
            // Log de operación y sistema
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'crear',
                'entidad' => 'Compra',
                'recurso_id' => $compra->id,
                'resultado' => 'exitoso',
                'mensaje_error' => null,
            ]);
            return redirect()->route('admin.compras.show', $compra->id)
                ->with('success', 'Compra realizada exitosamente. Número de compra: ' . $compra->numero_compra);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log de operación y sistema en caso de error
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'crear',
                'entidad' => 'Compra',
                'recurso_id' => null,
                'resultado' => 'fallido',
                'mensaje_error' => $e->getMessage(),
            ]);
            return back()->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified compra
     */
    public function show(Compra $compra) {
        $compra->load(['proveedor', 'usuario', 'tipoPago', 'detalles.producto']);
        return view('admin.compras.show', compact('compra'));
    }

    /**
     * Show the form for editing the specified compra
     */
    public function edit(Compra $compra) {
        if ($compra->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar compras pendientes');
        }

        $compra->load(['proveedor', 'detalles.producto']);
        $proveedores = Proveedor::orderBy('nombre')->get();
        $tiposPago = TipoPago::all();

        return view('admin.compras.edit', compact('compra', 'proveedores', 'tiposPago'));
    }

    /**
     * Update the specified compra in storage
     */
    public function update(Request $request, Compra $compra) {
        if ($compra->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden editar compras pendientes');
        }

        $request->validate([
            'proveedor_ruc' => 'required|exists:proveedores,ruc',
            'fecha_compra' => 'required|date',
            'tipo_pago_id' => 'required|exists:tipo_pagos,id',
            'descripcion' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            // Validar que todos los productos pertenecen al mismo proveedor
            $this->validarProductosDelProveedor(
                $request->input('proveedor_ruc'),
                $request->input('detalles')
            );

            // Validar que la cantidad no supere el stock máximo
            $this->validarStockMaximo($request->input('detalles'));

            // Actualizar compra
            $compra->update([
                'fecha_compra' => $request->fecha_compra,
                'proveedor_ruc' => $request->proveedor_ruc,
                'descripcion' => $request->descripcion,
                'tipo_pago_id' => $request->tipo_pago_id,
            ]);

            // Eliminar detalles anteriores
            $compra->detalles()->delete();

            // Crear nuevos detalles
            foreach ($request->input('detalles') as $detalle) {
                $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];

                CompraDetalle::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $subtotal,
                ]);
            }

            // Recalcular totales
            $compra->calcularTotal();

            DB::commit();

            // Log de operación y sistema
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'actualizar',
                'entidad' => 'Compra',
                'recurso_id' => $compra->id,
                'resultado' => 'exitoso',
                'mensaje_error' => null,
            ]);
            return redirect()->route('compras.show', $compra->id)
                ->with('success', 'Compra actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log de operación y sistema en caso de error
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'actualizar',
                'entidad' => 'Compra',
                'recurso_id' => $compra->id,
                'resultado' => 'fallido',
                'mensaje_error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Error al actualizar la compra: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cambiar estado de compra a recibida
     */
    public function recibir(Request $request, Compra $compra) {
        if ($compra->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden recibir compras pendientes');
        }

        try {
            DB::beginTransaction();

            // Actualizar stock de productos
            foreach ($compra->detalles as $detalle) {
                $producto = $detalle->producto;
                $producto->cantidad_stock += $detalle->cantidad;
                $producto->save();
            }

            // Cambiar estado
            $compra->update([
                'estado' => 'recibida',
                'fecha_recepcion' => now(),
            ]);

            DB::commit();

            // Log de operación y sistema
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'recibir',
                'entidad' => 'Compra',
                'recurso_id' => $compra->id,
                'resultado' => 'exitoso',
                'mensaje_error' => null,
            ]);
            return back()->with('success', 'Compra marcada como recibida. Stock actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log de operación y sistema en caso de error
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'recibir',
                'entidad' => 'Compra',
                'recurso_id' => $compra->id,
                'resultado' => 'fallido',
                'mensaje_error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Error al recibir la compra: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado de compra a cancelada
     */
    public function cancelar(Request $request, Compra $compra) {
        $request->validate([
            'razon' => 'required|string|min:10',
        ]);

        if ($compra->estado !== 'pendiente') {
            return back()->with('error', 'Solo se pueden cancelar compras pendientes');
        }

        try {
            $compra->update([
                'estado' => 'anulada',
                'observaciones' => $request->razon,
            ]);

            // Log de operación y sistema
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'cancelar',
                'entidad' => 'Compra',
                'recurso_id' => $compra->id,
                'resultado' => 'exitoso',
                'mensaje_error' => null,
            ]);
            return back()->with('success', 'Compra cancelada exitosamente');

        } catch (\Exception $e) {
            // Log de operación y sistema en caso de error
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'cancelar',
                'entidad' => 'Compra',
                'recurso_id' => $compra->id,
                'resultado' => 'fallido',
                'mensaje_error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Error al cancelar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Obtener productos de un proveedor (AJAX)
     */
    public function obtenerProductosProveedor($proveedorRuc) {
        try {
            $proveedor = Proveedor::findOrFail($proveedorRuc);
            
            // Obtener productos del proveedor
            $productos = DB::table('producto_proveedores')
                ->join('productos', 'producto_proveedores.producto_id', '=', 'productos.id')
                ->where('producto_proveedores.proveedor_ruc', $proveedorRuc)
                ->select(
                    'productos.id',
                    'productos.nombre',
                    'productos.codigo_barras',
                    'producto_proveedores.precio_costo',
                    'productos.tiene_iva'
                )
                ->orderBy('productos.nombre')
                ->get();
            
            return response()->json($productos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Generar factura de compra (PDF)
     */
    public function generarFactura(Compra $compra) {
        // Esta función puede ser implementada con una librería como TCPDF o DomPDF
        // Por ahora, retornamos una respuesta JSON
        return response()->json([
            'numero_compra' => $compra->numero_compra,
            'fecha_compra' => $compra->fecha_compra,
            'proveedor' => $compra->proveedor->nombre,
            'total' => $compra->total,
            'detalles' => $compra->detalles()->count(),
        ]);
    }

    /**
     * Validar que todos los productos pertenecen al proveedor especificado
     */
    private function validarProductosDelProveedor($proveedorRuc, $detalles) {
        foreach ($detalles as $detalle) {
            $producto = Producto::findOrFail($detalle['producto_id']);
            
            // Verificar que el producto existe en la tabla producto_proveedores
            $existe = DB::table('producto_proveedores')
                ->where('proveedor_ruc', $proveedorRuc)
                ->where('producto_id', $detalle['producto_id'])
                ->exists();

            if (!$existe) {
                throw new \Exception(
                    "El producto '{$producto->nombre}' no es suministrado por este proveedor."
                );
            }
        }
    }

    /**
     * Validar que la cantidad no supere el stock máximo de cada producto
     */
    private function validarStockMaximo($detalles) {
        foreach ($detalles as $detalle) {
            $producto = Producto::findOrFail($detalle['producto_id']);
            $cantidadCompra = $detalle['cantidad'];
            $stockActual = $producto->cantidad_stock;
            $nuevoStock = $stockActual + $cantidadCompra;

            if ($nuevoStock > $producto->stock_maximo) {
                throw new \Exception(
                    "El producto '{$producto->nombre}' no puede tener más de {$producto->stock_maximo} unidades en stock. " .
                    "Stock actual: {$stockActual}, cantidad a comprar: {$cantidadCompra}, total resultaría en: {$nuevoStock}."
                );
            }
        }
    }
}
