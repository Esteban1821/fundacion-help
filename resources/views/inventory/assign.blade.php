@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Asignar Equipo a Usuario</h2>

        <form action="{{ route('inventory.storeAssign') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 font-bold mb-2">Seleccionar Usuario</label>
                <select name="user_id" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500" required>
                    <option value="" disabled selected>Elija un usuario...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-bold mb-2">Seleccionar Equipo de Bodega</label>
                <select name="inventory_id" class="w-full border border-gray-300 p-2 rounded focus:outline-none focus:border-blue-500" required>
                    <option value="" disabled selected>Elija un equipo disponible...</option>
                    @foreach($equiposDisponibles as $equipo)
                        <option value="{{ $equipo->id }}">
                            {{ $equipo->category }} - {{ $equipo->description }} 
                            @if($equipo->serial_number) (SN: {{ $equipo->serial_number }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-150 shadow-sm">
                    Asignar Equipo
                </button>
                <a href="{{ route('inventory.index') }}" class="ml-4 text-gray-500 hover:text-gray-700 transition duration-150">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection