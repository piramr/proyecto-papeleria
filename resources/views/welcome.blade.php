<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Papelería') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

    <style>
        .floating-icon {
            position: absolute;
            opacity: 0.15;
            animation: float 15s infinite ease-in-out;
            color: #4f46e5;
            z-index: 0;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -50px) rotate(10deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
        }
        .icon-1 { top: 10%; left: 10%; width: 120px; height: 120px; color: #f43f5e; animation-delay: 0s; } /* Pencil - Rose */
        .icon-2 { top: 20%; right: 15%; width: 100px; height: 100px; color: #10b981; animation-delay: 2s; } /* Ruler - Emerald */
        .icon-3 { bottom: 15%; left: 20%; width: 140px; height: 140px; color: #f59e0b; animation-delay: 4s; } /* Notebook - Amber */
        .icon-4 { bottom: 25%; right: 10%; width: 80px; height: 80px; color: #3b82f6; animation-delay: 1s; } /* Clip - Blue */
        .icon-5 { top: 40%; left: 50%; width: 90px; height: 90px; color: #8b5cf6; animation-delay: 3s; } /* Scissors - Violet */

        /* Graph Paper Background */
        .bg-graph-paper {
            background-color: #f8fafc;
            background-image: 
                linear-gradient(rgba(148, 163, 184, 0.15) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.15) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Washi Tape Effect */
        .washi-tape-top {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%) rotate(-2deg);
            width: 80px;
            height: 25px;
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(2px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            z-index: 20;
            opacity: 0.8;
            mask-image: url("data:image/svg+xml,%3Csvg width='100%25' height='100%25' viewBox='0 0 100 100' preserveAspectRatio='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0 L100 0 L95 100 L5 100 Z' fill='black'/%3E%3C/svg%3E"); 
            /* Simple masking approximation, mostly relied on opacity/blur */
        }
        .washi-tape-yellow { background-color: rgba(253, 224, 71, 0.5); transform: translateX(-50%) rotate(-3deg); }
        .washi-tape-blue { background-color: rgba(147, 197, 253, 0.5); transform: translateX(-50%) rotate(2deg); }
        .washi-tape-pink { background-color: rgba(249, 168, 212, 0.5); transform: translateX(-50%) rotate(-1deg); }
    </style>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-50 selection:bg-indigo-500 selection:text-white">
    
    <!-- Navbar -->
    <nav class="absolute top-0 left-0 right-0 z-10 flex items-center justify-between px-6 py-6 lg:px-12">
        <div class="flex items-center gap-2">
            <!-- Logo Placeholder -->
            <div class="p-2 text-white bg-indigo-600 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-gray-900">Papelería</span>
        </div>

        @if (Route::has('login'))
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 text-sm font-semibold text-white transition-all bg-indigo-600 rounded-full hover:bg-indigo-500 shadow-lg shadow-indigo-200">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 transition-colors hover:text-indigo-600">
                        Iniciar Sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-semibold text-white transition-all bg-indigo-600 rounded-full hover:bg-indigo-500 shadow-lg shadow-indigo-200">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </nav>

    <!-- Hero Section -->
    <div class="relative flex flex-col items-center justify-center min-h-screen px-6 overflow-hidden lg:px-12 bg-graph-paper">
        <!-- Background Decorations (Animated) -->
        <!-- Background Decorations (Stationery Theme) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none select-none">
            <!-- Pencil -->
            <svg class="floating-icon icon-1" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            
            <!-- Ruler -->
            <svg class="floating-icon icon-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M21 6.75A2.25 2.25 0 0018.75 4.5H5.25A2.25 2.25 0 003 6.75v10.5a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 17.25V6.75zM6 15v-1.5m3 1.5v-1.5m3 1.5v-1.5m3 1.5v-1.5m3 1.5v-1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            
            <!-- Folder/Notebook -->
            <svg class="floating-icon icon-3" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            
            <!-- Paperclip -->
            <svg class="floating-icon icon-4" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            
            <!-- Scissors -->
            <svg class="floating-icon icon-5" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M7.875 14.25l1.214 1.942a2.25 2.25 0 001.908 1.058h2.006c.776 0 1.497-.28 2.05-.75l3.565-3.097a2.25 2.25 0 001.38-1.921 2.25 2.25 0 00-3.564-3.564l-3.097 3.565a2.25 2.25 0 01-.75 2.05l-1.942 1.214M2.25 15.75L6 12m-3.75 0L6 15.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>

        <div class="w-full max-w-6xl mx-auto text-center mt-20 mb-16 relative z-10">
            <span class="inline-block px-4 py-1.5 mb-6 text-sm font-medium text-indigo-700 bg-white/80 backdrop-blur-sm rounded-full shadow-sm hover:scale-105 transition-transform cursor-default">
                Gestión Integral de Papelería
            </span>
            <h1 class="mb-6 text-5xl font-extrabold tracking-tight text-gray-900 sm:text-7xl">
                Todo lo que necesitas <br class="hidden sm:block" /> 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-teal-500 typewriter">para tu oficina y estudio</span>
            </h1>
            <p class="max-w-2xl mx-auto mb-10 text-lg text-gray-600 sm:text-xl leading-relaxed">
                Bienvenido a nuestro portal. Selecciona tu perfil para ingresar al sistema y gestionar tus compras, ventas o inventarios.
            </p>
        </div>

        <!-- Cards Section -->
        <div class="grid w-full max-w-6xl grid-cols-1 gap-8 mx-auto lg:grid-cols-3 relative z-10">
            
            <!-- Admin Card - Indigo Theme -->
            <a href="{{ route('login') }}" class="group relative p-12 min-h-[500px] bg-indigo-200 backdrop-blur-md border border-indigo-300 rounded-[2rem] shadow-xl hover:shadow-2xl hover:shadow-indigo-300 transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-full justify-between" data-tilt data-tilt-glare data-tilt-max-glare="0.5" data-tilt-scale="1.05">
                <div class="washi-tape-top washi-tape-yellow"></div>
                <div class="absolute top-0 right-0 p-4 opacity-50">
                    <div class="w-32 h-32 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <!-- Content -->
                <div class="relative z-10 flex flex-col items-center flex-grow">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-8 text-indigo-700 bg-white/80 rounded-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    <h2 class="mb-4 text-3xl font-bold text-indigo-900 group-hover:text-indigo-800 transition-colors">Administrador</h2>
                    <p class="text-indigo-800/80 leading-relaxed mb-6 text-lg">
                        Control total del sistema, gestión de usuarios, reportes avanzados.
                    </p>
                </div>
                <div class="relative z-10 mt-auto">
                    <span class="inline-block w-full px-8 py-4 text-lg font-bold text-center text-white transition-all transform bg-indigo-600 shadow-md rounded-xl group-hover:bg-indigo-700 group-hover:shadow-lg group-hover:scale-105">
                        Ingresar <span class="ml-2">→</span>
                    </span>
                </div>
            </a>

            <!-- Employee Card - Green/Emerald Theme -->
            <a href="{{ route('login') }}" class="group relative p-12 min-h-[500px] bg-emerald-200 backdrop-blur-md border border-emerald-300 rounded-[2rem] shadow-xl hover:shadow-2xl hover:shadow-emerald-300 transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-full justify-between" data-tilt data-tilt-glare data-tilt-max-glare="0.5" data-tilt-scale="1.05">
                <div class="washi-tape-top washi-tape-blue"></div>
                <div class="absolute top-0 right-0 p-4 opacity-50">
                    <div class="w-32 h-32 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <div class="relative z-10 flex flex-col items-center flex-grow">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-8 text-emerald-700 bg-white/80 rounded-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                    <h2 class="mb-4 text-3xl font-bold text-emerald-900 group-hover:text-emerald-800 transition-colors">Empleado</h2>
                    <p class="text-emerald-800/80 leading-relaxed mb-6 text-lg">
                        Gestión de ventas en mostrador, inventario y atención al cliente.
                    </p>
                </div>
                <div class="relative z-10 mt-auto">
                    <span class="inline-block w-full px-8 py-4 text-lg font-bold text-center text-white transition-all transform bg-emerald-600 shadow-md rounded-xl group-hover:bg-emerald-700 group-hover:shadow-lg group-hover:scale-105">
                        Ingresar <span class="ml-2">→</span>
                    </span>
                </div>
            </a>

            <!-- Client Card - Blue Theme -->
            <a href="{{ route('login') }}" class="group relative p-12 min-h-[500px] bg-blue-200 backdrop-blur-md border border-blue-300 rounded-[2rem] shadow-xl hover:shadow-2xl hover:shadow-blue-300 transition-all duration-300 transform hover:-translate-y-1 flex flex-col items-center text-center h-full justify-between" data-tilt data-tilt-glare data-tilt-max-glare="0.5" data-tilt-scale="1.05">
                <div class="washi-tape-top washi-tape-pink"></div>
                <div class="absolute top-0 right-0 p-4 opacity-50">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                </div>
                <div class="relative z-10 flex flex-col items-center flex-grow">
                    <div class="inline-flex items-center justify-center w-16 h-16 mb-8 text-blue-700 bg-white/80 rounded-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 5.408c.636 2.726-1.429 5.337-4.223 5.337H7.354c-2.795 0-4.859-2.611-4.223-5.337l1.263-5.408a3 0 012.924-2.315h9.32a3 0 012.925 2.315z" />
                        </svg>
                    </div>
                    <h2 class="mb-4 text-3xl font-bold text-blue-900 group-hover:text-blue-800 transition-colors">Cliente</h2>
                    <p class="text-blue-800/80 leading-relaxed mb-6 text-lg">
                        Realiza tus pedidos, consulta nuestro catálogo y gestiona tu perfil.
                    </p>
                </div>
                <div class="relative z-10 mt-auto">
                    <span class="inline-block w-full px-8 py-4 text-lg font-bold text-center text-white transition-all transform bg-blue-600 shadow-md rounded-xl group-hover:bg-blue-700 group-hover:shadow-lg group-hover:scale-105">
                        Ingresar <span class="ml-2">→</span>
                    </span>
                </div>
            </a>

        </div>

        <!-- Footer -->
        <footer class="mt-20 text-center text-sm text-gray-400">
            <p>&copy; {{ date('Y') }} Papelería System. Todos los derechos reservados.</p>
        </footer>
    </div>
</body>
</html>
