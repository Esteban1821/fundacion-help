<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Service;
use App\Events\DashboardStatsUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class TicketController extends Controller
{
    // ==========================================
    // SECCIÓN 1: PANEL DE SOPORTE (TÉCNICOS)
    // ==========================================

    // Mostrar Bandeja de Entrada de Soporte
    public function index()
    {
        // El control de rol ya lo aplica el middleware 'role:soporte,admin'
        // sobre el grupo de rutas de soporte (ver routes/web.php).
        // Se pagina para que la bandeja no cargue la tabla completa en memoria
        // cuando el volumen de casos crezca.
        $tickets = Ticket::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('soporte.index', compact('tickets'));
    }

    // Mostrar detalles de un ticket para atenderlo
    public function show($id)
    {
        $ticket = Ticket::with('user')->findOrFail($id);
        return view('soporte.show', compact('ticket'));
    }

    // Actualizar el estado del ticket
    public function update(Request $request, $id)
    {
        // Validamos que el estado enviado sea uno de los permitidos.
        // Antes se guardaba directamente lo que llegara del formulario,
        // sin comprobar nada contra el enum de la base de datos.
        $request->validate([
            'estado' => 'required|in:Abierto,Atendido,Cerrado,Otros',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->estado = $request->input('estado');

        // Registramos quién atendió el ticket (para el reporte de tiempo
        // promedio de resolución por técnico). Guardamos siempre el
        // último que lo tocó, así el reporte refleja quién lo cerró.
        $ticket->atendido_por_id = Auth::id();

        $ticket->save();

        // Avisamos al dashboard en tiempo real que hay un cambio de estado
        broadcast(new DashboardStatsUpdated('ticket_actualizado'));

        return redirect()->route('soporte.index')
            ->with('success', 'El estado del ticket ' . $ticket->nro_ticket . ' ha sido actualizado a: ' . $ticket->estado);
    }

    // ==========================================
    // SECCIÓN 2: PANEL DE USUARIOS (CREAR TICKET)
    // ==========================================

    // Mostrar formulario para crear ticket
    public function create()
    {
        // Catálogo real desde la BD (antes era un array hardcodeado aquí).
        // Se transforma a { "Nombre Servicio": ["sub1", "sub2", ...] }
        // para no tener que tocar el JS/Alpine del formulario.
        $servicios = Service::with('subservicios')
            ->orderBy('orden')
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($servicio) => [
                $servicio->name => $servicio->subservicios->pluck('name')->all(),
            ]);

        return view('tickets.create', compact('servicios'));
    }

    // Guardar el nuevo ticket y procesar el archivo adjunto
    // Guardar el nuevo requerimiento
    public function store(Request $request)
    {
        // 1. Validar los datos recibidos
        $request->validate([
            'area_solicitante'          => 'required|string|max:255',
            'cargo_solicitante'         => 'required|string|max:255',
            'servicio_requerimiento'    => 'required|string|max:255',
            'subservicio_requerimiento' => 'required|string|max:255',
            'descripcion_requerimiento' => 'required|string',
            'prioridad'                 => 'required|in:Baja,Media,Alta',
            'soporte_adjunto'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // Máx 5MB
        ]);

        // 1.1 Resolver el servicio/subservicio elegido contra el catálogo
        // real (antes esto era texto libre sin ninguna validación contra
        // una fuente de verdad). Si el nombre no coincide con nada del
        // catálogo, el ticket igual se guarda (no bloqueamos al usuario)
        // pero sin servicio_id/subservicio_id, y el dashboard lo agrupa
        // como "Otros" en vez de fallar silenciosamente.
        $servicio = Service::where('name', $request->servicio_requerimiento)->first();
        $subservicio = $servicio
            ? $servicio->subservicios()->where('name', $request->subservicio_requerimiento)->first()
            : null;

        // 2. Subir el archivo adjunto (si el usuario subió uno)
        // Se guarda en el disco 'local' (privado), NO en 'public'. Antes
        // quedaba accesible por URL directa sin iniciar sesión, y los
        // adjuntos de un ticket suelen ser capturas con información
        // interna. Ahora solo se puede descargar por la ruta
        // tickets.adjunto, que verifica permisos.
        $rutaArchivo = null;
        if ($request->hasFile('soporte_adjunto')) {
            $rutaArchivo = $request->file('soporte_adjunto')->store('soportes', 'local');
        }

        // 3. Crear el ticket con su consecutivo.
        // El consecutivo se calcula contando los tickets del día, así que
        // dos usuarios que envíen el formulario al mismo tiempo pueden
        // obtener el mismo número. Como nro_ticket es UNIQUE en la base
        // de datos, el segundo INSERT falla; en ese caso reintentamos con
        // el número siguiente en lugar de mostrarle un error 500 al
        // usuario.
        $ticket = $this->crearTicketConConsecutivo([
            'user_id'                   => Auth::id(),
            'email_solicitante'         => Auth::user()->email,
            'area_solicitante'          => $request->area_solicitante,
            'cargo_solicitante'         => $request->cargo_solicitante,
            'servicio_requerimiento'    => $request->servicio_requerimiento,
            'servicio_id'               => $servicio?->id,
            'subservicio_requerimiento' => $request->subservicio_requerimiento,
            'subservicio_id'            => $subservicio?->id,
            'descripcion_requerimiento' => $request->descripcion_requerimiento,
            'prioridad'                 => $request->prioridad,
            'soporte_adjunto'           => $rutaArchivo,
            'estado'                    => 'Abierto',
        ]);

        // Avisamos al dashboard en tiempo real que hay un ticket nuevo
        broadcast(new DashboardStatsUpdated('ticket_creado'));

        // 4. Redirigir al panel con un mensaje de éxito
        return redirect()->route('dashboard')->with('success', '¡Su requerimiento (' . $ticket->nro_ticket . ') ha sido creado exitosamente!');
    }

    /**
     * Inserta el ticket generando el consecutivo del día (TK-AAAAMMDD-###).
     * Si otro usuario alcanzó a tomar ese mismo número, reintenta.
     */
    protected function crearTicketConConsecutivo(array $datos, int $intentosMaximos = 5): Ticket
    {
        for ($intento = 1; $intento <= $intentosMaximos; $intento++) {
            $fecha = now()->format('Ymd');
            $ticketsDeHoy = Ticket::whereDate('created_at', now()->toDateString())->count();

            // En cada reintento avanzamos una posición más
            $siguienteNumero = str_pad($ticketsDeHoy + $intento, 3, '0', STR_PAD_LEFT);
            $datos['nro_ticket'] = 'TK-' . $fecha . '-' . $siguienteNumero;

            try {
                return Ticket::create($datos);
            } catch (QueryException $e) {
                // 23000 = violación de restricción de integridad (aquí, el
                // UNIQUE de nro_ticket). Cualquier otro error sí se propaga.
                $esDuplicado = $e->getCode() === '23000';

                if (!$esDuplicado || $intento === $intentosMaximos) {
                    throw $e;
                }
            }
        }

        // Inalcanzable en la práctica, pero deja el tipo de retorno explícito
        throw new \RuntimeException('No fue posible generar un número de ticket disponible.');
    }

    /**
     * Entrega el archivo adjunto de un ticket verificando permisos.
     * Solo lo puede descargar quien creó el ticket o el personal de soporte.
     */
    public function descargarAdjunto($id)
    {
        $ticket = Ticket::findOrFail($id);

        $esGestor = in_array(Auth::user()->role, ['soporte', 'admin'], true);
        $esDueno  = $ticket->user_id === Auth::id();

        if (!$esGestor && !$esDueno) {
            abort(403, 'No tienes permiso para ver este archivo.');
        }

        if (!$ticket->soporte_adjunto) {
            abort(404, 'Este ticket no tiene un archivo adjunto.');
        }

        // Los tickets creados antes de este cambio guardaron el archivo en
        // el disco 'public'; los nuevos lo guardan en 'local' (privado).
        // Revisamos los dos para que los adjuntos antiguos sigan
        // descargandose y no se pierda el historial.
        foreach (['local', 'public'] as $disco) {
            if (Storage::disk($disco)->exists($ticket->soporte_adjunto)) {
                return Storage::disk($disco)->download($ticket->soporte_adjunto);
            }
        }

        abort(404, 'El archivo adjunto ya no se encuentra disponible.');
    }
}