<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\TipoPago;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->ventasQuery($request);

        $totales = (clone $query)
            ->selectRaw('COUNT(*) as total_facturas, COALESCE(SUM(subtotal), 0) as subtotal, COALESCE(SUM(total), 0) as total')
            ->first();

        $facturas = $query
            ->orderBy('fecha_hora', 'desc')
            ->paginate(15)
            ->withQueryString();

        $tiposPago = TipoPago::orderBy('nombre')->get();
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();
        $proveedores = \App\Models\Proveedor::orderBy('nombre')->get();

        return view('admin.reportes.index', compact('facturas', 'tiposPago', 'totales', 'categorias', 'proveedores'));
    }

    public function ventasPdf(Request $request)
    {
        $facturas = $this->ventasQuery($request)
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $totales = (clone $this->ventasQuery($request))
            ->selectRaw('COUNT(*) as total_facturas, COALESCE(SUM(subtotal), 0) as subtotal, COALESCE(SUM(total), 0) as total')
            ->first();

        $pdf = Pdf::loadView('pdf.ventas', [
            'facturas' => $facturas,
            'totales' => $totales,
        ]);

        return $pdf->stream();
    }

    public function ventasExcel(Request $request)
    {
        $facturas = $this->ventasQuery($request)
            ->orderBy('fecha_hora', 'desc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ventas');

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

        $headers = [
            'Nro',
            'Factura #',
            'Cliente',
            'Cédula',
            'Fecha',
            'Pago',
            'Subtotal',
            'Total',
            'Usuario',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        $sheet->getRowDimension(1)->setRowHeight(30);

        $row = 2;
        $nro = 1;
        foreach ($facturas as $factura) {
            $clienteNombre = trim(($factura->cliente->nombres ?? '') . ' ' . ($factura->cliente->apellidos ?? ''));
            $sheet->setCellValue('A' . $row, $nro);
            $sheet->setCellValue('B' . $row, $factura->id);
            $sheet->setCellValue('C' . $row, $clienteNombre ?: '-');
            $sheet->setCellValue('D' . $row, $factura->cliente->cedula ?? '-');
            $sheet->setCellValue('E' . $row, $factura->fecha_hora ? $factura->fecha_hora->format('d-m-Y H:i') : '-');
            $sheet->setCellValue('F' . $row, $factura->tipoPago->nombre ?? '-');
            $sheet->setCellValue('G' . $row, $factura->subtotal);
            $sheet->setCellValue('H' . $row, $factura->total);
            $sheet->setCellValue('I' . $row, $factura->usuario->name ?? '-');

            foreach (range('A', 'I') as $col) {
                $sheet->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
            $nro++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = 'Reporte_Ventas_' . $timestamp . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function comprasPdf(Request $request)
    {
        $compras = $this->comprasQuery($request)
            ->orderBy('fecha_compra', 'desc')
            ->get();

        $totales = (clone $this->comprasQuery($request))
            ->selectRaw('COUNT(*) as total_compras, COALESCE(SUM(subtotal), 0) as subtotal, COALESCE(SUM(iva), 0) as iva, COALESCE(SUM(total), 0) as total')
            ->first();

        $pdf = Pdf::loadView('pdf.compras', [
            'compras' => $compras,
            'totales' => $totales,
        ]);

        return $pdf->stream();
    }

    public function comprasExcel(Request $request)
    {
        $compras = $this->comprasQuery($request)
            ->orderBy('fecha_compra', 'desc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Compras');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        $headers = ['Nro', 'N° Compra', 'Proveedor', 'Fecha Compra', 'Estado', 'Tipo Pago', 'Subtotal', 'IVA', 'Total', 'Usuario'];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        $row = 2;
        $nro = 1;
        foreach ($compras as $compra) {
            $sheet->setCellValue('A' . $row, $nro);
            $sheet->setCellValue('B' . $row, $compra->numero_compra);
            $sheet->setCellValue('C' . $row, $compra->proveedor->nombre ?? '-');
            $sheet->setCellValue('D' . $row, $compra->fecha_compra ? $compra->fecha_compra->format('d-m-Y H:i') : '-');
            $sheet->setCellValue('E' . $row, $compra->estado ?? '-');
            $sheet->setCellValue('F' . $row, $compra->tipoPago->nombre ?? '-');
            $sheet->setCellValue('G' . $row, $compra->subtotal);
            $sheet->setCellValue('H' . $row, $compra->iva);
            $sheet->setCellValue('I' . $row, $compra->total);
            $sheet->setCellValue('J' . $row, $compra->usuario->name ?? '-');

            foreach (range('A', 'J') as $col) {
                $sheet->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
            $nro++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = 'Reporte_Compras_' . $timestamp . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function productosVendidosExcel(Request $request)
    {
        // Obtener todos los productos con su información de ventas
        $productos = \App\Models\Producto::with(['categoria'])
            ->select('productos.*')
            ->leftJoin('factura_detalles', 'productos.id', '=', 'factura_detalles.producto_id')
            ->selectRaw('COALESCE(SUM(factura_detalles.cantidad), 0) as total_vendido')
            ->selectRaw('COALESCE(SUM(factura_detalles.total), 0) as ingresos_generados')
            ->selectRaw('MAX(facturas.fecha_hora) as ultima_venta')
            ->leftJoin('facturas', 'factura_detalles.factura_id', '=', 'facturas.id')
            ->groupBy('productos.id')
            ->orderBy('total_vendido', 'desc')
            ->get();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        // Hoja 1: Productos más vendidos
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Más Vendidos');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        $headers = ['Nro', 'Código', 'Producto', 'Categoría', 'Precio Unit.', 'Unidades Vendidas', 'Ingresos Generados', 'Stock Actual', 'Última Venta'];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet1->setCellValue($col . '1', $header);
            $sheet1->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        // Top 20 productos más vendidos
        $row = 2;
        $nro = 1;
        $masVendidos = $productos->filter(function($p) { return $p->total_vendido > 0; })->take(20);
        
        foreach ($masVendidos as $producto) {
            $sheet1->setCellValue('A' . $row, $nro);
            $sheet1->setCellValue('B' . $row, $producto->codigo_barras);
            $sheet1->setCellValue('C' . $row, $producto->nombre);
            $sheet1->setCellValue('D' . $row, $producto->categoria->nombre ?? '-');
            $sheet1->setCellValue('E' . $row, $producto->precio_unitario);
            $sheet1->setCellValue('F' . $row, $producto->total_vendido);
            $sheet1->setCellValue('G' . $row, $producto->ingresos_generados);
            $sheet1->setCellValue('H' . $row, $producto->cantidad_stock);
            $sheet1->setCellValue('I' . $row, $producto->ultima_venta ? \Carbon\Carbon::parse($producto->ultima_venta)->format('d-m-Y') : '-');

            foreach (range('A', 'I') as $col) {
                $sheet1->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            $sheet1->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet1->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet1->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet1->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
            $nro++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet1->getColumnDimension($col)->setAutoSize(true);
        }

        // Hoja 2: Productos menos vendidos / Baja rotación
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Menos Vendidos');

        $headerStyle2 = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet2->setCellValue($col . '1', $header);
            $sheet2->getStyle($col . '1')->applyFromArray($headerStyle2);
            $col++;
        }

        // Productos con poca o ninguna venta
        $row = 2;
        $nro = 1;
        $menosVendidos = $productos->sortBy('total_vendido')->take(20);
        
        foreach ($menosVendidos as $producto) {
            $sheet2->setCellValue('A' . $row, $nro);
            $sheet2->setCellValue('B' . $row, $producto->codigo_barras);
            $sheet2->setCellValue('C' . $row, $producto->nombre);
            $sheet2->setCellValue('D' . $row, $producto->categoria->nombre ?? '-');
            $sheet2->setCellValue('E' . $row, $producto->precio_unitario);
            $sheet2->setCellValue('F' . $row, $producto->total_vendido);
            $sheet2->setCellValue('G' . $row, $producto->ingresos_generados);
            $sheet2->setCellValue('H' . $row, $producto->cantidad_stock);
            $sheet2->setCellValue('I' . $row, $producto->ultima_venta ? \Carbon\Carbon::parse($producto->ultima_venta)->format('d-m-Y') : 'Nunca');

            foreach (range('A', 'I') as $col) {
                $sheet2->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            $sheet2->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet2->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet2->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet2->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
            $nro++;
        }

        foreach (range('A', 'I') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = 'Reporte_Productos_Vendidos_' . $timestamp . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function gananciasExcel(Request $request)
    {
        // Obtener todas las facturas para calcular ganancias totales
        $facturas = \App\Models\Factura::with(['detalles.producto', 'cliente'])->get();
        
        // Calcular ganancias totales
        $gananciaTotal = 0;
        $costoTotal = 0;
        $ventasTotal = 0;
        
        foreach ($facturas as $factura) {
            foreach ($factura->detalles as $detalle) {
                // Costo promedio del producto
                $costoPromedio = \App\Models\CompraDetalle::where('producto_id', $detalle->producto_id)
                    ->avg('precio_unitario') ?? 0;
                
                $costoDetalle = $costoPromedio * $detalle->cantidad;
                $ventaDetalle = $detalle->precio_unitario * $detalle->cantidad;
                
                $costoTotal += $costoDetalle;
                $ventasTotal += $ventaDetalle;
                $gananciaTotal += ($ventaDetalle - $costoDetalle);
            }
        }
        
        $margenGeneral = $ventasTotal > 0 ? (($gananciaTotal / $ventasTotal) * 100) : 0;
        
        // Análisis por categoría
        $categorias = \App\Models\Categoria::with('productos')->get();
        $gananciaPorCategoria = [];
        
        foreach ($categorias as $categoria) {
            $costoCategoria = 0;
            $ventaCategoria = 0;
            $cantidadVendida = 0;
            
            foreach ($categoria->productos as $producto) {
                $cantidadVendida += \App\Models\FacturaDetalle::where('producto_id', $producto->id)
                    ->sum('cantidad');
                    
                $costoPromedio = \App\Models\CompraDetalle::where('producto_id', $producto->id)
                    ->avg('precio_unitario') ?? 0;
                
                $detalles = \App\Models\FacturaDetalle::where('producto_id', $producto->id)->get();
                foreach ($detalles as $detalle) {
                    $costoCategoria += $costoPromedio * $detalle->cantidad;
                    $ventaCategoria += $detalle->precio_unitario * $detalle->cantidad;
                }
            }
            
            $gananciaPorCategoria[] = [
                'nombre' => $categoria->nombre,
                'costo_total' => $costoCategoria,
                'venta_total' => $ventaCategoria,
                'ganancia_total' => $ventaCategoria - $costoCategoria,
                'cantidad_vendida' => $cantidadVendida,
                'margen' => $ventaCategoria > 0 ? (($ventaCategoria - $costoCategoria) / $ventaCategoria) * 100 : 0
            ];
        }
        
        // Análisis por producto
        $query = \App\Models\Producto::with(['categoria', 'proveedores']);

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        $productos = $query->orderBy('nombre')->get();

        foreach ($productos as $producto) {
            // Obtener precio promedio de compra del producto
            $precioPromedioCompra = \App\Models\CompraDetalle::where('producto_id', $producto->id)
                ->avg('precio_unitario') ?? 0;

            $producto->precio_compra_promedio = $precioPromedioCompra;
            $producto->precio_venta = $producto->precio_unitario;
            $producto->ganancia_unitaria = $producto->precio_venta - $precioPromedioCompra;
            $producto->margen_porcentaje = $precioPromedioCompra > 0 
                ? (($producto->ganancia_unitaria / $precioPromedioCompra) * 100) 
                : 0;
            
            // Total vendido
            $producto->total_vendidos = \App\Models\FacturaDetalle::where('producto_id', $producto->id)
                ->sum('cantidad');
            
            $producto->ganancia_total = $producto->ganancia_unitaria * $producto->total_vendidos;
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ganancias');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        // HOJA 1: RESUMEN GENERAL
        $headers = ['Concepto', 'Valor'];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        $row = 2;
        $sheet->setCellValue('A' . $row, 'Total Ventas');
        $sheet->setCellValue('B' . $row, $ventasTotal);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($dataStyle);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Costo');
        $sheet->setCellValue('B' . $row, $costoTotal);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($dataStyle);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'GANANCIA TOTAL');
        $sheet->setCellValue('B' . $row, $gananciaTotal);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($dataStyle);
        $sheet->getStyle('A' . $row . ':B' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCFFCC');
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Margen General (%)');
        $sheet->setCellValue('B' . $row, number_format($margenGeneral, 2));
        $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A' . $row . ':B' . $row)->applyFromArray($dataStyle);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);

        // HOJA 2: RESUMEN POR CATEGORÍA
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Por Categoría');

        $headers2 = ['Categoría', 'Costo Total', 'Venta Total', 'Ganancia Total', 'Cantidad Vendida', 'Margen (%)'];
        $col = 'A';
        foreach ($headers2 as $header) {
            $sheet2->setCellValue($col . '1', $header);
            $sheet2->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        $row = 2;
        foreach ($gananciaPorCategoria as $cat) {
            $sheet2->setCellValue('A' . $row, $cat['nombre']);
            $sheet2->setCellValue('B' . $row, $cat['costo_total']);
            $sheet2->setCellValue('C' . $row, $cat['venta_total']);
            $sheet2->setCellValue('D' . $row, $cat['ganancia_total']);
            $sheet2->setCellValue('E' . $row, $cat['cantidad_vendida']);
            $sheet2->setCellValue('F' . $row, number_format($cat['margen'], 2));

            foreach (range('A', 'F') as $col) {
                $sheet2->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            $sheet2->getStyle('B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet2->getStyle('C' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet2->getStyle('D' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet2->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet2->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet2->getColumnDimension($col)->setAutoSize(true);
        }

        // HOJA 3: DETALLE POR PRODUCTO
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Por Producto');

        $headers3 = ['Nro', 'Código', 'Producto', 'Categoría', 'Precio Compra Prom.', 'Precio Venta', 'Ganancia Unit.', 'Margen %', 'Total Vendidos', 'Ganancia Total'];
        
        $col = 'A';
        foreach ($headers3 as $header) {
            $sheet3->setCellValue($col . '1', $header);
            $sheet3->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        $row = 2;
        $nro = 1;
        foreach ($productos as $producto) {
            $sheet3->setCellValue('A' . $row, $nro);
            $sheet3->setCellValue('B' . $row, $producto->codigo_barras);
            $sheet3->setCellValue('C' . $row, $producto->nombre);
            $sheet3->setCellValue('D' . $row, $producto->categoria->nombre ?? '-');
            $sheet3->setCellValue('E' . $row, $producto->precio_compra_promedio);
            $sheet3->setCellValue('F' . $row, $producto->precio_venta);
            $sheet3->setCellValue('G' . $row, $producto->ganancia_unitaria);
            $sheet3->setCellValue('H' . $row, number_format($producto->margen_porcentaje, 2) . '%');
            $sheet3->setCellValue('I' . $row, $producto->total_vendidos);
            $sheet3->setCellValue('J' . $row, $producto->ganancia_total);

            foreach (range('A', 'J') as $col) {
                $sheet3->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            $sheet3->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet3->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet3->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet3->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet3->getStyle('J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
            $nro++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet3->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = 'Reporte_Ganancias_' . $timestamp . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function inventarioExcel(Request $request)
    {
        $query = \App\Models\Producto::with(['categoria', 'proveedores']);

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('stock_filter')) {
            if ($request->stock_filter === 'bajo') {
                $query->whereRaw('cantidad_stock <= stock_minimo');
            } elseif ($request->stock_filter === 'maximo') {
                $query->whereRaw('cantidad_stock >= stock_maximo');
            }
        }

        $productos = $query->orderBy('nombre')->get();

        foreach ($productos as $producto) {
            $producto->total_vendidos = \App\Models\FacturaDetalle::where('producto_id', $producto->id)
                ->sum('cantidad');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventario');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F4E78'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];

        $headers = ['Nro', 'Código', 'Producto', 'Categoría', 'Stock Actual', 'Stock Mín.', 'Stock Máx.', 'Precio Unit.', 'Total Vendidos', 'Estado'];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        $row = 2;
        $nro = 1;
        foreach ($productos as $producto) {
            $estado = 'Normal';
            if ($producto->cantidad_stock == 0) {
                $estado = 'Sin Stock';
            } elseif ($producto->cantidad_stock <= $producto->stock_minimo) {
                $estado = 'Stock Bajo';
            }

            $sheet->setCellValue('A' . $row, $nro);
            $sheet->setCellValue('B' . $row, $producto->codigo_barras);
            $sheet->setCellValue('C' . $row, $producto->nombre);
            $sheet->setCellValue('D' . $row, $producto->categoria->nombre ?? '-');
            $sheet->setCellValue('E' . $row, $producto->cantidad_stock);
            $sheet->setCellValue('F' . $row, $producto->stock_minimo);
            $sheet->setCellValue('G' . $row, $producto->stock_maximo);
            $sheet->setCellValue('H' . $row, $producto->precio_unitario);
            $sheet->setCellValue('I' . $row, $producto->total_vendidos);
            $sheet->setCellValue('J' . $row, $estado);

            foreach (range('A', 'J') as $col) {
                $sheet->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
            $nro++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = 'Reporte_Inventario_' . $timestamp . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function ventasQuery(Request $request)
    {
        $query = Factura::with(['cliente', 'tipoPago', 'usuario']);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_hora', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_hora', '<=', $request->fecha_hasta);
        }

        if ($request->filled('cliente')) {
            $term = trim($request->cliente);
            $query->whereHas('cliente', function ($q) use ($term) {
                $q->where('cedula', 'like', '%' . $term . '%')
                    ->orWhere('nombres', 'like', '%' . $term . '%')
                    ->orWhere('apellidos', 'like', '%' . $term . '%');
            });
        }

        if ($request->filled('tipo_pago_id')) {
            $query->where('tipo_pago_id', $request->tipo_pago_id);
        }

        if ($request->filled('numero_factura')) {
            $query->where('id', $request->numero_factura);
        }

        return $query;
    }

    private function comprasQuery(Request $request)
    {
        $query = \App\Models\Compra::with(['proveedor', 'tipoPago', 'usuario']);

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_compra', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_compra', '<=', $request->fecha_hasta);
        }

        if ($request->filled('proveedor_ruc')) {
            $query->where('proveedor_ruc', $request->proveedor_ruc);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        return $query;
    }
}
