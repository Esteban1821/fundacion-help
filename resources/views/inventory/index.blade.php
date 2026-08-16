@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4" x-data="{ tab: 'asignados' }">
    
    <!-- ENCABEZADO -->
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Inventario</h2>
        
        <!-- Botón para ir a la vista de Asignar -->
        <a href="{{ route('inventory.assign') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-sm text-sm transition duration-150">
            Asignar Equipo a Usuario
        </a>
    </div>

    <!-- PESTAÑAS (TABS) -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button @click="tab = 'asignados'" 
                    :class="{ 'border-blue-500 text-blue-600': tab === 'asignados', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'asignados' }" 
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                Equipos Asignados
            </button>
            <button @click="tab = 'bodega'" 
                    :class="{ 'border-blue-500 text-blue-600': tab === 'bodega', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'bodega' }" 
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none transition-colors duration-200">
                Equipos en Bodega (Stock)
            </button>
        </nav>
    </div>

    <!-- MENSAJES DE ÉXITO -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- ========================================================== -->
    <!-- CONTENIDO PESTAÑA 1: EQUIPOS ASIGNADOS -->
    <!-- ========================================================== -->
    <div x-show="tab === 'asignados'" x-transition style="display: none;">
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Usuario Asignado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($asignados as $equipo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $equipo->category }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $equipo->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $equipo->user ? $equipo->user->name : 'Desconocido' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <!-- BOTÓN DE DEVOLVER A BODEGA (CORREGIDO) -->
                                <form action="{{ route('inventory.return', $equipo->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que deseas devolver este equipo a la bodega?');">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-1 px-3 rounded text-sm transition duration-150 shadow-sm">
                                        Devolver a Bodega
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No hay equipos asignados en este momento.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================== -->
    <!-- CONTENIDO PESTAÑA 2: EQUIPOS EN BODEGA -->
    <!-- ========================================================== -->
    <div x-show="tab === 'bodega'" x-transition style="display: none;">
        
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-700">Equipos disponibles en stock</h3>
            <!-- AQUI ESTÁ LA RUTA CORREGIDA -->
            <a href="{{ route('inventory.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-150 shadow-sm text-sm">
                + Nuevo Registro
            </a>
        </div>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Descripción</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Cantidad Disponible</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($enBodega as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->category }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600">
                                {{ $item->cantidad_disponible }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">La bodega está vacía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- BARRA DE FILTROS Y DESCARGA PDF -->
    <div class="bg-white p-4 rounded shadow mb-6 mt-6">
        <form action="{{ route('inventory.downloadPdf') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Filtrar por Estado:</label>
                    <select name="estado" class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                        <option value="">-- Todos --</option>
                        <option value="asignados">Solo Asignados</option>
                        <option value="bodega">Solo en Bodega</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Filtrar por Categoría:</label>
                    <input type="text" name="category" placeholder="Ej. Laptops" class="border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-150 flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                    Descargar PDF
                </button>
            </div>
        </form>
    </div>
    <!-- BUSCADOR GENERAL DE INVENTARIO -->
    <div class="bg-white p-4 rounded shadow mb-6">
        <form action="{{ route('inventory.index') }}" method="GET" class="flex items-center gap-2">
            <div class="relative flex-1">
                <input type="text" 
                       name="buscar" 
                       value="{{ request('buscar') }}" 
                       placeholder="Buscar por categoría, descripción, serie o usuario..." 
                       class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm transition duration-150 shadow-sm flex items-center gap-1">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                Buscar
            </button>

            @if(request('buscar'))
                <a href="{{ route('inventory.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-3 rounded text-sm transition duration-150">
                    Limpiar
                </a>
            @endif
        </form>
    </div>
</div>
@endsection