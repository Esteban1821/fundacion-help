@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Atención de Requerimiento</h2>
        <a href="{{ route('soporte.index') }}" class="text-red-700 hover:text-red-900 font-semibold text-sm">
            &larr; Volver a la Bandeja
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md border-t-4 border-gray-800">
            <div class="flex justify-between border-b pb-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500">Ticket Nro</p>
                    <p class="text-xl font-bold text-gray-900">{{ $ticket->nro_ticket }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Fecha de Creación</p>
                    <p class="font-medium">{{ $ticket->created_at->format('d/m/Y H:i A') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Solicitante</p>
                    <p class="text-gray-800">{{ $ticket->user->name }} {{ $ticket->user->last_name }}</p>
                    <p class="text-sm text-gray-600">{{ $ticket->email_solicitante }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase">Ubicación</p>
                    <p class="text-gray-800">{{ $ticket->area_solicitante }}</p>
                    <p class="text-sm text-gray-600">{{ $ticket->cargo_solicitante }}</p>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded border mb-4">
                <p class="text-xs text-red-700 font-bold uppercase mb-1">{{ $ticket->servicio_requerimiento }} - {{ $ticket->subservicio_requerimiento }}</p>
                <p class="text-gray-800 font-medium whitespace-pre-line">{{ $ticket->descripcion_requerimiento }}</p>
            </div>

            {{-- El adjunto se descarga por la ruta tickets.adjunto y no por la URL
                 /storage, porque esa ruta valida que quien descarga sea el dueño
                 del ticket o personal de soporte. --}}
            @if($ticket->soporte_adjunto)
                <div class="mt-4">
                    <a href="{{ route('tickets.adjunto', $ticket->id) }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                        📎 Descargar archivo adjunto
                    </a>
                </div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-red-700 h-fit">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Gestión del Ticket</h3>
            
            <form action="{{ route('soporte.update', $ticket->id) }}" method="POST">
                @csrf
                @method('PUT') <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Estado Actual:</label>
                    <select name="estado" class="shadow-sm border border-gray-300 rounded w-full py-2 px-3 text-gray-700 focus:ring-red-500 focus:border-red-500">
                        <option value="Abierto" {{ $ticket->estado == 'Abierto' ? 'selected' : '' }}>Abierto</option>
                        <option value="Atendido" {{ $ticket->estado == 'Atendido' ? 'selected' : '' }}>Atendido</option>
                        <option value="Cerrado" {{ $ticket->estado == 'Cerrado' ? 'selected' : '' }}>Cerrado</option>
                        <option value="Otros" {{ $ticket->estado == 'Otros' ? 'selected' : '' }}>Otros</option>
                    </select>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Prioridad del Sistema:</label>
                    <input type="text" disabled value="{{ $ticket->prioridad }}" class="bg-gray-100 border border-gray-300 text-gray-500 rounded py-2 px-3 w-full cursor-not-allowed font-bold">
                </div>

                <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-4 rounded transition shadow">
                    Actualizar Estado
                </button>
            </form>
        </div>

    </div>
</div>
@endsection