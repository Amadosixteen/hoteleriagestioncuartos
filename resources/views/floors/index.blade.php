@extends('layouts.app')

@section('title', 'Gestión de Pisos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Gestión de Pisos</h2>
            <p class="mt-1 text-sm text-gray-500">Añade o elimina los pisos de tu hotel actual.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulario -->
        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8 h-fit">
            <div class="mb-6">
                <h3 class="text-xl font-black text-[#1e3a8a] uppercase tracking-tight">Añadir Nuevo Piso</h3>
                <p class="text-xs text-gray-500 mt-1">Completa los datos para crear un piso</p>
            </div>
            
            <form action="{{ route('floors.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <!-- Floor Number -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <label for="floor_number" class="flex items-center text-sm font-bold text-gray-700 mb-2">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            Número de Piso
                        </label>
                        <input type="number" name="floor_number" id="floor_number" required 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base font-bold text-gray-900" 
                               placeholder="Ej. 1, 2, 3...">
                        <p class="text-xs text-gray-500 mt-2">🔢 Ingresa el número del piso (1 = primer piso, 2 = segundo piso, etc.)</p>
                    </div>

                    <!-- Floor Name -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <label for="name" class="flex items-center text-sm font-bold text-gray-700 mb-2">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Nombre del Piso
                        </label>
                        <input type="text" name="name" id="name" required 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base font-bold text-gray-900" 
                               placeholder="Ej. Primer Piso, Segundo Piso...">
                        <p class="text-xs text-gray-500 mt-2">📝 Dale un nombre descriptivo al piso para identificarlo fácilmente</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex justify-center items-center py-3 px-4 border border-transparent shadow-lg text-base font-black rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-105 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            CREAR PISO
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Lista -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Habitaciones</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($floors as $floor)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $floor->floor_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $floor->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $floor->rooms()->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <form action="{{ route('floors.destroy', $floor->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este piso?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No hay pisos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
