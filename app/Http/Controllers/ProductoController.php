<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Http\Requests\UpdateProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Services\Auditoria\AuditoriaService;

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

        // Separar los RUCs de los proveedores y precios antes de crear el producto
        $proveedoresRuc = $validated['proveedor_ruc'] ?? [];
        $preciosCosto = $validated['precioCosto'] ?? [];
        unset($validated['proveedor_ruc'], $validated['precioCosto']);

        $producto = Producto::create($validated);
        // Guardar la relación con los proveedores incluyendo precio_costo
        if (!empty($proveedoresRuc)) {
            $pivotData = [];
            foreach ($proveedoresRuc as $index => $ruc) {
                $pivotData[$ruc] = ['precio_costo' => $preciosCosto[$index] ?? 0];
            }
            $producto->proveedores()->attach($pivotData);
        }
        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'crear',
            'entidad' => 'Producto',
            'recurso_id' => $producto->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        return redirect()->route('admin.productos')->with('success', 'Producto registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'proveedores']);
        
        if (request()->wantsJson() || request()->expectsJson()) {
            $data = $producto->toArray();
            // Asegurar que los proveedores incluyen el pivot
            $data['proveedores'] = $producto->proveedores->toArray();
            return response()->json($data);
        }

        return view('admin.inventario.productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $producto->load('proveedores');
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

        // Separar los RUCs de los proveedores y precios antes de actualizar
        $proveedoresRuc = $validated['proveedor_ruc'] ?? [];
        $preciosCosto = $validated['precioCosto'] ?? [];
        unset($validated['proveedor_ruc'], $validated['precioCosto']);

        $producto->update($validated);
        // Actualizar la relación con los proveedores incluyendo precio_costo
        $pivotData = [];
        foreach ($proveedoresRuc as $index => $ruc) {
            $pivotData[$ruc] = ['precio_costo' => $preciosCosto[$index] ?? 0];
        }
        $producto->proveedores()->sync($pivotData);
        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'actualizar',
            'entidad' => 'Producto',
            'recurso_id' => $producto->id,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
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
        try {
            // Verificar si tiene stock disponible
            if ($producto->stock > 0) {
                $mensaje = 'No se puede eliminar el producto porque tiene stock disponible (' . $producto->stock . ' unidades).';
                
                // Log de operación fallida
                AuditoriaService::registrarOperacion([
                    'user_id' => Auth::id(),
                    'tipo_operacion' => 'eliminar',
                    'entidad' => 'Producto',
                    'recurso_id' => $producto->id,
                    'resultado' => 'fallido',
                    'mensaje_error' => $mensaje,
                ]);
                
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['message' => $mensaje], 400);
                }
                return redirect()->route('admin.productos')->with('error', $mensaje);
            }
            
            // SoftDelete - no eliminamos las relaciones, solo marcamos como eliminado
            $producto->delete();
            // Log de operación y sistema
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'eliminar',
                'entidad' => 'Producto',
                'recurso_id' => $producto->id,
                'resultado' => 'exitoso',
                'mensaje_error' => null,
            ]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['message' => 'Producto eliminado correctamente']);
            }
            return redirect()->route('admin.productos')->with('success', 'Producto eliminado correctamente');
        } catch (\Exception $e) {
            // Log de operación y sistema en caso de error
            AuditoriaService::registrarOperacion([
                'user_id' => Auth::id(),
                'tipo_operacion' => 'eliminar',
                'entidad' => 'Producto',
                'recurso_id' => $producto->id,
                'resultado' => 'fallido',
                'mensaje_error' => $e->getMessage(),
            ]);
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['error' => 'Error al eliminar el producto: ' . $e->getMessage()], 400);
            }
            return back()->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
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
                    <button class="btn btn-sm btn-success mr-1 btnShowProducto" data-id="' . $producto->id . '"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-warning mr-1 btnEditProducto" data-id="' . $producto->id . '"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btnDeleteProducto" data-id="' . $producto->id . '" data-nombre="' . htmlspecialchars($producto->nombre) . '"><i class="fas fa-trash"></i></button>
                </div>
            ';
            })
            ->rawColumns(['acciones', 'proveedores'])
            ->make(true);
    }

    public function exportPdf(Request $request) {
        // Construir la consulta base con relaciones (solo productos no eliminados)
        $query = \App\Models\Producto::with(['categoria', 'proveedores']);

        // Aplicar filtro de categoría si existe
        if ($request->has('categoria_id') && $request->categoria_id != '') {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Aplicar filtro de proveedor si existe
        if ($request->has('proveedor_ruc') && $request->proveedor_ruc != '') {
            $query->whereHas('proveedores', function ($q) use ($request) {
                $q->where('proveedores.ruc', $request->proveedor_ruc);
            });
        }

        // Obtener productos filtrados (sin eliminados gracias a SoftDeletes)
        $productos = $query->get();

        // Calcular total_vendidos para cada producto
        foreach ($productos as $producto) {
            $producto->total_vendidos = \App\Models\FacturaDetalle::where('producto_id', $producto->id)->sum('cantidad');
        }

        $pdf = Pdf::loadView('pdf.productos', [
            'productos' => $productos
        ]);
        return $pdf->stream();
    }

    public function exportExcel(Request $request) {
        // Construir la consulta base con relaciones (solo productos no eliminados)
        $query = \App\Models\Producto::with(['categoria', 'proveedores']);

        // Aplicar filtro de categoría si existe
        if ($request->has('categoria_id') && $request->categoria_id != '') {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Aplicar filtro de proveedor si existe
        if ($request->has('proveedor_ruc') && $request->proveedor_ruc != '') {
            $query->whereHas('proveedores', function ($q) use ($request) {
                $q->where('proveedores.ruc', $request->proveedor_ruc);
            });
        }

        // Obtener productos filtrados ordenados por código (sin eliminados gracias a SoftDeletes)
        $productos = $query->orderBy('codigo_barras')->get();

        // Calcular total_vendidos para cada producto
        foreach ($productos as $producto) {
            $producto->total_vendidos = \App\Models\FacturaDetalle::where('producto_id', $producto->id)->sum('cantidad');
        }

        // Crear una nueva hoja de cálculo
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Productos');

        // Definir estilos para el encabezado
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Definir estilos para las celdas de datos
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ];

        // Agregar encabezados
        $headers = [
            'Nro',
            'Código',
            'Nombre',
            'Descripción',
            'Categoría',
            'Stock',
            'Precio de Venta',
            'Proveedor/es',
            'Total Vendidos',
            'Fecha de Registro'
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        // Ajustar altura del encabezado
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Agregar datos
        $row = 2;
        $nro = 1;
        foreach ($productos as $producto) {
            $sheet->setCellValue('A' . $row, $nro);
            $sheet->setCellValue('B' . $row, $producto->codigo_barras);
            $sheet->setCellValue('C' . $row, $producto->nombre);
            $sheet->setCellValue('D' . $row, $producto->caracteristicas ?? '-');
            $sheet->setCellValue('E' . $row, $producto->categoria->nombre ?? '-');
            $sheet->setCellValue('F' . $row, $producto->cantidad_stock);
            $sheet->setCellValue('G' . $row, $producto->precio_unitario);
            
            // Proveedores
            if ($producto->proveedores->isEmpty()) {
                $sheet->setCellValue('H' . $row, 'Sin proveedores');
            } else {
                $proveedoresNombres = $producto->proveedores->pluck('nombre')->toArray();
                if (count($proveedoresNombres) > 1) {
                    $proveedoresConVinetas = array_map(function($nombre) { return '• ' . $nombre; }, $proveedoresNombres);
                    $sheet->setCellValue('H' . $row, implode("\n", $proveedoresConVinetas));
                } else {
                    $sheet->setCellValue('H' . $row, implode(', ', $proveedoresNombres));
                }
            }
            
            // Total vendidos
            $sheet->setCellValue('I' . $row, $producto->total_vendidos);
            
            // Fecha de registro
            $sheet->setCellValue('J' . $row, $producto->created_at->format('d-m-Y H:i'));

            // Aplicar estilos a la fila de datos
            foreach (range('A', 'J') as $col) {
                $sheet->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            // Alinear números a la derecha
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
            $nro++;
        }

        // Ajustar ancho de columnas automáticamente
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Crear el archivo Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        // Generar nombre del archivo
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = 'Productos_' . $timestamp . '.xlsx';

        // Enviar el archivo como descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
