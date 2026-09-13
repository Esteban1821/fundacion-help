<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Rating;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // Estados que consideramos "terminales" (el ticket ya no se sigue trabajando)
    protected array $estadosTerminales = ['Cerrado', 'Otros'];

    // Vista principal del dashboard (primer render con datos ya reales)
    public function index()
    {
        $usuarioLogueado = Auth::user();
        $esGestor = in_array($usuarioLogueado->role, ['admin', 'soporte']);

        $equipos = Inventory::where('user_id', $usuarioLogueado->id)->get();

        if ($esGestor) {
            $tickets = Ticket::with('rating')->orderBy('created_at', 'desc')->get();
        } else {
            $tickets = Ticket::with('rating')
                ->where('user_id', $usuarioLogueado->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $kpis = $this->calcularKpis($usuarioLogueado, $esGestor, $equipos);

        return view('dashboard', array_merge(
            compact('equipos', 'tickets'),
            $kpis,
            // Datos iniciales para las gráficas (para que rendericen ya
            // reales desde el primer paint, sin esperar al fetch/WebSocket)
            ['statsIniciales' => $esGestor ? $this->buildStatsPayload($usuarioLogueado, $esGestor) : null]
        ));
    }

    /**
     * Endpoint JSON que consume el dashboard. Se usa como respaldo (fetch
     * inicial + polling de seguridad) y es lo que el frontend vuelve a
     * pedir cuando llega un evento de Reverb por el canal 'dashboard'.
     * GET /dashboard/stats
     */
    public function stats()
    {
        $usuarioLogueado = Auth::user();
        $esGestor = in_array($usuarioLogueado->role, ['admin', 'soporte']);

        return response()->json($this->buildStatsPayload($usuarioLogueado, $esGestor));
    }

    protected function buildStatsPayload($usuarioLogueado, bool $esGestor): array
    {
        $equipos = Inventory::where('user_id', $usuarioLogueado->id)->get();
        $kpis = $this->calcularKpis($usuarioLogueado, $esGestor, $equipos);

        return [
            'kpis' => $kpis,
            'csat' => $this->calcularCsat($usuarioLogueado, $esGestor),
            'ticketsByDay' => $this->ticketsPorDia($usuarioLogueado, $esGestor),
            'ticketsByArea' => $this->ticketsPorServicio($usuarioLogueado, $esGestor),
            'resolutionByAgent' => $esGestor ? $this->tiempoResolucionPorTecnico() : null,
            'updatedAt' => now()->toIso8601String(),
        ];
    }

    protected function calcularKpis($usuarioLogueado, bool $esGestor, $equipos): array
    {
        // Nota: el enum real de la columna `estado` (ver migración) solo
        // admite Abierto | Atendido | Cerrado | Otros. El código original
        // filtraba por 'Resuelto', un valor que nunca existe en la BD,
        // así que esa tarjeta siempre daba 0 aunque hubiera tickets
        // cerrados. Se corrige para usar los estados terminales reales.
        if ($esGestor) {
            $ticketsAbiertos = Ticket::where('estado', 'Abierto')->count();
            $ticketsResueltosHoy = Ticket::whereIn('estado', $this->estadosTerminales)
                ->whereDate('updated_at', today())
                ->count();
            $totalEquipos = Inventory::count();
        } else {
            $ticketsAbiertos = Ticket::where('user_id', $usuarioLogueado->id)->where('estado', 'Abierto')->count();
            $ticketsResueltosHoy = Ticket::where('user_id', $usuarioLogueado->id)
                ->whereIn('estado', $this->estadosTerminales)
                ->whereDate('updated_at', today())
                ->count();
            $totalEquipos = $equipos->count();
        }

        $usuariosActivos = User::count();

        return compact('ticketsAbiertos', 'ticketsResueltosHoy', 'totalEquipos', 'usuariosActivos');
    }

    // Calificación promedio (CSAT) real, tomada del modelo Rating
    protected function calcularCsat($usuarioLogueado, bool $esGestor): array
    {
        $query = Rating::query();
        if (!$esGestor) {
            $query->whereHas('ticket', fn ($q) => $q->where('user_id', $usuarioLogueado->id));
        }

        $total = (clone $query)->count();
        $promedio = $total > 0 ? round((clone $query)->avg('stars'), 2) : null;

        // Distribución 1-5 estrellas, útil para un mini-histograma si se
        // quiere mostrar más adelante.
        $distribucion = (clone $query)
            ->selectRaw('stars, COUNT(*) as total')
            ->groupBy('stars')
            ->pluck('total', 'stars');

        return [
            'promedio' => $promedio,
            'total' => $total,
            'distribucion' => collect(range(1, 5))->mapWithKeys(fn ($n) => [$n => (int) ($distribucion[$n] ?? 0)]),
        ];
    }

    // Conteo real de tickets creados por día en los últimos 7 días
    protected function ticketsPorDia($usuarioLogueado, bool $esGestor): array
    {
        $desde = now()->subDays(6)->startOfDay();

        $query = Ticket::where('created_at', '>=', $desde);
        if (!$esGestor) {
            $query->where('user_id', $usuarioLogueado->id);
        }

        $conteos = $query
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $dias = collect(range(0, 6))->map(function ($i) use ($conteos) {
            $fecha = now()->subDays(6 - $i);
            $clave = $fecha->toDateString();

            return [
                'label' => ucfirst($fecha->translatedFormat('D')),
                'fecha' => $clave,
                'total' => (int) ($conteos[$clave] ?? 0),
            ];
        });

        return [
            'labels' => $dias->pluck('label')->all(),
            'data' => $dias->pluck('total')->all(),
        ];
    }

    // Distribución real de tickets por servicio, usando el catálogo en BD
    // (antes eran categorías fijas hardcodeadas y agrupaba por texto libre).
    protected function ticketsPorServicio($usuarioLogueado, bool $esGestor): array
    {
        $categorias = Service::orderBy('orden')->orderBy('name')->pluck('name')->all();

        $query = Ticket::query();
        if (!$esGestor) {
            $query->where('user_id', $usuarioLogueado->id);
        }

        // Tickets ya enlazados al catálogo (servicio_id no nulo): se
        // agrupan por el id real, no por texto, así no importa si el
        // texto tiene mayúsculas/espacios distintos.
        $conteosPorCatalogo = (clone $query)
            ->whereNotNull('servicio_id')
            ->join('services', 'services.id', '=', 'tickets.servicio_id')
            ->selectRaw('services.name as nombre, COUNT(*) as total')
            ->groupBy('services.name')
            ->pluck('total', 'nombre');

        // Tickets legados sin servicio_id (creados antes del catálogo, o
        // que no hicieron match durante la migración de datos): se
        // agrupan por el texto libre como respaldo, para no perderlos.
        $conteosLegado = (clone $query)
            ->whereNull('servicio_id')
            ->selectRaw('servicio_requerimiento as nombre, COUNT(*) as total')
            ->groupBy('servicio_requerimiento')
            ->pluck('total', 'nombre');

        $resultado = collect($categorias)->mapWithKeys(function ($cat) use ($conteosPorCatalogo, $conteosLegado) {
            $total = (int) ($conteosPorCatalogo[$cat] ?? 0) + (int) ($conteosLegado[$cat] ?? 0);
            return [$cat => $total];
        });

        // Cualquier texto legado que no coincida con ninguna categoría
        // del catálogo actual se agrupa como "Otros" en vez de perderse.
        $otros = $conteosLegado->except($categorias)->sum();
        if ($otros > 0) {
            $resultado->put('Otros', $otros);
        }

        return [
            'labels' => $resultado->keys()->all(),
            'data' => $resultado->values()->all(),
        ];
    }

    // Tiempo promedio de resolución (horas) por técnico, calculado con
    // datos reales: diferencia entre creación y último cambio de estado
    // para tickets ya cerrados/atendidos-y-cerrados que tienen técnico
    // asignado (columna atendido_por_id, se llena desde TicketController).
    protected function tiempoResolucionPorTecnico(): array
    {
        // Partimos del listado de funcionarios que pueden atender casos,
        // no de los tickets. Asi un tecnico recien creado aparece en el
        // reporte desde el primer momento (con cero) en lugar de quedar
        // invisible hasta que cierre su primer caso. Los usuarios con rol
        // 'usuario' quedan excluidos porque no atienden requerimientos.
        $tecnicos = User::whereIn('role', ['soporte', 'admin'])
            ->orderBy('name')
            ->get(['id', 'name', 'last_name']);

        // Un caso se considera resuelto desde que el tecnico lo marca como
        // Atendido. El paso posterior a Cerrado depende de que el usuario
        // califique el servicio, y esa demora no deberia ocultar el trabajo
        // ya realizado por el area de soporte. Antes solo se contaban los
        // estados terminales, por lo que el personal de soporte no aparecia
        // en la grafica hasta que el solicitante calificara.
        $estadosResueltos = array_merge(['Atendido'], $this->estadosTerminales);

        $porTecnico = Ticket::whereIn('estado', $estadosResueltos)
            ->whereNotNull('atendido_por_id')
            ->get(['id', 'atendido_por_id', 'created_at', 'updated_at'])
            ->groupBy('atendido_por_id');

        $filas = $tecnicos->map(function ($tecnico) use ($porTecnico) {
            $suyos = $porTecnico->get($tecnico->id, collect());

            $horasPromedio = $suyos->isEmpty()
                ? 0
                : $suyos->avg(fn ($t) => $t->created_at->diffInMinutes($t->updated_at) / 60);

            return [
                'tecnico' => trim($tecnico->name . ' ' . $tecnico->last_name),
                'horas_promedio' => round($horasPromedio, 1),
                'tickets_resueltos' => $suyos->count(),
            ];
        })->sortByDesc('tickets_resueltos')->values();

        return [
            'labels' => $filas->pluck('tecnico')->all(),
            'data' => $filas->pluck('horas_promedio')->all(),
            'tickets' => $filas->pluck('tickets_resueltos')->all(),
        ];
    }
}
