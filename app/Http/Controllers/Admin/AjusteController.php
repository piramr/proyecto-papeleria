<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ajuste;
use Illuminate\Http\Request;

class AjusteController extends Controller
{
    public function index()
    {
        $ajuste = Ajuste::getOrCreate();
        $tiposPago = \App\Models\TipoPago::all();

        return view('admin.ajustes.index', compact('ajuste', 'tiposPago'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'iva_porcentaje' => 'required|numeric|min:0|max:100',
            'empresa_nombre' => 'nullable|string|max:150',
            'empresa_ruc' => 'nullable|string|max:30',
            'empresa_direccion' => 'nullable|string|max:255',
            'empresa_telefono' => 'nullable|string|max:30',
            'empresa_email' => 'nullable|email|max:100',
            'logo_url' => 'nullable|string|max:255',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pie_factura' => 'nullable|string|max:500',
            'moneda_simbolo' => 'nullable|string|max:10',
            'moneda_decimales' => 'nullable|integer|min:0|max:4',
            'prefijo_factura' => 'nullable|string|max:20',
            'siguiente_factura' => 'nullable|integer|min:1',
            'secuencial_digitos' => 'nullable|integer|min:1|max:12',
            'tipo_pago_default_id' => 'nullable|exists:tipos_pago,id',
            'stock_minimo' => 'nullable|integer|min:0|max:9999',
            'stock_alerta_habilitada' => 'nullable|boolean',
            'notif_stock_bajo' => 'nullable|boolean',
            'notif_venta_realizada' => 'nullable|boolean',
            'notif_compra_recibida' => 'nullable|boolean',
        ], [
            'logo_file.image' => 'El archivo del logo debe ser una imagen.',
            'logo_file.mimes' => 'El logo debe ser un archivo de tipo: jpeg, png, jpg, gif.',
            'logo_file.max' => 'El logo no debe superar los 2MB.',
            'iva_porcentaje.required' => 'El IVA es obligatorio.',
            'empresa_email.email' => 'El correo electrónico no es válido.',
        ]);

        $ajuste = Ajuste::getOrCreate();

        // Asignar valores por defecto si vienen vacíos
        $data['moneda_decimales'] = $data['moneda_decimales'] ?? $ajuste->moneda_decimales ?? 2;
        $data['secuencial_digitos'] = $data['secuencial_digitos'] ?? $ajuste->secuencial_digitos ?? 9;
        $data['stock_minimo'] = $data['stock_minimo'] ?? $ajuste->stock_minimo ?? 5;

        $data['stock_alerta_habilitada'] = $request->boolean('stock_alerta_habilitada');
        $data['notif_stock_bajo'] = $request->boolean('notif_stock_bajo');
        $data['notif_venta_realizada'] = $request->boolean('notif_venta_realizada');
        $data['notif_compra_recibida'] = $request->boolean('notif_compra_recibida');

        // Procesar subida de logo
        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $filename);
            $data['logo_url'] = '/images/' . $filename;
        }

        $ajuste->update($data);

        return redirect()->route('admin.ajustes')
            ->with('success', 'Ajustes actualizados correctamente.');
    }
}
