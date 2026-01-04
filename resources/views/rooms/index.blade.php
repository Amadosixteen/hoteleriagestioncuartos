@extends('layouts.app')

@section('title', 'Gestión de Habitaciones')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Gestión de Habitaciones</h2>
            <p class="mt-1 text-sm text-gray-500">Añade o elimina habitaciones de los pisos de tu hotel.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 h-fit">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Añadir Nueva Habitación</h3>
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="floor_id" class="block text-sm font-medium text-gray-700">Seleccionar Piso</label>
                        <select name="floor_id" id="floor_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->name }} (Piso {{ $floor->floor_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700">Número de Habitación</label>
                        <input type="number" name="room_number" id="room_number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. 101">
                    </div>
                    <div>
                        <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Crear Habitación
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Listado por Pisos -->
        <div class="lg:col-span-2 space-y-6">
            @forelse($floors as $floor)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">{{ $floor->name }}</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                        @forelse($floor->rooms as $room)
                        <div class="relative group p-4 border rounded-lg text-center bg-gray-50 hover:bg-gray-100 transition-colors">
                            <span class="block text-lg font-bold text-gray-800">{{ $room->room_number }}</span>
                            <span class="text-xs text-gray-500 uppercase">{{ $room->status }}</span>
                            
                            <!-- Botón eliminar flotante -->
                            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="absolute -top-2 -right-2 hidden group-hover:block" onsubmit="return confirm('¿Eliminar habitación?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                        @empty
                        <p class="col-span-full text-sm text-gray-500 text-center py-4">No hay habitaciones en este piso.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <p class="text-gray-500">Debes crear al menos un piso antes de añadir habitaciones.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
