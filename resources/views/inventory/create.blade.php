@extends('layouts.app') {{-- Reemplaza 'layouts.app' por la plantilla principal que use tu proyecto --}}

@section('content')
<div class="container mx-auto p-6 max-w-lg">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Registrar Nuevo Equipo en Bodega</h2>

        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf

            <!-- Seleccionar Categoría -->
            <div class="mb-4">
                <label for="category" class="block text-gray-700 font-bold mb-2">Categoría:</label>
                <select name="category" id="category" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="" disabled selected>-- Selecciona una categoría --</option>
                    <option value="Asignación Equipos Computo">Asignación Equipos Cómputo</option>
                    <option value="Asignacion Monitores">Asignación Monitores</option>
                    <option value="Asignacion Teclado">Asignación Teclado</option>
                    <option value="Asignacion Mouse">Asignación Mouse</option>
                    <option value="Asignacion Modem">Asignación Modém</option>
                    <option value="Asignacion Plan Celular">Asignación Plan Celular</option>
                    <option value="Otros Dispositivos">Otros Dispositivos</option>
                </select>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Descripción -->
            <div class="mb-4">
                <label for="description" class="block text-gray-700 font-bold mb-2">Descripción / Modelo:</label>
                <input type="text" name="description" id="description" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej: Dell Optiplex 7050" required>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Número de Serie -->
            <div class="mb-4">
                <label for="serial_number" class="block text-gray-700 font-bold mb-2">Número de Serie / Placa:</label>
                <input type="text" name="serial_number" id="serial_number" class="w-full border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ej: 123456789S">
                @error('serial_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
    <label>Cédula</label>
    <input type="text" name="cedula" class="form-control" required>
</div>

            <!-- Botones de Acción -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('inventory.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancelar</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar en Bodega</button>
            </div>
        </form>
    </div>
</div>
@endsection