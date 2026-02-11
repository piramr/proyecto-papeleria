<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - {{ config('app.name', 'Papelería') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes float {
            0%, 100% {
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
        <!-- Formulario de Registro -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 lg:p-12 bg-white z-20">
            <div class="max-w-lg w-full space-y-6">
                <!-- Encabezado -->
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 mb-4 transform hover:scale-110 transition-transform duration-300">
                        <img src="img/logo.jpg" alt="logo" class="object-contain w-full h-full">
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800">Crea tu cuenta</h2>
                    <p class="mt-2 text-gray-600">Completa los datos para registrarte en el sistema</p>
                </div>

                <!-- Alerta de información -->
                <div class="px-4 py-3 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-r-lg" role="alert">
                    <p class="font-semibold text-sm">¡Atención!</p>
                    <p class="text-xs">Una vez registrado, contacta al <strong>Administrador del Sistema</strong> para que te asigne el rol correspondiente.</p>
                </div>

                <!-- Mostrar errores de validación -->
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg">
                        <ul class="text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Nombres y Apellidos -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="text" placeholder="Nombres" name="nombres" value="{{ old('nombres') }}"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm" required autofocus>
                        </div>
                        <div>
                            <input type="text" placeholder="Apellidos" name="apellidos" value="{{ old('apellidos') }}"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm" required>
                        </div>
                    </div>

                    <!-- Cédula y Teléfono -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="text" placeholder="Cédula" name="cedula" value="{{ old('cedula') }}"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm" required>
                        </div>
                        <div>
                            <input type="text" placeholder="Teléfono" name="telefono" value="{{ old('telefono') }}"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm" required>
                        </div>
                    </div>

                    <!-- Fecha de Nacimiento y Género -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="date" placeholder="Fecha de nacimiento" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm" required>
                        </div>
                        <div>
                            <select name="genero"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm" required>
                                <option value="" disabled {{ old('genero') ? '' : 'selected' }}>Género</option>
                                <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <div>
                        <input type="text" placeholder="Dirección" name="direccion" value="{{ old('direccion') }}"
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm" required>
                    </div>

                    <!-- Email -->
                    <div>
                        <input type="email" placeholder="Email" name="email" value="{{ old('email') }}"
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm" required>
                    </div>

                    <!-- Contraseñas -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <input id="passwordInput" type="password" placeholder="Contraseña" name="password"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm pr-12" required autocomplete="new-password">
                            <button type="button" onclick="togglePassword('passwordInput', 'eyeIcon1')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-600 transition-colors">
                                <i id="eyeIcon1" class="fa-solid fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                        <div class="relative">
                            <input id="passwordConfirmationInput" type="password" placeholder="Confirmar contraseña" name="password_confirmation"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-400 focus:bg-white transition-all outline-none text-sm pr-12" required>
                            <button type="button" onclick="togglePassword('passwordConfirmationInput', 'eyeIcon2')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-indigo-600 transition-colors">
                                <i id="eyeIcon2" class="fa-solid fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 -mt-2">Mínimo 8 caracteres, mayúsculas, minúsculas, números y símbolos</p>

                    <!-- Términos y condiciones -->
                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" name="terms" id="terms" required
                                class="w-4 h-4 mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="terms" class="text-xs text-gray-600">
                                Acepto los 
                                <a target="_blank" href="{{ route('terms.show') }}" class="text-blue-500 hover:underline">Términos de Servicio</a>
                                y la 
                                <a target="_blank" href="{{ route('policy.show') }}" class="text-blue-500 hover:underline">Política de Privacidad</a>
                            </label>
                        </div>
                    @endif

                    <!-- Botón de registro -->
                    <button type="submit"
                        class="w-full py-3 bg-indigo-500 hover:bg-indigo-600 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 transform transition-all hover:-translate-y-1 active:scale-95">
                        Registrarse
                    </button>
                </form>

                <!-- Link para login -->
                <p class="text-center text-sm text-gray-500">
                    ¿Ya tienes una cuenta?
                    <a href="{{ route('login') }}" class="font-bold text-blue-500 hover:underline">Iniciar sesión</a>
                </p>
            </div>
        </div>

        <!-- Sección decorativa lateral -->
        <div class="hidden lg:flex w-1/2 bg-slate-50 relative overflow-hidden items-center justify-center border-l border-gray-100">
            <!-- Elementos decorativos flotantes -->
            <div class="absolute top-10 right-20 w-40 h-40 bg-purple-200 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-float"></div>
            <div class="absolute bottom-20 left-10 w-56 h-56 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-50 animate-float" style="animation-delay: 2s"></div>

            <!-- Texto principal -->
            <div class="relative z-10 text-center px-16">
                <h1 class="text-5xl font-black text-slate-800 leading-tight">
                    PapeleríaXpress <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-600">
                        Únete a nuestro equipo
                    </span>
                </h1>
                <p class="mt-4 text-lg text-gray-600">
                    Crea tu cuenta y forma parte de nuestro sistema de gestión de papelería.
                </p>
            </div>

            <!-- Formas decorativas adicionales -->
            <div class="absolute top-[10%] left-[15%] w-24 h-24 bg-purple-400 rounded-full opacity-40 animate-pulse"></div>
            <div class="absolute bottom-[15%] right-[20%] w-32 h-32 bg-indigo-300 rounded-tr-[80px] rotate-12 opacity-40"></div>
            <div class="absolute bottom-[-5%] left-[30%] w-48 h-24 bg-pink-400 rounded-t-full opacity-30 transform -rotate-12"></div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

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