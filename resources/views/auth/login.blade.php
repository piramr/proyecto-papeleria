{{-- <x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}"
                    class="text-base font-medium text-gray-700 dark:text-gray-300" />
                <x-input id="email"
                    class="block mt-2 w-full px-4 py-3 text-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                    placeholder="tu@email.com" />
            </div>

            <div>
                <x-label for="password" value="{{ __('') }}"
                    class="text-base font-medium text-gray-700 dark:text-gray-300" />
                <x-input id="password"
                    class="block mt-2 w-full px-4 py-3 text-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm"
                    type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <label for="remember_me" class="flex items-center cursor-pointer">
                    <x-checkbox id="remember_me" name="remember"
                        class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500" />
                    <span class="ms-2 text-base text-gray-600 dark:text-gray-400">{{ __('Recuérdame') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-base font-medium text-indigo-600 hover:text-indigo-500 hover:underline dark:text-indigo-400"
                        href="{{ route('password.request') }}">
                        {{ __('Olvidaste tu contraseña?') }}
                    </a>
                @endif
            </div>

            <div class="mt-8">
                <button type="submit"
                    class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-bold text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-all transform hover:-translate-y-0.5">
                    {{ __('Iniciar sesión') }}
                </button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout> --}}

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>

<body class="bg-white font-sans antialiased overflow-x-hidden">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-white z-20">
            <div class="max-w-md w-full space-y-8">

                <div class="text-center">
                    <div
                        class="inline-flex items-center justify-center w-auto h-48 mb-4 transform hover:scale-110 transition-transform duration-300">
                        <img src="img/logo.jpg" alt="logo" class="object-contain w-full h-full">
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800">Ingrese sus credenciales</h2>
                </div>
                <x-validation-errors class="mb-4" />

                @session('status')
                    <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                        {{ $value }}
                    </div>
                @endsession

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <input type="email" placeholder="Email" value="{{ old('email') }}" name="email"
                            class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none">
                    </div>

                    <div class="relative">
                        <input id="passwordInput" type="password" placeholder="Contraseña" name="password"
                            class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none">

                        <button type="button" onclick="togglePassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-600 transition-colors px-2">
                            <i id="eyeIcon" class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center space-x-2 cursor-pointer group">
                            <input type="checkbox" name="remember" id="remember_me"
                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-gray-500 group-hover:text-gray-700">Recuérdame</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="font-semibold text-blue-500 hover:text-blue-600 transition-colors"
                                href="{{ route('password.request') }}">
                                {{ __('¿Olvidaste tu contraseña?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 transform transition-all hover:-translate-y-1 active:scale-95">
                        Iniciar sesión
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500">
                    ¿No tienes una cuenta?
                    <a href="{{ route('register')}}" class="font-bold text-blue-500 hover:underline">Registrarse</a>
                </p>
            </div>
        </div>

        <div
            class="hidden lg:flex w-1/2 bg-slate-50 relative overflow-hidden items-center justify-center border-l border-gray-100">
            <div
                class="absolute top-10 right-20 w-40 h-40 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-float">
            </div>
            <div class="absolute bottom-20 left-10 w-56 h-56 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-float"
                style="animation-delay: 2s"></div>

            <div class="relative z-10 text-center px-16">
                <h1 class="text-5xl font-black text-slate-800 leading-tight">
                    PapeleríaXpress <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-600">Todo lo
                        que buscas para ti</span>
                </h1>
            </div>

            <div class="absolute top-[10%] left-[15%] w-24 h-24 bg-purple-400 rounded-full opacity-40 animate-pulse">
            </div>
            <div
                class="absolute bottom-[15%] right-[20%] w-32 h-32 bg-indigo-300 rounded-tr-[80px] rotate-12 opacity-40">
            </div>
            <div
                class="absolute bottom-[-5%] left-[30%] w-48 h-24 bg-pink-400 rounded-t-full opacity-30 transform -rotate-12">
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        }
    </script>

</body>

</html>
