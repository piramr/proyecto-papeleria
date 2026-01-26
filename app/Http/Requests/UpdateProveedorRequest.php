<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProveedorRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        $ruc = $this->route('proveedor')->ruc;

        return [
            'ruc' => [
                'required',
                'numeric',
                'digits:13',
                // 'regex:/^(10|17|20)\d{11}$/',
                Rule::unique('proveedores', 'ruc')->ignore($ruc, 'ruc')
            ],

            'nombre' => 'required|string|min:3|max:255',
            'telefono_principal' => 'required|digits_between:7,10',
            'telefono_secundario' => 'nullable|digits_between:7,10',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('proveedores', 'email')->ignore($ruc, 'ruc')
            ],

            'direcciones' => 'required|array|min:1',
            'direcciones.*.provincia' => 'required|string|max:255',
            'direcciones.*.ciudad' => 'required|string|max:255',
            'direcciones.*.calle' => 'required|string|max:255',
            'direcciones.*.referencia' => 'required|string|max:255',
        ];
    }

    public function messages(): array {
        return [
            'ruc.required' => 'El RUC es obligatorio',
            'ruc.digits' => 'El RUC debe tener 13 dígitos',
            'ruc.regex' => 'El RUC no es válido en Ecuador',
            'nombre.required' => 'La razón social es obligatoria',
            'telefono_principal.required' => 'El teléfono principal es obligatorio',
            'telefono_principal.digits_between' => 'El teléfono principal no es válido',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'Ingrese un correo válido',
            'email.unique' => 'Este correo ya está registrado',

            'direcciones.required' => 'Debe registrar al menos una dirección',
            'direcciones.array' => 'Formato de direcciones inválido',
            'direcciones.*.provincia.required' => 'La provincia es obligatoria',
            'direcciones.*.ciudad.required' => 'La ciudad es obligatoria',
            'direcciones.*.calle.required' => 'La calle es obligatoria',
            'direcciones.*.referencia.required' => 'La referencia es obligatoria'
        ];
    }
}
