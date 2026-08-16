<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * Se dispara cada vez que algo que afecta al dashboard cambia:
 * ticket creado, estado de ticket actualizado, o nueva calificación.
 *
 * Usamos ShouldBroadcastNow (en vez de ShouldBroadcast) a propósito:
 * este proyecto no tiene un worker de colas corriendo por defecto, y
 * un dashboard "en tiempo real" que depende de que alguien tenga
 * `php artisan queue:work` activo no sirve de mucho. Al ser síncrono,
 * el evento se envía a Reverb en la misma request, sin colas de por
 * medio.
 *
 * El payload es intencionalmente mínimo: el frontend solo lo usa como
 * "señal" para volver a pedir /dashboard/stats, que es quien realmente
 * calcula y filtra los datos por rol. Así hay una sola fuente de verdad
 * para las estadísticas (evitamos tener la lógica de agregación
 * duplicada entre el evento y el controlador).
 */
class DashboardStatsUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public string $motivo = 'ticket_actualizado')
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stats.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'motivo' => $this->motivo,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
