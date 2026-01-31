<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            <div class="mb-4 font-medium text-lg text-red-600 dark:text-red-400">
                ¡Acceso Restringido!
            </div>
            <p class="mb-4">
                Tu cuenta ha sido creada exitosamente, pero <strong>aún no tienes un rol asignado</strong>.
            </p>
            <p class="mb-4">
                Por tal motivo, no puedes acceder al sistema. Por favor, comunícate inmediatamente con el <strong>Administrador del Sistema</strong> para que te habilite los permisos necesarios (Auditor, Administrador o Empleado).
            </p>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    {{ __('Cerrar Sesión') }}
                </button>
            </form>
        </div>
    </x-authentication-card>
</x-guest-layout>
