<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 px-4 py-3 bg-blue-50 border-l-4 border-blue-500 text-blue-700 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-100 p-4" role="alert">
            <p class="font-bold">¡Atención!</p>
            <p class="text-sm">Una vez registrado, debes comunicarte con el <strong>Administrador del Sistema</strong> para que te asigne el rol correspondiente (Auditor, Administrador o Empleado) y puedas acceder correctamente.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
                <div>
                    <x-label for="nombres" value="{{ __('Nombres') }}" />
                    <x-input id="nombres" class="block mt-1 w-full" type="text" name="nombres" :value="old('nombres')" required autofocus />
                    <x-input-error for="nombres" class="mt-2" />
                </div>
                <div>
                    <x-label for="apellidos" value="{{ __('Apellidos') }}" />
                    <x-input id="apellidos" class="block mt-1 w-full" type="text" name="apellidos" :value="old('apellidos')" required />
                    <x-input-error for="apellidos" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-label for="cedula" value="{{ __('Cédula') }}" />
                    <x-input id="cedula" class="block mt-1 w-full" type="text" name="cedula" :value="old('cedula')" required />
                    <x-input-error for="cedula" class="mt-2" />
                </div>
                <div>
                    <x-label for="telefono" value="{{ __('Teléfono') }}" />
                    <x-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono')" required />
                    <x-input-error for="telefono" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-label for="fecha_nacimiento" value="{{ __('Fecha de nacimiento') }}" />
                    <x-input id="fecha_nacimiento" class="block mt-1 w-full" type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento')" required />
                    <x-input-error for="fecha_nacimiento" class="mt-2" />
                </div>
                <div>
                    <x-label for="genero" value="{{ __('Género') }}" />
                    <select id="genero" name="genero" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                        <option value="" disabled {{ old('genero') ? '' : 'selected' }}>{{ __('Seleccione') }}</option>
                        <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>{{ __('Masculino') }}</option>
                        <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>{{ __('Femenino') }}</option>
                        <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>{{ __('Otro') }}</option>
                    </select>
                    <x-input-error for="genero" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <x-label for="direccion" value="{{ __('Dirección') }}" />
                <x-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion')" required />
                <x-input-error for="direccion" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                <x-input-error for="email" class="mt-2" />
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <x-label for="password" value="{{ __('Contraseña') }}" />
                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error for="password" class="mt-2" />
                    <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos</p>
                </div>
                <div>
                    <x-label for="password_confirmation" value="{{ __('Confirmar contraseña') }}" />
                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    <x-input-error for="password_confirmation" class="mt-2" />
                </div>
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('Acepto los :terms_of_service y la :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Términos de Servicio</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Política de Privacidad</a>',
                                ]) !!}
                            </div>
                        </div>
                        <x-input-error for="terms" class="mt-2" />
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
                <x-button class="ms-4">
                    {{ __('Registrarse') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>