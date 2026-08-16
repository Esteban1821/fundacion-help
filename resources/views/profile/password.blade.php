@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md border-t-4 border-red-700 mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <svg class="w-6 h-6 mr-2 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
        Cambiar Contraseña
    </h2>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 mb-6 rounded shadow-sm">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Contraseña Actual</label>
            <input type="password" name="current_password" required class="w-full border-gray-300 rounded p-2 border focus:ring-red-700">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Nueva Contraseña</label>
            <input type="password" name="password" required minlength="8" class="w-full border-gray-300 rounded p-2 border focus:ring-red-700" placeholder="Mínimo 8 caracteres">
        </div>

        <div class="mb-8">
            <label class="block text-gray-700 font-bold mb-2">Confirmar Nueva Contraseña</label>
            <input type="password" name="password_confirmation" required minlength="8" class="w-full border-gray-300 rounded p-2 border focus:ring-red-700">
        </div>

        <div class="flex flex-col space-y-3">
            <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white px-6 py-2 rounded font-bold transition shadow">
                Actualizar Contraseña
            </button>
            <a href="{{ route('dashboard') }}" class="w-full text-center text-gray-500 hover:text-gray-800 font-medium py-2">
                Cancelar y Volver
            </a>
        </div>
    </form>
</div>
@endsection