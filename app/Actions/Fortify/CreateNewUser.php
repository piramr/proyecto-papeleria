<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Validation\Rule;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'cedula' => [
                'required', 
                'digits:10',
                'unique:users,cedula'
            ],
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'digits:10'],
            'genero' => ['required', 'string', Rule::in(['Masculino', 'Femenino', 'Otro'])],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'direccion' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            // Mensajes para cédula
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.digits' => 'La cédula debe tener exactamente 10 dígitos.',
            'cedula.unique' => 'Esta cédula ya está registrada en el sistema.',
            
            // Mensajes para nombres
            'nombres.required' => 'Los nombres son obligatorios.',
            'nombres.string' => 'Los nombres deben ser texto.',
            'nombres.max' => 'Los nombres no pueden exceder 255 caracteres.',
            
            // Mensajes para apellidos
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'apellidos.string' => 'Los apellidos deben ser texto.',
            'apellidos.max' => 'Los apellidos no pueden exceder 255 caracteres.',
            
            // Mensajes para teléfono
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.digits' => 'El teléfono debe tener exactamente 10 dígitos.',
            
            // Mensajes para género
            'genero.required' => 'El género es obligatorio.',
            'genero.in' => 'El género seleccionado no es válido.',
            
            // Mensajes para fecha de nacimiento
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no es válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            
            // Mensajes para dirección
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.string' => 'La dirección debe ser texto.',
            'direccion.max' => 'La dirección no puede exceder 255 caracteres.',
            
            // Mensajes para email
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser texto.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.max' => 'El correo electrónico no puede exceder 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
            
            // Mensajes para contraseña
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.mixed_case' => 'La contraseña debe contener al menos una letra mayúscula y una minúscula.',
            'password.letters' => 'La contraseña debe contener letras.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un símbolo especial.',
            
            // Mensajes para términos
            'terms.accepted' => 'Debes aceptar los términos y condiciones.',
            'terms.required' => 'Debes aceptar los términos y condiciones.',
        ])->validate();

        return User::create([
            'cedula' => $input['cedula'],
            'nombres' => $input['nombres'],
            'apellidos' => $input['apellidos'],
            'telefono' => $input['telefono'],
            'email' => $input['email'],
            'genero' => $input['genero'],
            'fecha_nacimiento' => $input['fecha_nacimiento'],
            'direccion' => $input['direccion'],
            'password' => Hash::make($input['password']),
            'role' => 'Pendiente',
        ]);
    }
}
