@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md border-t-4 border-red-700">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Registrar Nuevo Usuario</h2>

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

    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- NUEVO CAMPO: Cédula -->
            <div>
                <label class="block text-gray-700 font-bold mb-2">Cédula</label>
                <input type="text" name="cedula" required class="w-full border-gray-300 rounded p-2 border focus:ring-red-500" value="{{ old('cedula') }}">
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">Nombre(s)</label>
                <input type="text" name="name" required class="w-full border-gray-300 rounded p-2 border focus:ring-red-500" value="{{ old('name') }}">
            </div>
            
            <div>
                <label class="block text-gray-700 font-bold mb-2">Apellidos</label>
                <input type="text" name="last_name" required class="w-full border-gray-300 rounded p-2 border focus:ring-red-500" value="{{ old('last_name') }}">
            </div>
            
            <div>
                <label class="block text-gray-700 font-bold mb-2">Usuario (Login)</label>
                <input type="text" name="username" id="username" required 
                       class="w-full border-gray-300 rounded p-2 border focus:ring-red-500" 
                       value="{{ old('username') }}"
                       oninput="document.getElementById('email').value = this.value.toLowerCase().replace(/\s+/g, '') + '@fundacionunivalle.com.co'">
            </div>
            
            <div>
                <label class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                <input type="email" name="email" id="email" required readonly 
                       class="w-full border-gray-300 rounded p-2 border bg-gray-100 cursor-not-allowed text-gray-500" 
                       value="{{ old('email') }}">
            </div>

            <div>
                <label class="block text-gray-700 font-bold mb-2">Contraseña Temporal</label>
                <input type="password" name="password" required minlength="8" class="w-full border-gray-300 rounded p-2 border focus:ring-red-500">
            </div>
            
            <div>
                <label class="block text-gray-700 font-bold mb-2">Rol del Sistema</label>
                <select name="role" required class="w-full border-gray-300 rounded p-2 border focus:ring-red-500">
                    <option value="usuario">Usuario Normal (Solo crea tickets)</option>
                    <option value="soporte">Soporte Técnico (Atiende tickets)</option>
                    <option value="admin">Administrador (Gestión total)</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end space-x-3 border-t pt-4">
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition">Cancelar</a>
            <button type="submit" class="bg-red-700 hover:bg-red-800 text-white px-6 py-2 rounded font-bold transition">Guardar Usuario</button>
        </div>
    </form>
</div>
@endsection