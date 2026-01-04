<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestión Hotelera')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    @auth
    <!-- Navbar -->
    <nav class="bg-white shadow-sm border-b border-gray-200" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo y Desktop Nav -->
                <div class="flex items-center space-x-8">
                    <h1 class="text-xl font-semibold text-gray-900">Sistema de Gestión Hotelera</h1>
                    <div class="hidden sm:flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Dashboard</a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('hotels.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Gestionar Hoteles</a>
                        @endif
                        <a href="{{ route('analytics.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Analíticas</a>
                        <a href="{{ route('calendar.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Calendario</a>
                        <a href="{{ route('floors.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Pisos</a>
                        <a href="{{ route('rooms.index') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Habitaciones</a>
                    </div>
                </div>

                <!-- Botón de Menú Móvil -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Usuario y Logout (Desktop) -->
                <div class="hidden sm:flex items-center space-x-6">
                    <!-- Suscripción Desktop -->
                    <div class="flex flex-col items-end space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Mi Suscripción</span>
                            <span class="text-xs font-bold {{ auth()->user()->days_remaining <= 5 ? 'text-red-600' : 'text-green-600' }}">
                                {{ auth()->user()->days_remaining }} días
                            </span>
                        </div>
                        <div class="w-32 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ auth()->user()->days_remaining <= 5 ? 'bg-red-500' : 'bg-green-500' }}" 
                                 style="width: {{ auth()->user()->subscription_progress }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 border-l pl-6 border-gray-200">
                        <span class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-600 transition-colors">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menú Desplegable Móvil -->
        <div x-show="open" @click.away="open = false" class="sm:hidden border-t border-gray-100 bg-white" x-transition>
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Dashboard</a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('hotels.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Gestionar Hoteles</a>
                @endif
                <a href="{{ route('analytics.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Analíticas</a>
                <a href="{{ route('calendar.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Calendario</a>
                <a href="{{ route('floors.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Pisos</a>
                <a href="{{ route('rooms.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Habitaciones</a>
                
                <div class="pt-4 pb-1 border-t border-gray-200" x-data="{ showYape: false }">
                    <div class="px-3 text-xs font-semibold text-gray-500 uppercase mb-2">{{ auth()->user()->name }}</div>
                    
                    <!-- Mi Suscripción Móvil -->
                    <div class="px-3 py-3 bg-gray-50 rounded-lg mx-2 mb-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-bold text-gray-700">Mi Suscripción</span>
                            <span class="text-xs font-bold {{ auth()->user()->days_remaining <= 5 ? 'text-red-600' : 'text-green-600' }}">
                                {{ auth()->user()->days_remaining }} días restantes
                            </span>
                        </div>
                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mb-3">
                            <div class="h-full {{ auth()->user()->days_remaining <= 5 ? 'bg-red-500' : 'bg-green-500' }}" 
                                 style="width: {{ auth()->user()->subscription_progress }}%"></div>
                        </div>
                        <button @click="showYape = !showYape" class="w-full py-2 bg-blue-600 text-white text-sm font-bold rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2">
                            <span>Renovar con Yape</span>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                        </button>

                        <!-- Info de Yape -->
                        <div x-show="showYape" class="mt-4 p-3 bg-white border border-blue-100 rounded-md shadow-inner" x-transition>
                            <p class="text-xs text-center text-gray-600 mb-2">Escanea el QR o yapea al número:</p>
                            <p class="text-lg font-black text-blue-800 text-center mb-2">905 562 625</p>
                            <p class="text-[10px] text-center text-gray-400">Costo: S/ 35.90 por mes</p>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="block w-full">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-base font-medium text-red-600 hover:bg-gray-50">
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Main Content -->
    <main class="flex-1">
        @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-sm text-gray-600">
                Desarrollado por <span class="font-semibold">Team doj</span>
            </p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
