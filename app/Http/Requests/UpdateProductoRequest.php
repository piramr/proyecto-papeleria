<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras,' . $productoId,
            'nombre' => 'nullable|string|min:3|max:255',
            'caracteristicas' => 'nullable|string|min:5|max:500',
            'cantidad_stock' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'tiene_iva' => 'nullable|boolean',
            'ubicacion' => 'nullable|string|min:3|max:100',
            'precio_unitario' => 'nullable|numeric|min:0.01',
            'marca' => 'nullable|string|max:100',
            'en_oferta' => 'nullable|boolean',
            'precio_oferta' => 'required_if:en_oferta,1|numeric|min:0.01|lt:precio_unitario',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
            'proveedor_ruc' => 'nullable|array',
            'proveedor_ruc.*' => 'string|max:13|exists:proveedores,ruc'
        ];
    }

    public function messages(): array {
        return [
            'codigo_barras.unique' => 'Ya existe un producto con ese código de barras',
            'codigo_barras.max' => 'El código de barras no puede exceder 100 caracteres',

            'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
            'nombre.max' => 'El nombre no puede exceder 255 caracteres',

            'caracteristicas.min' => 'Las características deben tener al menos 5 caracteres',
            'caracteristicas.max' => 'Las características no pueden exceder 500 caracteres',

            'cantidad_stock.integer' => 'La cantidad de stock debe ser un número entero',
            'cantidad_stock.min' => 'La cantidad de stock no puede ser negativa',

            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo',

            'ubicacion.min' => 'La ubicación debe tener al menos 3 caracteres',
            'ubicacion.max' => 'La ubicación no puede exceder 100 caracteres',

            'precio_unitario.numeric' => 'El precio unitario debe ser un número válido',
            'precio_unitario.min' => 'El precio unitario debe ser mayor a 0',

            'precio_oferta.numeric' => 'El precio de oferta debe ser un número válido',
            'precio_oferta.min' => 'El precio de oferta debe ser mayor a 0',
            'precio_oferta.lt' => 'El precio de oferta debe ser menor al precio unitario',
            'precio_oferta.required_if' => 'El precio de oferta es obligatorio cuando el producto está en oferta',

            'marca.max' => 'La marca no puede exceder 100 caracteres',

            'categoria_id.exists' => 'La categoría seleccionada no existe',

            'proveedor_ruc.*.exists' => 'Uno de los proveedores seleccionados no existe'
        ];
    }
}
