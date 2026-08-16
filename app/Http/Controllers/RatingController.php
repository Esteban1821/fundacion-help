<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Rating;
use App\Events\DashboardStatsUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    // 1. Mostrar la pantalla de calificación
    public function create($id)
    {
        $ticket = Ticket::findOrFail($id);

        $this->autorizarCalificacion($ticket);

        return view('ratings.create', compact('ticket'));
    }

    // 2. Guardar la calificación y CERRAR el caso
    public function store(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // Esta validación faltaba por completo en store(): solo estaba en
        // create(). Como create() únicamente pinta el formulario, quien
        // enviara el POST directamente (sin pasar por la pantalla) podía
        // calificar y cerrar el ticket de CUALQUIER otro usuario.
        // La regla tiene que evaluarse en la acción que modifica datos,
        // no solo en la que muestra la vista.
        $this->autorizarCalificacion($ticket);

        // Validamos que envíe entre 1 y 5 estrellas
        $request->validate([
            'stars'    => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string|max:1000'
        ]);

        // Creamos la calificación asociada al ticket
        $ticket->rating()->create([
            'stars'    => $request->stars,
            'comments' => $request->comments
        ]);

        // ¡LA MAGIA DEL FLUJO ITIL!
        // Cambiamos el estado del ticket a Cerrado
        $ticket->estado = 'Cerrado';
        $ticket->save();

        // Avisamos al dashboard en tiempo real: cambió el CSAT y el
        // conteo de tickets cerrados.
        broadcast(new DashboardStatsUpdated('calificacion_creada'));

        return redirect()->route('dashboard')->with('success', '¡Gracias por tu calificación! El caso ha sido cerrado exitosamente.');
    }

    /**
     * Reglas para poder calificar un ticket. Se usa tanto al mostrar el
     * formulario como al guardarlo, para que ambas rutas apliquen
     * exactamente el mismo criterio.
     */
    protected function autorizarCalificacion(Ticket $ticket): void
    {
        // Solo el dueño del ticket puede calificarlo
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Este ticket no te pertenece.');
        }

        // Solo se califica cuando el técnico ya lo atendió
        if ($ticket->estado !== 'Atendido') {
            abort(403, 'Este ticket todavía no está disponible para calificación.');
        }

        // Un ticket se califica una sola vez. Sin esto, reenviar el
        // formulario creaba varias calificaciones para el mismo ticket
        // y desviaba el promedio de satisfacción (CSAT).
        if ($ticket->rating()->exists()) {
            abort(403, 'Este ticket ya fue calificado.');
        }
    }
}