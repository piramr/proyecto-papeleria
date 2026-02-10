<?php

namespace App\Notifications;

use App\Models\Producto;
use App\Models\Ajuste;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notificación de stock bajo
 * 
 * IMPORTANTE: Antes de enviar esta notificación, verificar que las notificaciones 
 * estén habilitadas usando: Ajuste::notificacionesStockBajoHabilitadas()
 * 
 * Ejemplo de uso:
 * if (Ajuste::notificacionesStockBajoHabilitadas()) {
 *     $user->notify(new LowStockNotification($producto, $stockMinimo));
 * }
 */
class LowStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $producto;
    public $stockMinimo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Producto $producto, int $stockMinimo)
    {
        $this->producto = $producto;
        $this->stockMinimo = $stockMinimo;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Alerta: Stock bajo - ' . $this->producto->nombre)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El producto "' . $this->producto->nombre . '" ha alcanzado un stock bajo.')
            ->line('**Stock actual:** ' . $this->producto->cantidad_stock)
            ->line('**Stock mínimo configurado:** ' . $this->stockMinimo)
            ->action('Ver inventario', route('admin.inventario.productos'))
            ->line('¡Por favor, considera hacer un pedido a los proveedores!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
