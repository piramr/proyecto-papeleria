<!-- Notificaciones Bell en el Navbar -->
<style>
    .notificaciones-container {
        position: relative;
    }

    .notificaciones-bell {
        cursor: pointer;
        font-size: 1.3rem;
        position: relative;
        color: #6c757d;
        transition: color 0.3s;
    }

    .notificaciones-bell:hover {
        color: #495057;
    }

    .notificaciones-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #dc3545;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: bold;
    }

    .notificaciones-dropdown {
        position: absolute;
        top: 40px;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 350px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    .notificaciones-dropdown.show {
        display: block;
    }

    .notificaciones-header {
        padding: 12px;
        border-bottom: 1px solid #eee;
        background: #f8f9fa;
        font-weight: bold;
    }

    .notificaciones-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .notificacion-item {
        padding: 12px;
        border-bottom: 1px solid #eee;
        cursor: pointer;
        transition: background 0.2s;
    }

    .notificacion-item:hover {
        background: #f8f9fa;
    }

    .notificacion-item:last-child {
        border-bottom: none;
    }

    .notificacion-icon {
        display: inline-block;
        width: 30px;
        text-align: center;
        margin-right: 10px;
    }

    .notificacion-content {
        display: inline-block;
        vertical-align: top;
        width: calc(100% - 40px);
    }

    .notificacion-titulo {
        font-weight: bold;
        margin-bottom: 4px;
        font-size: 0.9rem;
    }

    .notificacion-mensaje {
        font-size: 0.85rem;
        color: #666;
    }

    .notificaciones-empty {
        padding: 20px;
        text-align: center;
        color: #999;
    }

    .notificacion-warning .notificacion-icon i {
        color: #ffc107;
    }

    .notificacion-danger .notificacion-icon i {
        color: #dc3545;
    }

    .notificacion-info .notificacion-icon i {
        color: #17a2b8;
    }

    .notificacion-success .notificacion-icon i {
        color: #28a745;
    }
</style>

<div class="notificaciones-container">
    <div class="notificaciones-bell" id="notificacionesBell" title="Notificaciones">
        <i class="fas fa-bell"></i>
        <span class="notificaciones-badge" id="notificacionesBadge" style="display: none;">0</span>
    </div>

    <div class="notificaciones-dropdown" id="notificacionesDropdown">
        <div class="notificaciones-header">
            <i class="fas fa-bell"></i> Notificaciones
        </div>
        <ul class="notificaciones-list" id="notificacionesList">
            <li class="notificaciones-empty">Cargando...</li>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notificacionesBell');
    const dropdown = document.getElementById('notificacionesDropdown');
    const badge = document.getElementById('notificacionesBadge');
    const list = document.getElementById('notificacionesList');

    // Alternar dropdown
    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
    });

    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.notificaciones-container')) {
            dropdown.classList.remove('show');
        }
    });

    // Cargar notificaciones
    function cargarNotificaciones() {
        fetch('{{ route("admin.api.todas-alertas") }}')
            .then(response => response.json())
            .then(data => {
                // Actualizar badge
                if (data.total > 0) {
                    badge.textContent = data.total;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }

                // Actualizar lista
                if (data.notificaciones.length === 0) {
                    list.innerHTML = '<li class="notificaciones-empty"><i class="fas fa-check-circle"></i> Sin notificaciones</li>';
                } else {
                    list.innerHTML = data.notificaciones.map(n => `
                        <li class="notificacion-item notificacion-${n.color}" onclick="window.location.href='${n.url}'">
                            <div class="notificacion-icon">
                                <i class="${n.icono}"></i>
                            </div>
                            <div class="notificacion-content">
                                <div class="notificacion-titulo">${n.titulo}</div>
                                <div class="notificacion-mensaje">${n.mensaje}</div>
                            </div>
                        </li>
                    `).join('');
                }
            })
            .catch(error => console.error('Error al cargar notificaciones:', error));
    }

    // Cargar al inicio
    cargarNotificaciones();

    // Actualizar cada 30 segundos
    setInterval(cargarNotificaciones, 30000);
});
</script>
