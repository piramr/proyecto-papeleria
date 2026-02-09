<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Proveedor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\Auditoria\AuditoriaService;


class ProveedorController extends Controller
{



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreProveedorRequest $request)
    {
        $data = $request->validated();

        $proveedor = Proveedor::create([
            'ruc' => $data['ruc'],
            'nombre' => $data['nombre'],
            'telefono_principal' => $data['telefono_principal'],
            'telefono_secundario' => $data['telefono_secundario'] ?? null,
            'email' => $data['email'],
        ]);

        foreach ($data['direcciones'] as $dir) {
            $dir['proveedor_ruc'] = $proveedor->ruc;
            $proveedor->direcciones()->create($dir);
        }

        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'crear',
            'entidad' => 'Proveedor',
            'recurso_id' => $proveedor->ruc,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        return redirect()
            ->route('admin.proveedores')
            ->with('success', 'Proveedor registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        $proveedor->load('direcciones');

        $html = view('admin.proveedores.partials.form-edit', [
            'proveedor' => $proveedor,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UpdateProveedorRequest $request, Proveedor $proveedor)
    {
        DB::transaction(function () use ($request, $proveedor) {

            // Actualizar proveedor
            $proveedor->update($request->validated());

            // Direcciones que vienen del form
            $direccionesRequest = collect($request->input('direcciones', []));
            $idsRequest = $direccionesRequest->pluck('id')->filter();
            $idsBD = $proveedor->direcciones()->pluck('id');

            // Detectar direcciones eliminadas
            $idsEliminar = $idsBD->diff($idsRequest);
            if ($idsEliminar->isNotEmpty()) {
                $direccionesAEliminar = $proveedor->direcciones()->whereIn('id', $idsEliminar)->get();
                foreach ($direccionesAEliminar as $direccion) {
                    $direccion->delete(); 
                }
            }

            // Actualizar o crear direcciones
            foreach ($direccionesRequest as $dir) {
                if (!empty($dir['id'])) {
                    $direccion = $proveedor->direcciones()->find($dir['id']);
                    if ($direccion) {
                        $direccion->update($dir);
                    }
                } else {
                    $proveedor->direcciones()->create($dir);
                }
            }
        });

        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'actualizar',
            'entidad' => 'Proveedor',
            'recurso_id' => $proveedor->ruc,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        return redirect()
            ->route('admin.proveedores')
            ->with('success', 'Proveedor actualizado correctamente');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        $proveedor->direcciones()->delete();
        $proveedor->delete();
        // Log de operación y sistema
        AuditoriaService::registrarOperacion([
            'user_id' => Auth::id(),
            'tipo_operacion' => 'eliminar',
            'entidad' => 'Proveedor',
            'recurso_id' => $proveedor->ruc,
            'resultado' => 'exitoso',
            'mensaje_error' => null,
        ]);
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['message' => 'Proveedor eliminado correctamente']);
        }
        return redirect()->route('admin.proveedores')->with('success', 'Proveedor eliminado correctamente');
    }

    public function datatables()
    {
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

    public function exportPdf()
    {
        // Obtener todos los proveedores con sus relaciones
        $proveedores = Proveedor::with(['direcciones', 'productos'])->get();

        // Calcular datos adicionales para cada proveedor
        foreach ($proveedores as $proveedor) {
            // Contar compras realizadas
            $proveedor->nro_compras = \App\Models\Pedido::where('proveedor_ruc', $proveedor->ruc)->count();

            // Calcular total de gastos en compras
            $proveedor->total_gastos = \App\Models\Pedido::where('proveedor_ruc', $proveedor->ruc)->sum('total');
        }

        $pdf = Pdf::loadView('pdf.proveedores', [
            'proveedores' => $proveedores
        ]);
        return $pdf->stream();
    }

    public function exportExcel()
    {
        // Obtener todos los proveedores con sus relaciones
        $proveedores = Proveedor::with(['direcciones', 'productos'])->get();

        // Calcular datos adicionales para cada proveedor
        foreach ($proveedores as $proveedor) {
            $proveedor->nro_compras = \App\Models\Pedido::where('proveedor_ruc', $proveedor->ruc)->count();
            $proveedor->total_gastos = \App\Models\Pedido::where('proveedor_ruc', $proveedor->ruc)->sum('total');
        }

        // Crear una nueva hoja de cálculo
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Proveedores');

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
            'RUC',
            'Razón Social',
            'Email',
            'Teléfono Principal',
            'Teléfono Secundario',
            'Direcciones',
            'Productos Ofrecidos',
            'Nro. de Compras',
            'Gastos en Compras',
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
        foreach ($proveedores as $proveedor) {
            $sheet->setCellValue('A' . $row, $nro);
            $sheet->setCellValue('B' . $row, $proveedor->ruc);
            $sheet->setCellValue('C' . $row, $proveedor->nombre);
            $sheet->setCellValue('D' . $row, $proveedor->email);
            $sheet->setCellValue('E' . $row, $proveedor->telefono_principal);
            $sheet->setCellValue('F' . $row, $proveedor->telefono_secundario ?? '-');

            // Direcciones
            if ($proveedor->direcciones->isEmpty()) {
                $sheet->setCellValue('G' . $row, 'Sin direcciones');
            } else if ($proveedor->direcciones->count() > 1) {
                $direcciones = [];
                foreach ($proveedor->direcciones as $dir) {
                    $direcciones[] = '• ' . $dir->calle . ', ' . $dir->ciudad . ', ' . $dir->provincia;
                }
                $sheet->setCellValue('G' . $row, implode("\n", $direcciones));
            } else {
                $direcciones = [];
                foreach ($proveedor->direcciones as $dir) {
                    $direcciones[] = $dir->calle . ', ' . $dir->ciudad . ', ' . $dir->provincia;
                }
                $sheet->setCellValue('G' . $row, implode('; ', $direcciones));
            }

            // Productos ofrecidos
            if ($proveedor->productos->isEmpty()) {
                $sheet->setCellValue('H' . $row, 'Sin productos');
            } else if ($proveedor->productos->count() > 1) {
                $productosNombres = $proveedor->productos->pluck('nombre')->toArray();
                $productosConVinetas = array_map(function ($nombre) {
                    return '• ' . $nombre;
                }, $productosNombres);
                $sheet->setCellValue('H' . $row, implode("\n", $productosConVinetas));
            } else {
                $productosNombres = $proveedor->productos->pluck('nombre')->toArray();
                $sheet->setCellValue('H' . $row, implode(', ', $productosNombres));
            }

            // Número de compras
            $sheet->setCellValue('I' . $row, $proveedor->nro_compras);

            // Gastos en compras
            $sheet->setCellValue('J' . $row, $proveedor->total_gastos ?? 0);

            // Fecha de registro
            $sheet->setCellValue('K' . $row, $proveedor->created_at->format('d-m-Y H:i'));

            // Aplicar estilos a la fila de datos
            foreach (range('A', 'K') as $col) {
                $sheet->getStyle($col . $row)->applyFromArray($dataStyle);
            }

            // Alinear números a la derecha
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('I' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('J' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $row++;
            $nro++;
        }

        // Ajustar ancho de columnas automáticamente
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Crear el archivo Excel
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Generar nombre del archivo
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = 'Proveedores_' . $timestamp . '.xlsx';

        // Enviar el archivo como descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
