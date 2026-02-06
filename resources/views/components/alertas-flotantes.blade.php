<!-- Alertas Flotantes en el Dashboard -->
<style>
    .alertas-flotantes-container {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 999;
        max-width: 400px;
    }

    .alerta-flotante {
        background: white;
        border-left: 4px solid #ffc107;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        padding: 15px;
        margin-bottom: 10px;
        animation: slideIn 0.3s ease-out;
        display: flex;
        align-items: flex-start;
    }

    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .alerta-flotante.danger {
        border-left-color: #dc3545;
    }

    .alerta-flotante.success {
        border-left-color: #28a745;
    }

    .alerta-flotante.info {
        border-left-color: #17a2b8;
    }

    .alerta-icon {
        margin-right: 12px;
        font-size: 1.2rem;
    }

    .alerta-content {
        flex: 1;
    }

    .alerta-titulo {
        font-weight: bold;
        margin-bottom: 4px;
        font-size: 0.95rem;
    }

    .alerta-mensaje {
        font-size: 0.85rem;
        color: #666;
    }

    .alerta-close {
        margin-left: 10px;
        cursor: pointer;
        color: #999;
        font-size: 1.2rem;
        line-height: 1;
    }

    .alerta-close:hover {
        color: #333;
    }
</style>

<div class="alertas-flotantes-container" id="alertasFlorantes"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('alertasFlorantes');

    function mostrarAlerta(titulo, mensaje, tipo = 'warning') {
        const alerta = document.createElement('div');
        alerta.className = `alerta-flotante ${tipo}`;
        
        let icono = 'fas fa-exclamation-triangle';
        if (tipo === 'danger') icono = 'fas fa-exclamation-circle';
        if (tipo === 'success') icono = 'fas fa-check-circle';
        if (tipo === 'info') icono = 'fas fa-info-circle';

        alerta.innerHTML = `
            <div class="alerta-icon">
                <i class="${icono}"></i>
            </div>
            <div class="alerta-content">
                <div class="alerta-titulo">${titulo}</div>
                <div class="alerta-mensaje">${mensaje}</div>
            </div>
            <div class="alerta-close" onclick="this.parentElement.style.opacity='0'; setTimeout(() => this.parentElement.remove(), 300);">
                ×
            </div>
        `;

        container.appendChild(alerta);

        // Auto-remover después de 8 segundos
        setTimeout(() => {
            if (alerta.parentElement) {
                alerta.style.transition = 'opacity 0.3s ease-out';
                alerta.style.opacity = '0';
                setTimeout(() => alerta.remove(), 300);
            }
        }, 8000);
    }

    // Cargar alertas iniciales
    function cargarAlertas() {
        fetch('{{ route("admin.api.todas-alertas") }}')
            .then(response => response.json())
            .then(data => {
                data.notificaciones.forEach(notif => {
                    // Mostrar solo las de stock bajo como flotantes
                    if (notif.tipo === 'stock_bajo') {
                        mostrarAlerta(notif.titulo, notif.mensaje, 'warning');
                    }
                });
            })
            .catch(error => console.error('Error al cargar alertas:', error));
    }

    // Cargar alertas al inicio
    cargarAlertas();

    // Exponer función globalmente para que pueda ser llamada desde otras partes
    window.mostrarAlertaFlotante = mostrarAlerta;
});
</script>
