<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductoRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        $productoId = $this->route('producto');

        return [
            'codigo_barras' => [
                'required',
                'string',
                'max:100',
                Rule::unique('productos', 'codigo_barras')->ignore($productoId),
            ],
            'nombre' => 'required|string|min:3|max:255',
            'caracteristicas' => 'nullable|string|min:5|max:500',
            'cantidad_stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'stock_maximo' => 'required|integer|min:0',
            'tiene_iva' => 'required|boolean',
            'ubicacion' => 'nullable|string|min:3|max:100',
            'precio_unitario' => 'required|numeric|min:0.01',
            'marca' => 'required|string|max:100',
            'en_oferta' => 'nullable|boolean',
            'precio_oferta' => 'required_if:en_oferta,1|numeric|min:0.01|lt:precio_unitario',
            'categoria_id' => 'required|integer|exists:categorias,id',
            'proveedor_ruc' => 'required|array',
            'proveedor_ruc.*' => 'string|max:13|exists:proveedores,ruc',
            'precioCosto' => 'required|array',
            'precioCosto.*' => 'required|numeric|min:0.01'
        ];
    }

    public function messages(): array {
        return [
            'codigo_barras.required' => 'El código de barras es obligatorio',
            'codigo_barras.unique' => 'Ya existe un producto con ese código de barras',
            'codigo_barras.max' => 'El código de barras no puede exceder 100 caracteres',

            'nombre.required' => 'El nombre del producto es obligatorio',
            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',

            'caracteristicas.min' => 'Las características deben tener al menos 5 caracteres',
            'caracteristicas.max' => 'Las características no pueden exceder 500 caracteres',

            'cantidad_stock.required' => 'La cantidad de stock es obligatoria',
            'cantidad_stock.integer' => 'La cantidad de stock debe ser un número entero',
            'cantidad_stock.min' => 'La cantidad de stock no puede ser negativa',

            'stock_minimo.required' => 'El stock mínimo es obligatorio',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo',
            
            'stock_maximo.required' => 'El stock máximo es obligatorio',
            'stock_maximo.integer' => 'El stock máximo debe ser un número entero',
            'stock_maximo.min' => 'El stock máximo no puede ser negativo',

            'ubicacion.min' => 'La ubicación debe tener al menos 3 caracteres',
            'ubicacion.max' => 'La ubicación no puede exceder 100 caracteres',

            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número válido',
            'precio_unitario.min' => 'El precio unitario debe ser mayor a 0',

            'precio_oferta.numeric' => 'El precio de oferta debe ser un número válido',
            'precio_oferta.min' => 'El precio de oferta debe ser mayor a 0',
            'precio_oferta.lt' => 'El precio de oferta debe ser menor al precio unitario',
            'precio_oferta.required_if' => 'El precio de oferta es obligatorio cuando el producto está en oferta',

            'marca.required' => 'La marca es obligatoria',
            'marca.max' => 'La marca no puede exceder 100 caracteres',

            'categoria_id.required' => 'La categoría es obligatoria',
            'categoria_id.exists' => 'La categoría seleccionada no existe',

            'proveedor_ruc.required' => 'Debe seleccionar al menos un proveedor',
            'proveedor_ruc.*.exists' => 'Uno de los proveedores seleccionados no existe',

            'precioCosto.required' => 'Debe ingresar los precios de costo',
            'precioCosto.*.required' => 'El precio de costo es obligatorio para cada proveedor',
            'precioCosto.*.numeric' => 'El precio de costo debe ser un número válido',
            'precioCosto.*.min' => 'El precio de costo debe ser mayor a 0'
        ];
    }
}
