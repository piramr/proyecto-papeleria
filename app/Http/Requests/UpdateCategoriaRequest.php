<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest {
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
        $categoriaId = $this->route('categoria')->id;

        return [
            'nombre' => [
                'required',
                'string',
                'min:3',
                'max:100',
                Rule::unique('categorias', 'nombre')->ignore($categoriaId, 'id')
            ],
            'descripcion' => 'nullable|string|max:255'
        ];
    }

    public function messages(): array {
        return [
            'nombre.min' => 'Ingrese un nombre más descriptivo',
            'nombre.required' => 'El nombre de la categoría es requerida',
            'nombre.unique' => 'Ya existe una categoria con ese nombre',
        ];
    }
}
