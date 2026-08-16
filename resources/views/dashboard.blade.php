@extends('layouts.app')

@section('content')

    {{-- MENSAJES DE ÉXITO --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ==========================================
         1. SECCIÓN DASHBOARD (SOLO ADMIN Y SOPORTE)
    =========================================== --}}
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'soporte')
        
        {{-- Tarjetas (KPIs) --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 lg:gap-5 mb-8">
            <!-- Tarjeta: Tickets Abiertos -->
            <a href="#panel-tickets" class="block transform transition hover:-translate-y-0.5 duration-200">
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 hover:shadow-md p-5 border-t-4 border-red-600 h-full flex flex-col items-center text-center gap-2 transition">
                    <div class="w-11 h-11 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-lg">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <p class="text-gray-500 text-[11px] font-bold uppercase tracking-wider">Tickets Abiertos</p>
                    <p class="text-3xl font-extrabold text-gray-900 tabular-nums" id="kpi-abiertos">{{ $ticketsAbiertos }}</p>
                </div>
            </a>

            <!-- Tarjeta: Tickets Resueltos Hoy -->
            <a href="#panel-tickets" class="block transform transition hover:-translate-y-0.5 duration-200">
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 hover:shadow-md p-5 border-t-4 border-emerald-600 h-full flex flex-col items-center text-center gap-2 transition">
                    <div class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="text-gray-500 text-[11px] font-bold uppercase tracking-wider">Resueltos Hoy</p>
                    <p class="text-3xl font-extrabold text-gray-900 tabular-nums" id="kpi-resueltos-hoy">{{ $ticketsResueltosHoy }}</p>
                </div>
            </a>

            <!-- Tarjeta: Equipos en Inventario -->
            <a href="{{ route('inventory.index') }}" class="block transform transition hover:-translate-y-0.5 duration-200">
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 hover:shadow-md p-5 border-t-4 border-blue-600 h-full flex flex-col items-center text-center gap-2 transition">
                    <div class="w-11 h-11 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <p class="text-gray-500 text-[11px] font-bold uppercase tracking-wider">Equipos en Inventario</p>
                    <p class="text-3xl font-extrabold text-gray-900 tabular-nums" id="kpi-equipos">{{ $totalEquipos }}</p>
                </div>
            </a>

            <!-- Tarjeta: Usuarios Activos -->
            <a href="{{ route('users.index') }}" class="block transform transition hover:-translate-y-0.5 duration-200">
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 hover:shadow-md p-5 border-t-4 border-slate-500 h-full flex flex-col items-center text-center gap-2 transition">
                    <div class="w-11 h-11 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-lg">
                        <i class="fas fa-users"></i>
                    </div>
                    <p class="text-gray-500 text-[11px] font-bold uppercase tracking-wider">Usuarios Activos</p>
                    <p class="text-3xl font-extrabold text-gray-900 tabular-nums" id="kpi-usuarios">{{ $usuariosActivos }}</p>
                </div>
            </a>

            <!-- Tarjeta: CSAT (Calificación promedio) -->
            <div class="bg-white rounded-xl shadow-sm ring-1 ring-black/5 hover:shadow-md p-5 border-t-4 border-amber-500 h-full flex flex-col items-center text-center gap-2 transition col-span-2 lg:col-span-1"
                 id="kpi-csat-card" title="Sin calificaciones aún">
                <div class="w-11 h-11 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center text-lg">
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-500 text-[11px] font-bold uppercase tracking-wider">Satisfacción (CSAT)</p>
                <p class="text-3xl font-extrabold text-gray-900 tabular-nums" id="kpi-csat">&mdash;</p>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Gráfico de Barras -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-chart-bar text-blue-600 mr-2"></i>Tickets por Día (Últimos 7 días)</h3>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span id="live-ping" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-300 opacity-75"></span>
                            <span id="live-dot" class="relative inline-flex rounded-full h-2.5 w-2.5 bg-gray-400"></span>
                        </span>
                        <span class="text-xs text-gray-400" id="live-status">Conectando...</span>
                        <span class="text-xs text-gray-300">·</span>
                        <span class="text-xs text-gray-400" id="dashboard-updated-at"></span>
                    </div>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="ticketsByDayChart"></canvas>
                </div>
            </div>

            <!-- Gráfico de Dona -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-chart-pie text-blue-600 mr-2"></i>Por Servicio</h3>
                <div class="relative h-72 w-full flex justify-center">
                    <canvas id="ticketsByAreaChart"></canvas>
                </div>
            </div>

            <!-- Gráfico: Tiempo de resolución por técnico -->
            <div class="lg:col-span-3 bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-user-clock text-blue-600 mr-2"></i>Tiempo Promedio de Resolución por Técnico (horas)</h3>
                <div class="relative h-64 w-full" id="resolutionChartWrapper">
                    <canvas id="resolutionByAgentChart"></canvas>
                    <p id="resolutionChartEmpty" class="hidden text-center text-sm text-gray-400 py-10">Todavía no hay tickets cerrados con técnico asignado.</p>
                </div>
            </div>
        </div>
        
    @endif
    {{-- FIN SECCIÓN DASHBOARD --}}


    {{-- ==========================================
         2. PANEL DE TICKETS (VISIBLE PARA TODOS)
    =========================================== --}}
    <div id="panel-tickets" class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-700 mb-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Panel de Control - Tickets</h2>
            
            {{-- BOTÓN: Disponible para TODOS los roles --}}
            <a href="{{ route('tickets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition">
                + Nuevo Requerimiento
            </a>
        </div>

        <div x-data="{ tab: 'abiertos' }">
            <div class="border-b border-gray-200 mb-4">
                <nav class="-mb-px flex space-x-8">
                    <button @click="tab = 'abiertos'" :class="{ 'border-red-500 text-red-600': tab === 'abiertos', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'abiertos' }" class="py-4 px-1 border-b-2 font-medium text-sm">Casos Abiertos</button>
                    <button @click="tab = 'atendidos'" :class="{ 'border-red-500 text-red-600': tab === 'atendidos', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'atendidos' }" class="py-4 px-1 border-b-2 font-medium text-sm">Casos Atendidos</button>
                    <button @click="tab = 'cerrados'" :class="{ 'border-red-500 text-red-600': tab === 'cerrados', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'cerrados' }" class="py-4 px-1 border-b-2 font-medium text-sm">Otros Casos (Cerrados)</button>
                </nav>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nro Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado y Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tickets as $ticket)
                            {{-- Lógica de agrupación en pestañas (Array includes para agrupar estados) --}}
                            <tr x-show="(tab === 'abiertos' && '{{ $ticket->estado }}' === 'Abierto') || 
                                        (tab === 'atendidos' && '{{ $ticket->estado }}' === 'Atendido') || 
                                        (tab === 'cerrados' && ['Cerrado', 'Otros'].includes('{{ $ticket->estado }}'))">
                                
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $ticket->nro_ticket }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $ticket->servicio_requerimiento }}</td>
                                <td class="px-6 py-4 text-sm">
                                    {{-- Insignia del Estado --}}
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                        {{ $ticket->estado === 'Abierto' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $ticket->estado === 'Pendiente' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $ticket->estado === 'Atendido' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $ticket->estado === 'Cerrado' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $ticket->estado === 'Cancelado' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ $ticket->estado }}
                                    </span>

                                    {{-- Botón de Calificar o visualización de Estrellas --}}
                                    @if($ticket->estado === 'Atendido' && !$ticket->rating)
                                        <a href="{{ route('ratings.create', $ticket->id) }}" class="ml-3 inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded shadow transition">
                                            ⭐ Calificar
                                        </a>
                                    @elseif($ticket->rating)
                                        <span class="ml-3 text-xs text-gray-500 font-medium">
                                            {{ $ticket->rating->stars }} ⭐
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No hay tickets registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ==========================================
         3. MI INVENTARIO (VISIBLE PARA TODOS)
    =========================================== --}}
    <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-gray-500"
         x-data="{ categoriaAbierta: null }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Mi Inventario Tecnológico</h3>
            @if($equipos->count() > 0)
                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                    {{ $equipos->count() }} equipo{{ $equipos->count() === 1 ? '' : 's' }}
                </span>
            @endif
        </div>

        @forelse($equipos->groupBy('category') as $categoria => $equiposDeCategoria)
            <div class="border rounded-lg mb-2 overflow-hidden">
                {{-- Cabecera de la categoría (clic para expandir/colapsar) --}}
                <button type="button"
                        @click="categoriaAbierta = (categoriaAbierta === '{{ $categoria }}') ? null : '{{ $categoria }}'"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition text-left">
                    <span class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase text-red-700">{{ $categoria }}</span>
                        <span class="text-[11px] text-gray-400 bg-white border rounded-full px-2 py-0.5">{{ $equiposDeCategoria->count() }}</span>
                    </span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform"
                         :class="{ 'rotate-180': categoriaAbierta === '{{ $categoria }}' }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                {{-- Contenido: la lista de equipos de esa categoría --}}
                <div x-show="categoriaAbierta === '{{ $categoria }}'" x-cloak x-transition
                     class="grid grid-cols-1 md:grid-cols-3 gap-3 p-4 bg-white">
                    @foreach($equiposDeCategoria as $equipo)
                        <div class="border rounded-lg p-3 bg-gray-50">
                            <p class="text-gray-800 font-medium text-sm">{{ $equipo->description }}</p>
                            <p class="text-xs text-gray-500 mt-1">S/N: {{ $equipo->serial_number ?? 'N/A' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-4 bg-yellow-50 rounded">
                <p class="text-yellow-700 text-sm">No tienes equipos asignados todavía.</p>
            </div>
        @endforelse
    </div>


    {{-- LIBRERÍAS (FontAwesome visible para todos, Chart.js solo para Admin/Soporte) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'soporte')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        {{-- Cliente WebSocket (Reverb habla el protocolo Pusher) + Echo, vía CDN
             para no depender de un build de Vite en esta vista. --}}
        <script src="https://cdn.jsdelivr.net/npm/pusher-js@8/dist/web/pusher.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1/dist/echo.iife.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const STATS_URL = "{{ route('dashboard.stats') }}";
                const POLL_MS = 60000; // respaldo por si el WebSocket se cae: cada 60s

                // Datos iniciales renderizados por el servidor (evita
                // parpadeo/gráfica vacía mientras llega el primer fetch)
                const datosIniciales = @json($statsIniciales);

                const colorPorServicio = {
                    'Usuarios y Accesos': '#2563eb',
                    'Hardware y Equipos': '#d97706',
                    'Software y Aplicaciones': '#059669',
                    'Redes y Conectividad': '#dc2626',
                    'Otros': '#6b7280',
                };

                const ctxBar = document.getElementById('ticketsByDayChart').getContext('2d');
                const chartBar = new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: datosIniciales?.ticketsByDay?.labels ?? [],
                        datasets: [{
                            label: 'Tickets Creados',
                            data: datosIniciales?.ticketsByDay?.data ?? [],
                            backgroundColor: 'rgba(37, 99, 235, 0.7)',
                            borderColor: 'rgba(37, 99, 235, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 400 },
                        plugins: { legend: { display: false } },
                        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                    }
                });

                const ctxDoughnut = document.getElementById('ticketsByAreaChart').getContext('2d');
                const chartDoughnut = new Chart(ctxDoughnut, {
                    type: 'doughnut',
                    data: {
                        labels: datosIniciales?.ticketsByArea?.labels ?? [],
                        datasets: [{
                            data: datosIniciales?.ticketsByArea?.data ?? [],
                            backgroundColor: (datosIniciales?.ticketsByArea?.labels ?? []).map(l => colorPorServicio[l] || '#6b7280'),
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 400 },
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 15, usePointStyle: true, font: { size: 11 } }
                            }
                        }
                    }
                });

                const ctxResolucion = document.getElementById('resolutionByAgentChart').getContext('2d');
                const chartResolucion = new Chart(ctxResolucion, {
                    type: 'bar',
                    data: {
                        labels: datosIniciales?.resolutionByAgent?.labels ?? [],
                        datasets: [{
                            label: 'Horas promedio de resolución',
                            data: datosIniciales?.resolutionByAgent?.data ?? [],
                            backgroundColor: 'rgba(5, 150, 105, 0.7)',
                            borderColor: 'rgba(5, 150, 105, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 400 },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => {
                                        const tickets = datosIniciales?.resolutionByAgent?.tickets?.[ctx.dataIndex];
                                        return `${ctx.parsed.x} h promedio` + (tickets ? ` · ${tickets} tickets` : '');
                                    }
                                }
                            }
                        },
                        scales: { x: { beginAtZero: true, title: { display: true, text: 'Horas' } } }
                    }
                });

                function actualizarUI(stats) {
                    if (!stats) return;

                    // KPIs
                    document.getElementById('kpi-abiertos').textContent = stats.kpis.ticketsAbiertos;
                    document.getElementById('kpi-resueltos-hoy').textContent = stats.kpis.ticketsResueltosHoy;
                    document.getElementById('kpi-equipos').textContent = stats.kpis.totalEquipos;
                    document.getElementById('kpi-usuarios').textContent = stats.kpis.usuariosActivos;

                    // CSAT
                    const csat = stats.csat ?? {};
                    document.getElementById('kpi-csat').textContent = csat.promedio ?? '—';
                    const csatCard = document.getElementById('kpi-csat-card');
                    csatCard.title = csat.total > 0
                        ? 'Promedio sobre 5 · ' + csat.total + ' calificación' + (csat.total === 1 ? '' : 'es')
                        : 'Sin calificaciones aún';

                    // Gráfico de barras (tickets por día)
                    chartBar.data.labels = stats.ticketsByDay.labels;
                    chartBar.data.datasets[0].data = stats.ticketsByDay.data;
                    chartBar.update();

                    // Gráfico de dona (por servicio)
                    chartDoughnut.data.labels = stats.ticketsByArea.labels;
                    chartDoughnut.data.datasets[0].data = stats.ticketsByArea.data;
                    chartDoughnut.data.datasets[0].backgroundColor = stats.ticketsByArea.labels.map(l => colorPorServicio[l] || '#6b7280');
                    chartDoughnut.update();

                    // Gráfico de resolución por técnico
                    const resolucion = stats.resolutionByAgent ?? { labels: [], data: [], tickets: [] };
                    const hayDatos = resolucion.labels.length > 0;
                    document.getElementById('resolutionChartEmpty').classList.toggle('hidden', hayDatos);
                    document.getElementById('resolutionByAgentChart').classList.toggle('hidden', !hayDatos);
                    chartResolucion.data.labels = resolucion.labels;
                    chartResolucion.data.datasets[0].data = resolucion.data;
                    chartResolucion._ticketsPorAgente = resolucion.tickets; // usado por el tooltip
                    chartResolucion.update();

                    const marca = document.getElementById('dashboard-updated-at');
                    if (marca && stats.updatedAt) {
                        const hora = new Date(stats.updatedAt).toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        marca.textContent = 'Actualizado ' + hora;
                    }
                }

                async function refrescarDashboard() {
                    try {
                        const resp = await fetch(STATS_URL, {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });
                        if (!resp.ok) return;
                        const stats = await resp.json();
                        actualizarUI(stats);
                    } catch (e) {
                        // Silencioso: si falla un refresco, se reintenta en el próximo ciclo
                        console.warn('No se pudo refrescar el dashboard:', e);
                    }
                }

                function marcarEstadoConexion(estado) {
                    // estado: 'live' | 'polling' | 'connecting'
                    const dot = document.getElementById('live-dot');
                    const ping = document.getElementById('live-ping');
                    const texto = document.getElementById('live-status');
                    const colores = { live: 'bg-green-500', polling: 'bg-amber-500', connecting: 'bg-gray-400' };
                    const textos = { live: 'En vivo', polling: 'Actualización periódica', connecting: 'Conectando...' };

                    Object.values(colores).forEach(c => { dot.classList.remove(c); ping.classList.remove(c); });
                    dot.classList.add(colores[estado]);
                    ping.classList.add(colores[estado]);
                    ping.style.display = estado === 'live' ? '' : 'none';
                    texto.textContent = textos[estado];
                }

                // Primer render inmediato con lo que trajo el servidor
                actualizarUI(datosIniciales);
                refrescarDashboard();

                // --- Tiempo real vía Laravel Reverb (WebSockets) ---
                const reverbKey = document.querySelector('meta[name="reverb-key"]')?.content;
                let echo = null;
                let pollingInterval = null;

                function activarRespaldoPorPolling() {
                    marcarEstadoConexion('polling');
                    if (!pollingInterval) {
                        pollingInterval = setInterval(refrescarDashboard, POLL_MS);
                    }
                }

                function desactivarRespaldoPorPolling() {
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                        pollingInterval = null;
                    }
                }

                try {
                    if (reverbKey && typeof Echo !== 'undefined') {
                        marcarEstadoConexion('connecting');

                        echo = new Echo({
                            broadcaster: 'reverb',
                            key: reverbKey,
                            wsHost: document.querySelector('meta[name="reverb-host"]')?.content,
                            wsPort: Number(document.querySelector('meta[name="reverb-port"]')?.content || 80),
                            wssPort: Number(document.querySelector('meta[name="reverb-port"]')?.content || 443),
                            forceTLS: (document.querySelector('meta[name="reverb-scheme"]')?.content) === 'https',
                            enabledTransports: ['ws', 'wss'],
                            auth: {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                                }
                            },
                        });

                        echo.connector.pusher.connection.bind('connected', () => {
                            marcarEstadoConexion('live');
                            desactivarRespaldoPorPolling();
                        });

                        echo.connector.pusher.connection.bind('unavailable', activarRespaldoPorPolling);
                        echo.connector.pusher.connection.bind('failed', activarRespaldoPorPolling);
                        echo.connector.pusher.connection.bind('disconnected', activarRespaldoPorPolling);

                        echo.private('dashboard').listen('.stats.updated', () => {
                            refrescarDashboard();
                        });

                        // Si en unos segundos no logró conectar, no dejamos
                        // el dashboard "muerto": activamos el respaldo.
                        setTimeout(() => {
                            if (echo?.connector?.pusher?.connection?.state !== 'connected') {
                                activarRespaldoPorPolling();
                            }
                        }, 8000);
                    } else {
                        activarRespaldoPorPolling();
                    }
                } catch (e) {
                    console.warn('No se pudo iniciar Reverb, usando actualización periódica:', e);
                    activarRespaldoPorPolling();
                }

                // Refresca también cuando el usuario vuelve a la pestaña
                document.addEventListener('visibilitychange', function () {
                    if (!document.hidden) refrescarDashboard();
                });
            });
        </script>
    @endif
@endsection