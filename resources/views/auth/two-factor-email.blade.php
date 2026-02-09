<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600 text-center">
            {{ __('Hemos enviado un código de verificación a tu correo electrónico. Por favor ingrésalo a continuación para continuar.') }}
        </div>

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600 text-center">
                {{ session('status') }}
            </div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('two-factor.store') }}">
            @csrf

            <div class="mt-4">
                <x-label for="code" value="{{ __('Código de Verificación') }}" class="text-center" />
                <x-input id="code" class="block mt-2 w-full text-center text-3xl tracking-[0.5em] font-bold py-3" 
                            type="text" name="code" required autofocus autocomplete="one-time-code" maxlength="6" inputmode="numeric" placeholder="######" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('two-factor.resend') }}" class="text-sm text-indigo-600 hover:text-indigo-900 hover:underline">
                    {{ __('¿No recibiste el código?') }}
                </a>

                <div class="flex items-center gap-4">
                    <form method="GET" action="{{ route('logout.get') }}">
                        <x-button type="submit" class="bg-gray-200 text-gray-800 hover:bg-gray-300 active:bg-gray-400">
                            {{ __('Cancelar y volver al inicio') }}
                        </x-button>
                    </form>

                    <x-button class="bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-900">
                        {{ __('Verificar') }}
                    </x-button>
                </div>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
