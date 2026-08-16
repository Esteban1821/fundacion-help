@extends('layouts.app')

@section('content')
@if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
<div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-gray-800">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Bandeja de Entrada - Soporte Técnico</h2>
            <p class="text-gray-500 text-sm mt-1">Gestión general de requerimientos de la Fundación</p>
        </div>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Ticket</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Solicitante</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Área / Servicio</th>
                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-wider">Acción</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-900">{{ $ticket->nro_ticket }}</span><br>
                            <span class="text-xs text-gray-500">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-gray-800">{{ $ticket->user->name }} {{ $ticket->user->last_name }}</span><br>
                            <span class="text-xs text-gray-500">{{ $ticket->email_solicitante }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-800">{{ $ticket->area_solicitante }}</span><br>
                            <span class="text-xs text-gray-500">{{ $ticket->servicio_requerimiento }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $ticket->estado === 'Abierto' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $ticket->estado === 'Atendido' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $ticket->estado === 'Cerrado' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ $ticket->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
    <a href="{{ route('soporte.show', $ticket->id) }}" class="text-red-700 hover:text-white bg-red-50 hover:bg-red-700 px-4 py-2 rounded border border-red-200 transition inline-block">
        Atender
    </a>
</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            ¡Excelente trabajo! No hay tickets pendientes en la bandeja.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection