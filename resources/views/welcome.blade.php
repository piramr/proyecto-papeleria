<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Papelería') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-xl">
            <div class="bg-white shadow-sm rounded-2xl p-8 border">
                <div class="flex flex-col items-center text-center gap-4">
                    {{-- Logo --}}
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo Papelería" class="h-28 w-auto">

                    <div>
                        <h1 class="text-3xl font-bold">Sistema de Inventario</h1>
                        <p class="text-gray-600 mt-2">
                            Gestión sencilla de productos y stock para la papelería.
                        </p>
                    </div>

                    <div class="w-full flex flex-col sm:flex-row gap-3 mt-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800">
                                Ir al Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-3 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800">
                                Iniciar sesión
                            </a>

                            <a href="{{ route('register') }}"
                               class="w-full inline-flex justify-center items-center px-4 py-3 rounded-xl bg-white text-gray-900 font-semibold border hover:bg-gray-50">
                                Registrarse
                            </a>
                        @endauth
                    </div>

                    <div class="text-xs text-gray-500 mt-2">
                        {{ date('Y') }} • {{ config('app.name', 'Proyecto Papelería') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
