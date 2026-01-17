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
        // dd($input);

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
        ]);
    }
}
