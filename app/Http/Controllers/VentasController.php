<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Producto;
use App\Models\TipoPago;
use App\Models\Ajuste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\Auditoria\AuditoriaService;
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

        // Filtro por cliente (cédula o nombre)
        if ($request->filled('cliente_cedula')) {
            $termino = $request->cliente_cedula;
            $like = '%' . $termino . '%';

            $query->where(function ($sub) use ($like) {
                $sub->where('cliente_cedula', 'like', $like)
                    ->orWhereHas('cliente', function ($cliente) use ($like) {
                        $cliente->where('cedula', 'like', $like)
                            ->orWhere('nombres', 'like', $like)
                            ->orWhere('apellidos', 'like', $like)
                            ->orWhereRaw("nombres || ' ' || apellidos like ?", [$like])
                            ->orWhereRaw("apellidos || ' ' || nombres like ?", [$like]);
                    });
            });
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
        $ajuste = Ajuste::getOrCreate();
        $ivaPorcentaje = $ajuste->iva_porcentaje;
        $ultimaFactura = Factura::orderBy('created_at', 'desc')->value('numero_factura');
        $sugerenciaFactura = null;
        if (!empty($ajuste->prefijo_factura) && !empty($ajuste->siguiente_factura)) {
            $digitos = $ajuste->secuencial_digitos ?? 9;
            $sugerenciaFactura = $ajuste->prefijo_factura . str_pad((string) $ajuste->siguiente_factura, $digitos, '0', STR_PAD_LEFT);
        } elseif (!empty($ultimaFactura)) {
            $matches = [];
            if (preg_match('/^(\d{3}-\d{3}-)(\d+)$/', $ultimaFactura, $matches)) {
                $prefijo = $matches[1];
                $numero = $matches[2];
                $siguiente = str_pad((string) ((int) $numero + 1), strlen($numero), '0', STR_PAD_LEFT);
                $sugerenciaFactura = $prefijo . $siguiente;
            }
        }

        return view('admin.ventas.create', compact('clientes', 'productos', 'tiposPago', 'ivaPorcentaje', 'ultimaFactura', 'ajuste', 'sugerenciaFactura'));
    }

    /**
     * Guardar nueva venta (factura)
     */
    public function store(Request $request)
    {
        Log::info('VentasController@store iniciado', [
            'user_id' => Auth::id(),
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
            'tipo_pago_id' => 'required|exists:tipo_pagos,id',
            'consumidor_final' => 'nullable|boolean',
            'productos' => 'required|array',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
        ], [
            'numero_factura.required' => 'El número de factura es obligatorio.',
            'numero_factura.unique' => 'El número de factura ya está registrado.',
            'numero_factura.max' => 'El número de factura no debe superar 20 caracteres.',
            'cliente_cedula.required' => 'La cédula es obligatoria.',
            'cliente_nombres.required' => 'Los nombres son obligatorios.',
            'cliente_apellidos.required' => 'Los apellidos son obligatorios.',
            'cliente_email.email' => 'El correo electrónico no es válido.',
            'cliente_fecha_nacimiento.date' => 'La fecha de nacimiento no es válida.',
            'cliente_fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'tipo_pago_id.required' => 'El tipo de pago es obligatorio.',
            'tipo_pago_id.exists' => 'El tipo de pago seleccionado no es válido.',
            'productos.required' => 'Debes agregar al menos un producto.',
            'productos.array' => 'El listado de productos no es válido.',
            'productos.*.id.required' => 'Selecciona un producto válido.',
            'productos.*.id.exists' => 'El producto seleccionado no existe.',
            'productos.*.cantidad.required' => 'La cantidad es obligatoria.',
            'productos.*.cantidad.integer' => 'La cantidad debe ser un número entero.',
            'productos.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
            'productos.*.precio.required' => 'El precio es obligatorio.',
            'productos.*.precio.numeric' => 'El precio debe ser numérico.',
            'productos.*.precio.min' => 'El precio no puede ser negativo.',
        ]);

        try {
            DB::beginTransaction();

            $esMenor = false;
            if (!empty($validated['cliente_fecha_nacimiento'])) {
                $edad = Carbon::parse($validated['cliente_fecha_nacimiento'])->age;
                $esMenor = $edad < 18;
            }
            $usarConsumidorFinal = $request->boolean('consumidor_final');

            // Cliente “Consumidor Final” para facturar a menores
            $consumidorFinalCedula = '9999999999';
            $consumidorFinalDatos = [
                'nombres' => 'Consumidor',
                'apellidos' => 'Final',
                'email' => null,
                'telefono' => null,
            ];

            $warnings = [];

            // Crear o actualizar cliente según edad
            if ($esMenor || $usarConsumidorFinal) {
                $cliente = Cliente::firstOrCreate(
                    ['cedula' => $consumidorFinalCedula],
                    $consumidorFinalDatos
                );
            } else {
                $telefono = $validated['cliente_telefono'] ?? null;
                if (!empty($telefono)) {
                    $telefonoEnUso = Cliente::where('telefono', $telefono)
                        ->where('cedula', '!=', $validated['cliente_cedula'])
                        ->exists();
                    if ($telefonoEnUso) {
                        $validated['cliente_telefono'] = null;
                        $warnings[] = 'El teléfono ya está registrado en otro cliente. Se omitió en esta venta.';
                    }
                }

                $email = $validated['cliente_email'] ?? null;
                if (!empty($email)) {
                    $emailEnUso = Cliente::where('email', $email)
                        ->where('cedula', '!=', $validated['cliente_cedula'])
                        ->exists();
                    if ($emailEnUso) {
                        $validated['cliente_email'] = null;
                        $warnings[] = 'El correo ya está registrado en otro cliente. Se omitió en esta venta.';
                    }
                }

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

            // Calcular IVA desde ajustes
            $ajuste = Ajuste::getOrCreate();
            $ivaPorcentaje = $ajuste->iva_porcentaje;
            $iva = $subtotal * ($ivaPorcentaje / 100);
            $total = $subtotal + $iva;

            // Crear factura
            $factura = Factura::create([
                'numero_factura' => $validated['numero_factura'],
                'fecha_hora' => now(),
                'cliente_cedula' => $cliente->cedula,
                'usuario_id' => Auth::id(),
                'tipo_pago_id' => $validated['tipo_pago_id'],
                'subtotal' => $subtotal,
                'iva' => $iva,
                'iva_porcentaje' => $ivaPorcentaje,
                'total' => $total,
            ]);

            if (!empty($ajuste->prefijo_factura) && !empty($ajuste->siguiente_factura)) {
                $digitos = $ajuste->secuencial_digitos ?? 9;
                $sugerenciaFactura = $ajuste->prefijo_factura . str_pad((string) $ajuste->siguiente_factura, $digitos, '0', STR_PAD_LEFT);
                if ($validated['numero_factura'] === $sugerenciaFactura) {
                    $ajuste->update(['siguiente_factura' => $ajuste->siguiente_factura + 1]);
                }
            }

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
            // Registrar log de operación: venta registrada correctamente
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'crear',
                'entidad' => 'Factura',
                'recurso_id' => $factura->id,
                'resultado' => 'exitoso',
                'mensaje_error' => null,
            ]);

            $redirect = redirect()->route('admin.ventas.show', $factura->id)
                ->with('success', 'Venta registrada correctamente. Factura #' . $factura->numero_factura);

            if (!empty($warnings)) {
                $redirect->with('warning', implode(' ', $warnings));
            }

            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            // Registrar log de operación: error al registrar venta
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'crear',
                'entidad' => 'Factura',
                'recurso_id' => null,
                'resultado' => 'fallido',
                'mensaje_error' => $e->getMessage(),
            ]);
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
        $ajuste = Ajuste::getOrCreate();
        return view('admin.ventas.show', compact('factura', 'ajuste'));
    }

    /**
     * Imprimir factura
     */
    public function print(Factura $factura)
    {
        $factura->load('cliente', 'tipoPago', 'detalles.producto');
        $ajuste = Ajuste::getOrCreate();
        return view('admin.ventas.print', compact('factura', 'ajuste'));
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

            // Recalcular siguiente secuencial si aplica
            $ajuste = Ajuste::getOrCreate();
            if (!empty($ajuste->prefijo_factura) && !empty($ajuste->secuencial_digitos)) {
                $prefijo = $ajuste->prefijo_factura;
                $digitos = (int) ($ajuste->secuencial_digitos ?? 9);

                $numeros = Factura::where('numero_factura', 'like', $prefijo . '%')
                    ->pluck('numero_factura');

                $max = 0;
                foreach ($numeros as $numero) {
                    if (str_starts_with($numero, $prefijo)) {
                        $secuencial = substr($numero, strlen($prefijo));
                        if (ctype_digit($secuencial)) {
                            $valor = (int) $secuencial;
                            if ($valor > $max) {
                                $max = $valor;
                            }
                        }
                    }
                }

                $ajuste->update(['siguiente_factura' => $max > 0 ? $max + 1 : 1]);
            }

            DB::commit();

            return redirect()->route('admin.ventas.index')
                ->with('success', 'Factura #' . $factura->numero_factura . ' anulada correctamente. Stock restaurado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular la factura: ' . $e->getMessage());
        }
    }
}
