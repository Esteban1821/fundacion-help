@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md border-t-4 border-yellow-500 text-center mt-10">
    
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Califica nuestro servicio</h2>
    <p class="text-gray-500 mb-6">Tu opinión sobre el ticket <strong>{{ $ticket->nro_ticket }}</strong> es muy importante para nosotros.</p>

    <form action="{{ route('ratings.store', $ticket->id) }}" method="POST">
        @csrf

        <div class="mb-8">
            <label class="block text-gray-700 text-sm font-bold mb-4">¿Cómo calificarías la atención recibida?</label>
            
            <div class="flex justify-center space-x-4">
                @for ($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        {{-- CORRECCIÓN: name="stars" en lugar de "estrellas" --}}
                        <input type="radio" name="stars" value="{{ $i }}" class="sr-only peer" required>
                        <div class="text-4xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 transition">
                            ★
                        </div>
                        <span class="text-xs text-gray-500 block mt-1">{{ $i }}</span>
                    </label>
                @endfor
            </div>
        </div>

        <div class="mb-6 text-left">
            <label class="block text-gray-700 text-sm font-bold mb-2">Comentarios adicionales (Opcional):</label>
            {{-- CORRECCIÓN: name="comments" en lugar de "comentario" --}}
            <textarea name="comments" rows="3" class="shadow-sm border border-gray-300 rounded w-full py-2 px-3 text-gray-700 focus:ring-yellow-500 focus:border-yellow-500" placeholder="Cuéntanos cómo fue tu experiencia..."></textarea>
        </div>

        <div class="flex justify-center space-x-4">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800 font-bold py-2 px-4 rounded transition flex items-center">
                Cancelar
            </a>
            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow transition">
                Enviar Calificación
            </button>
        </div>
    </form>
</div>
@endsection