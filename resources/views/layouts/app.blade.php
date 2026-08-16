<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="reverb-key" content="{{ config('broadcasting.connections.reverb.key') }}">
        <meta name="reverb-host" content="{{ config('broadcasting.connections.reverb.options.host') }}">
        <meta name="reverb-port" content="{{ config('broadcasting.connections.reverb.options.port') }}">
        <meta name="reverb-scheme" content="{{ config('broadcasting.connections.reverb.options.scheme') }}">
    @endauth
    <title>Fundación del Valle - Mesa de Ayuda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-100 min-h-screen">

    <nav class="bg-red-800 shadow-lg relative z-40">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-10">
            <div class="flex justify-between h-16">
                
                {{-- Lado Izquierdo: Logo de la Fundación --}}
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="hover:opacity-80 transition duration-300">
                        <img src="{{ asset('img/logo-blanco.png') }}" alt="Fundación Univalle" class="h-9 w-auto">
                    </a>
                </div>
                
                {{-- Lado Derecho: Botones Administrativos y Menú de Usuario --}}
                <div class="flex items-center gap-2">
                    
                    @auth
                        {{-- Botón de Usuarios (Solo Admin) --}}
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('users.index') }}" class="flex items-center gap-1.5 text-white text-sm font-semibold bg-white/10 hover:bg-white/20 px-3 py-2 rounded-md transition">
                                <span aria-hidden="true">👥</span> Usuarios
                            </a>
                        @endif

                        {{-- Botones de Soporte e Inventario (Admin y Soporte) --}}
                        @if(Auth::user()->role === 'soporte' || Auth::user()->role === 'admin')
                            <a href="{{ route('inventory.index') }}" class="flex items-center gap-1.5 text-white text-sm font-semibold bg-white/10 hover:bg-white/20 px-3 py-2 rounded-md transition">
                                <span aria-hidden="true">📦</span> Inventario General
                            </a>
                            <a href="{{ route('soporte.index') }}" class="flex items-center gap-1.5 text-white text-sm font-semibold bg-white/10 hover:bg-white/20 px-3 py-2 rounded-md transition">
                                <span aria-hidden="true">⚙️</span> Panel de Soporte
                            </a>
                        @endif
                        
                        {{-- MENÚ DESPLEGABLE DEL USUARIO --}}
                        <div class="relative ml-2 pl-2 border-l border-white/20" x-data="{ menuAbierto: false }">
                            
                            <button @click="menuAbierto = !menuAbierto" @click.away="menuAbierto = false" class="flex items-center gap-2 text-white focus:outline-none transition py-2 px-3 rounded-md bg-white/10 hover:bg-white/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="font-semibold text-sm">{{ ucwords(strtolower(Auth::user()->name)) }}</span>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': menuAbierto }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div x-show="menuAbierto" x-transition.opacity 
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl py-1 border border-gray-200 z-50" 
                                 style="display: none;">
                                
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-700 transition">
                                    👤 Perfil del usuario
                                </a>
                                <a href="{{ route('password.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-red-700 transition">
                                    🔑 Cambiar contraseña
                                </a>
                                
                                <hr class="my-1 border-gray-200">
                                
                                <form action="{{ route('logout') }}" method="POST" 
                                      onsubmit="return confirm('¿Estás seguro de que deseas cerrar sesión y salir del sistema?');">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-bold transition">
                                        🚪 Desconectarse
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-10 py-8">
        @yield('content')
    </main>

</body>
</html>