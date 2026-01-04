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
                <div class="hidden sm:flex items-center space-x-4">
                    <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                            Cerrar sesión
                        </button>
                    </form>
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
                
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-3 text-xs font-semibold text-gray-500 uppercase">{{ auth()->user()->name }}</div>
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
