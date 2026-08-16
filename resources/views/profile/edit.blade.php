@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md border-t-4 border-gray-800 mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Mi Perfil</h2>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 mb-6 rounded shadow-sm">
            <p class="font-bold">Por favor corrige los siguientes errores:</p>
            <ul class="list-disc pl-5 text-sm mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Nombre(s)</label>
            <input type="text" name="name" required class="w-full border-gray-300 rounded p-2 border focus:ring-red-700" value="{{ old('name', $user->name) }}">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Apellidos</label>
            <input type="text" name="last_name" required class="w-full border-gray-300 rounded p-2 border focus:ring-red-700" value="{{ old('last_name', $user->last_name) }}">
        </div>

        {{-- Datos de solo lectura (El usuario no puede cambiar su propio username o rol) --}}
        <div class="bg-gray-50 p-4 rounded border mb-6 text-sm text-gray-600">
            <p><strong>Usuario de acceso:</strong> {{ $user->username }}</p>
            <p><strong>Correo electrónico:</strong> {{ $user->email }}</p>
            <p><strong>Rol en el sistema:</strong> <span class="uppercase font-semibold">{{ $user->role }}</span></p>
            <p class="text-xs text-gray-400 mt-2">* Para cambiar tu correo o usuario, contacta al administrador.</p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t">
            <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded transition">Cancelar</a>
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-6 py-2 rounded font-bold transition">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection