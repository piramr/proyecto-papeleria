{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        <x-session-timeout />
        @livewireScripts
    </body>
</html> --}}
{{-- resources/views/layouts/app.blade.php --}}
{{-- @extends('adminlte::page')

@section('title', 'Perfil')

@section('content_header')
    @if (isset($header))
        {{ $header }}
    @endif
@stop

@section('content')
    {{ $slot ?? '' }}
@stop

@section('css')
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@stop

@section('js')
    @livewireScripts
@stop --}}

@extends('adminlte::page')

@section('css')
    @stack('styles')
@stop

@section('js')
    @stack('scripts')
    <!-- Notificaciones Bell y alertas flotantes -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const apiUrl = '{{ route("admin.api.todas-alertas") }}';
            let alertasMostradas = false;

            // Crear estilos para alertas flotantes
            if (!document.getElementById('alertas-flotantes-style')) {
                const style = document.createElement('style');
                style.id = 'alertas-flotantes-style';
                style.textContent = `
                    .alertas-flotantes-container { position: fixed; top: 70px; right: 24px; z-index: 2000; max-width: 420px; }
                    .alerta-flotante { background: white; border-left: 4px solid #ffc107; border-radius: 6px; box-shadow: 0 8px 24px rgba(0,0,0,0.18); padding: 14px 16px; margin-bottom: 10px; animation: slideIn 0.3s ease-out; display: flex; align-items: flex-start; }
                    @keyframes slideIn { from { transform: translateX(400px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
                    .alerta-flotante.danger { border-left-color: #dc3545; }
                    .alerta-flotante.success { border-left-color: #28a745; }
                    .alerta-flotante.info { border-left-color: #17a2b8; }
                    .alerta-icon { margin-right: 12px; font-size: 1.2rem; }
                    .alerta-content { flex: 1; }
                    .alerta-titulo { font-weight: bold; margin-bottom: 4px; font-size: 0.95rem; }
                    .alerta-mensaje { font-size: 0.85rem; color: #666; }
                    .alerta-close { margin-left: 10px; cursor: pointer; color: #999; font-size: 1.2rem; line-height: 1; }
                    .alerta-close:hover { color: #333; }
                `;
                document.head.appendChild(style);
            }

            // Crear contenedor para alertas flotantes
            let alertasContainer = document.getElementById('alertasFlotantes');
            if (!alertasContainer) {
                alertasContainer = document.createElement('div');
                alertasContainer.id = 'alertasFlotantes';
                alertasContainer.className = 'alertas-flotantes-container';
                document.body.appendChild(alertasContainer);
            }

            function mostrarAlerta(titulo, mensaje, tipo = 'warning') {
                const alerta = document.createElement('div');
                alerta.className = `alerta-flotante ${tipo}`;

                let icono = 'fas fa-exclamation-triangle';
                if (tipo === 'danger') icono = 'fas fa-exclamation-circle';
                if (tipo === 'success') icono = 'fas fa-check-circle';
                if (tipo === 'info') icono = 'fas fa-info-circle';

                alerta.innerHTML = `
                    <div class="alerta-icon"><i class="${icono}"></i></div>
                    <div class="alerta-content">
                        <div class="alerta-titulo">${titulo}</div>
                        <div class="alerta-mensaje">${mensaje}</div>
                    </div>
                    <div class="alerta-close" onclick="this.parentElement.style.opacity='0'; setTimeout(() => this.parentElement.remove(), 300);">×</div>
                `;

                alertasContainer.appendChild(alerta);

                setTimeout(() => {
                    if (alerta.parentElement) {
                        alerta.style.transition = 'opacity 0.3s ease-out';
                        alerta.style.opacity = '0';
                        setTimeout(() => alerta.remove(), 300);
                    }
                }, 8000);
            }

            // Cerrar alertas al hacer clic en cualquier parte de la pantalla
            document.addEventListener('click', function(e) {
                const alertas = document.querySelectorAll('.alerta-flotante');
                if (alertas.length > 0) {
                    alertas.forEach(a => {
                        a.style.transition = 'opacity 0.2s ease-out';
                        a.style.opacity = '0';
                        setTimeout(() => a.remove(), 200);
                    });
                }
            });

            // Crear campanita en el navbar
            const navbarRight = document.querySelector('.navbar-nav.ml-auto') || document.querySelector('.navbar-nav:last-of-type');
            if (navbarRight && !document.getElementById('notificacionesBell')) {
                const bellHtml = `
                    <li class="nav-item" style="position: relative;">
                        <div class="notificaciones-container" style="position: relative;">
                            <div class="notificaciones-bell" id="notificacionesBell" title="Notificaciones" style="cursor: pointer; font-size: 1.3rem; position: relative; color: #6c757d; margin: 0 15px;">
                                <i class="fas fa-bell"></i>
                                <span class="notificaciones-badge" id="notificacionesBadge" style="display: none; position: absolute; top: -5px; right: -5px; background-color: #dc3545; color: white; border-radius: 50%; width: 20px; height: 20px; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: bold;">0</span>
                            </div>
                            <div class="notificaciones-dropdown" id="notificacionesDropdown" style="position: absolute; top: 40px; right: 0; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; max-height: 400px; overflow-y: auto; z-index: 1000; display: none;">
                                <div class="notificaciones-header" style="padding: 12px; border-bottom: 1px solid #eee; background: #f8f9fa; font-weight: bold;">
                                    <i class="fas fa-bell"></i> Notificaciones
                                </div>
                                <ul class="notificaciones-list" id="notificacionesList" style="list-style: none; margin: 0; padding: 0;">
                                    <li class="notificaciones-empty" style="padding: 20px; text-align: center; color: #999;">Cargando...</li>
                                </ul>
                            </div>
                        </div>
                    </li>
                `;
                navbarRight.insertAdjacentHTML('beforeend', bellHtml);

                const bell = document.getElementById('notificacionesBell');
                const dropdown = document.getElementById('notificacionesDropdown');

                bell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
                });

                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.notificaciones-container')) {
                        dropdown.style.display = 'none';
                    }
                });
            }

            function cargarNotificaciones(mostrarFlotantes = false) {
                const badge = document.getElementById('notificacionesBadge');
                const list = document.getElementById('notificacionesList');

                fetch(apiUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (badge) {
                            if (data.total > 0) {
                                badge.textContent = data.total;
                                badge.style.display = 'flex';
                            } else {
                                badge.style.display = 'none';
                            }
                        }

                        if (list) {
                            if (data.notificaciones.length === 0) {
                                list.innerHTML = '<li class="notificaciones-empty" style="padding: 20px; text-align: center; color: #999;"><i class="fas fa-check-circle"></i> Sin notificaciones</li>';
                            } else {
                                list.innerHTML = data.notificaciones.map(n => `
                                    <li class="notificacion-item" style="padding: 12px; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.2s;" onclick="window.location.href='${n.url}'">
                                        <div style="display: inline-block; width: 30px; text-align: center; margin-right: 10px; color: ${n.color === 'warning' ? '#ffc107' : n.color === 'danger' ? '#dc3545' : '#17a2b8'};">
                                            <i class="${n.icono}"></i>
                                        </div>
                                        <div style="display: inline-block; vertical-align: top; width: calc(100% - 40px);">
                                            <div style="font-weight: bold; margin-bottom: 4px; font-size: 0.9rem;">${n.titulo}</div>
                                            <div style="font-size: 0.85rem; color: #666;">${n.mensaje}</div>
                                        </div>
                                    </li>
                                `).join('');
                            }
                        }

                        if (mostrarFlotantes && !alertasMostradas) {
                            data.notificaciones.forEach(notif => {
                                if (notif.tipo === 'stock_bajo') {
                                    mostrarAlerta(notif.titulo, notif.mensaje, 'warning');
                                }
                            });
                            alertasMostradas = true;
                        }
                    })
                    .catch(error => console.error('Error al cargar notificaciones:', error));
            }

            cargarNotificaciones(true);
            setInterval(() => cargarNotificaciones(false), 30000);
        });
    </script>
@stop
