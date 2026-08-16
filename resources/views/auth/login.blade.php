<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fundación del Valle Help</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen w-screen overflow-hidden bg-white flex">

    {{-- ==========================================
         PANEL IZQUIERDO — Marca (oculto en móvil)
    =========================================== --}}
    <div class="hidden lg:flex lg:w-[45%] xl:w-[40%] relative flex-col justify-between text-white p-12 xl:p-16 overflow-hidden"
         style="background: linear-gradient(155deg, #7f1d1d 0%, #991b1b 45%, #1e293b 100%);">

        {{-- Formas decorativas sutiles --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/5"></div>
        <div class="absolute bottom-0 -left-16 w-72 h-72 rounded-full bg-white/5"></div>
        <div class="absolute top-1/3 right-10 w-40 h-40 rounded-full border border-white/10"></div>

        <div class="relative">
            <img src="{{ asset('img/logo-blanco.png') }}" alt="Fundación Univalle" class="h-14 w-auto mb-1">
        </div>

        <div class="relative">
            <h1 class="text-4xl xl:text-5xl font-extrabold leading-tight mb-4">
                Mesa de<br>Ayuda
            </h1>
            <p class="text-red-100/90 text-lg max-w-sm mb-10">
                Plataforma interna de soporte tecnológico e inventario de la Fundación Univalle.
            </p>

            <ul class="space-y-4">
                <li class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center text-lg flex-shrink-0">🎫</span>
                    <span class="text-sm text-red-50/90">Solicita y da seguimiento a tus tickets de soporte</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center text-lg flex-shrink-0">💻</span>
                    <span class="text-sm text-red-50/90">Consulta el inventario tecnológico asignado a ti</span>
                </li>
                <li class="flex items-center gap-3">
                    <span class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center text-lg flex-shrink-0">📊</span>
                    <span class="text-sm text-red-50/90">Estadísticas de atención en tiempo real</span>
                </li>
            </ul>
        </div>

        <p class="relative text-xs text-red-100/60">&copy; {{ date('Y') }} Fundación Universidad del Valle.</p>
    </div>

    {{-- ==========================================
         PANEL DERECHO — Formulario
    =========================================== --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 bg-slate-50 lg:bg-white overflow-y-auto">
        <div class="w-full max-w-sm">

            {{-- Logo visible solo en móvil (el panel de marca está oculto) --}}
            <div class="lg:hidden text-center mb-8">
                <img src="{{ asset('img/logo-color.png') }}" alt="Fundación Univalle" class="mx-auto h-14 w-auto mb-3">
                <p class="text-gray-500 text-sm">Plataforma de Soporte Tecnológico</p>
            </div>

            <div class="mb-8 hidden lg:block">
                <h2 class="text-2xl font-bold text-gray-800">Bienvenido de vuelta</h2>
                <p class="text-gray-500 mt-1 text-sm">Ingresa tus credenciales institucionales para continuar.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 rounded mb-5" role="alert">
                    <ul class="list-disc pl-5 text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Usuario Institucional</label>
                    <input type="text" name="username" id="username" class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all" placeholder="Ej: dgiron" required value="{{ old('username') }}" autofocus>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Contraseña</label>
                    <input type="password" name="password" id="password" class="shadow-sm appearance-none border border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent transition-all" placeholder="••••••••" required>
                </div>

                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-3 px-4 rounded-lg w-full transition duration-300 shadow-md shadow-red-700/20">
                    Ingresar al Sistema
                </button>
            </form>

            <p class="lg:hidden text-center text-xs text-gray-400 mt-8">&copy; {{ date('Y') }} Fundación Universidad del Valle.</p>
        </div>
    </div>

</body>
</html>