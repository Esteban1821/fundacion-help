@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-md border-t-4 border-red-700">
    
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h2 class="text-2xl font-bold text-gray-800">Solicitar Soporte Técnico</h2>
        <a href="{{ route('dashboard') }}" class="text-red-600 hover:text-red-800 font-semibold text-sm">
            &larr; Volver al Panel
        </a>
    </div>

    {{-- BLOQUE DE ERRORES AÑADIDO AQUÍ --}}
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

    <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" x-data="ticketForm({{ json_encode($servicios) }})">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Área Solicitante <span class="text-red-500">*</span></label>
                <input type="text" name="area_solicitante" required class="border border-gray-300 rounded w-full p-2.5 focus:ring-red-500 focus:border-red-500">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Cargo Solicitante <span class="text-red-500">*</span></label>
                <input type="text" name="cargo_solicitante" required class="border border-gray-300 rounded w-full p-2.5 focus:ring-red-500 focus:border-red-500">
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 mb-6">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Servicio <span class="text-red-500">*</span></label>
                <select name="servicio_requerimiento" x-model="servicioSeleccionado" @change="actualizarSubservicios()" required class="border border-gray-300 rounded w-full p-2.5">
                    <option value="">Seleccione un servicio</option>
                    <template x-for="servicio in Object.keys(listaServicios)" :key="servicio">
                        <option :value="servicio" x-text="servicio"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Subservicio <span class="text-red-500">*</span></label>
                <select name="subservicio_requerimiento" required class="border border-gray-300 rounded w-full p-2.5">
                    <option value="">Seleccione un subservicio</option>
                    <template x-for="sub in subserviciosDisponibles" :key="sub">
                        <option :value="sub" x-text="sub"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Descripción del Problema <span class="text-red-500">*</span></label>
                <textarea name="descripcion_requerimiento" rows="4" required class="border border-gray-300 rounded w-full p-2.5"></textarea>
            </div>

            <div class="bg-gray-50 p-4 border rounded">
                <label class="block text-gray-700 text-sm font-bold mb-2">Soporte Adjunto (Evidencia)</label>
                <input type="file" name="soporte_adjunto" class="block w-full text-sm text-gray-900 border border-gray-300 rounded cursor-pointer bg-white">
                <p class="mt-1 text-xs text-gray-500">Imágenes o documentos (Máx. 5MB).</p>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Prioridad <span class="text-red-500">*</span></label>
                <select name="prioridad" required class="border border-gray-300 rounded w-full p-2.5">
                    <option value="Baja">Baja</option>
                    <option value="Media" selected>Media</option>
                    <option value="Alta">Alta</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 px-8 rounded shadow-md transition">
                Enviar Requerimiento
            </button>
        </div>
    </form>
</div>

<script>
    function ticketForm(datosServicios) {
        return {
            listaServicios: datosServicios,
            servicioSeleccionado: '',
            subserviciosDisponibles: [],
            actualizarSubservicios() {
                this.subserviciosDisponibles = this.servicioSeleccionado ? this.listaServicios[this.servicioSeleccionado] : [];
            }
        }
    }
</script>
@endsection