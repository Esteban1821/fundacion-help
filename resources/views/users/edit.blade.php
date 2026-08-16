@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md border-t-4 border-blue-600">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Editar Usuario: {{ $user->name }}</h2>

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

    {{-- Importante: El action va a users.update y usamos @method('PUT') --}}
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 font-bold mb-2">Nombre(s)</label>
                <input type="text" name="name" required class="w-full border-gray-300 rounded p-2 border focus:ring-blue-500" value="{{ old('name', $user->name) }}">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Apellidos</label>
                <input type="text" name="last_name" required class="w-full border-gray-300 rounded p-2 border focus:ring-blue-500" value="{{ old('last_name', $user->last_name) }}">
            </div>
            
            <div>
                <label class="block text-gray-700 font-bold mb-2">Usuario (Login)</label>
                <input type="text" name="username" id="username" required 
                       class="w-full border-gray-300 rounded p-2 border focus:ring-blue-500" 
                       value="{{ old('username', $user->username) }}"
                       oninput="document.getElementById('email').value = this.value.toLowerCase().replace(/\s+/g, '') + '@fundacionunivalle.com.co'">
            </div>
            <div>
                <label class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                <input type="email" name="email" id="email" required readonly 
                       class="w-full border-gray-300 rounded p-2 border bg-gray-100 cursor-not-allowed text-gray-500" 
                       value="{{ old('email', $user->email) }}">
            </div>

            <div class="bg-blue-50 p-4 rounded border">
                <label class="block text-gray-700 font-bold mb-2">Nueva Contraseña (Opcional)</label>
                <input type="password" name="password" minlength="8" class="w-full border-gray-300 rounded p-2 border focus:ring-blue-500" placeholder="Dejar en blanco para no cambiarla">
            </div>
            
            <div>
                <label class="block text-gray-700 font-bold mb-2">Rol del Sistema</label>
                <select name="role" required class="w-full border-gray-300 rounded p-2 border focus:ring-blue-500">
                    <option value="usuario" {{ $user->role === 'usuario' ? 'selected' : '' }}>Usuario Normal (Solo crea tickets)</option>
                    <option value="soporte" {{ $user->role === 'soporte' ? 'selected' : '' }}>Soporte Técnico (Atiende tickets)</option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Administrador (Gestión total)</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end space-x-3 border-t pt-4">
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancelar</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-bold transition">Guardar Cambios</button>
        </div>
    </form>
</div>
@endsection