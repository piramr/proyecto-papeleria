<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Producto;
use App\Models\TipoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VentasController extends Controller
{
    /**
     * Mostrar lista de ventas realizadas
     */
    public function index(Request $request)
    {
        $query = Factura::with('cliente', 'tipoPago');

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_hasta);
        }

        // Filtro por cliente
        if ($request->filled('cliente_cedula')) {
            $query->where('cliente_cedula', 'like', '%' . $request->cliente_cedula . '%');
        }

        // Filtro por tipo de pago
        if ($request->filled('tipo_pago_id')) {
            $query->where('tipo_pago_id', $request->tipo_pago_id);
        }

        // Filtro por número de factura
        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->numero_factura . '%');
        }

        $facturas = $query->orderBy('created_at', 'desc')->paginate(15);
        $clientes = Cliente::orderBy('nombres')->get();
        $tiposPago = TipoPago::all();

        return view('admin.ventas.index', compact('facturas', 'clientes', 'tiposPago'));
    }

    /**
     * Mostrar formulario para crear nueva venta
     */
    public function create()
    {
        $clientes = Cliente::all();
        $productos = Producto::where('cantidad_stock', '>', 0)->get();
        $tiposPago = TipoPago::all();
        
        return view('admin.ventas.create', compact('clientes', 'productos', 'tiposPago'));
    }

    /**
     * Guardar nueva venta (factura)
     */
    public function store(Request $request)
    {
        Log::info('VentasController@store iniciado', [
            'user_id' => auth()->id(),
            'payload' => $request->all(),
        ]);

        $validated = $request->validate([
            'numero_factura' => 'required|string|unique:facturas|max:20',
            'cliente_cedula' => 'required|string|max:20',
            'cliente_nombres' => 'required|string|max:100',
            'cliente_apellidos' => 'required|string|max:100',
            'cliente_email' => 'nullable|email|max:100',
            'cliente_telefono' => 'nullable|string|max:20',
            'cliente_fecha_nacimiento' => 'nullable|date|before:today',
            'tipo_pago_id' => 'required|exists:tipos_pago,id',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $esMenor = false;
            if (!empty($validated['cliente_fecha_nacimiento'])) {
                $edad = Carbon::parse($validated['cliente_fecha_nacimiento'])->age;
                $esMenor = $edad < 18;
            }

            // Cliente “Consumidor Final” para facturar a menores
            $consumidorFinalCedula = '9999999999';
            $consumidorFinalDatos = [
                'nombres' => 'Consumidor',
                'apellidos' => 'Final',
                'email' => null,
                'telefono' => null,
            ];

            // Crear o actualizar cliente según edad
            if ($esMenor) {
                $cliente = Cliente::firstOrCreate(
                    ['cedula' => $consumidorFinalCedula],
                    $consumidorFinalDatos
                );
            } else {
                $cliente = Cliente::updateOrCreate(
                    ['cedula' => $validated['cliente_cedula']],
                    [
                        'nombres' => $validated['cliente_nombres'],
                        'apellidos' => $validated['cliente_apellidos'],
                        'email' => $validated['cliente_email'],
                        'telefono' => $validated['cliente_telefono'],
                        'fecha_nacimiento' => $validated['cliente_fecha_nacimiento'] ?? null,
                    ]
                );
            }

            // Calcular totales
            $subtotal = 0;
            foreach ($validated['productos'] as $item) {
                $subtotal += $item['cantidad'] * $item['precio'];
            }

            // Calcular IVA del 15%
            $iva = $subtotal * 0.15;
            $total = $subtotal + $iva;

            // Crear factura
            $factura = Factura::create([
                'numero_factura' => $validated['numero_factura'],
                'fecha_hora' => now(),
                'cliente_cedula' => $cliente->cedula,
                'usuario_id' => auth()->id(),
                'tipo_pago_id' => $validated['tipo_pago_id'],
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => $total,
            ]);

            // Crear detalles de factura y actualizar stock
            foreach ($validated['productos'] as $item) {
                FacturaDetalle::create([
                    'factura_id' => $factura->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'total' => $item['cantidad'] * $item['precio'],
                ]);

                // Restar del stock
                $producto = Producto::find($item['id']);
                $producto->decrement('cantidad_stock', $item['cantidad']);
            }

            DB::commit();
            
            return redirect()->route('admin.ventas.show', $factura->id)
                ->with('success', 'Venta registrada correctamente. Factura #' . $factura->numero_factura);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar venta', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar factura generada
     */
    public function show(Factura $factura)
    {
        $factura->load('cliente', 'tipoPago', 'detalles.producto');
        return view('admin.ventas.show', compact('factura'));
    }

    /**
     * Imprimir factura
     */
    public function print(Factura $factura)
    {
        $factura->load('cliente', 'tipoPago', 'detalles.producto');
        return view('admin.ventas.print', compact('factura'));
    }

    /**
     * Obtener productos por AJAX
     */
    public function getProductos()
    {
        $productos = Producto::select('id', 'nombre', 'precio_unitario', 'cantidad_stock')
            ->where('cantidad_stock', '>', 0)
            ->get();
        
        return response()->json($productos);
    }

    /**
     * Obtener cliente por cédula
     */
    public function getClienteByCedula($cedula)
    {
        $cliente = Cliente::where('cedula', $cedula)->first();
        
        if ($cliente) {
            return response()->json([
                'success' => true,
                'cliente' => $cliente
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Cliente no encontrado'
        ]);
    }

    /**
     * Anular una factura
     */
    public function destroy(Factura $factura)
    {
        try {
            DB::beginTransaction();

            // Devolver stock de los productos
            foreach ($factura->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                $producto->increment('cantidad_stock', $detalle->cantidad);
            }

            // Eliminar detalles y factura
            $factura->detalles()->delete();
            $factura->delete();

            DB::commit();

            return redirect()->route('admin.ventas.index')
                ->with('success', 'Factura #' . $factura->id . ' anulada correctamente. Stock restaurado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular la factura: ' . $e->getMessage());
        }
    }
}
