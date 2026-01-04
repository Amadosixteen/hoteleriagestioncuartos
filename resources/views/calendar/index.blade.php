@extends('layouts.app')

@section('title', 'Calendario de Ocupación')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Calendario de Ocupación</h2>
            <p class="mt-1 text-sm text-gray-500">Histórico de huéspedes y habitaciones por fecha.</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <form action="{{ route('calendar.index') }}" method="GET" class="flex items-center space-x-3">
                <label for="date" class="text-sm font-medium text-gray-700">Seleccionar fecha:</label>
                <input type="date" name="date" id="date" value="{{ $date }}" 
                    class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                    onchange="this.form.submit()">
            </form>
        </div>
    </div>

    @php
        $formattedDate = \Carbon\Carbon::parse($date)->translatedFormat('d \d\e F, Y');
    @endphp

    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                Ocupación el {{ $formattedDate }}
            </h3>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                {{ $reservations->count() }} Habitaciones ocupadas
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Piso / Cuarto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Huésped Principal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-in</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Otros Huéspedes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reservations as $res)
                    @php
                        $primary = $res->guests->firstWhere('is_primary', true);
                        $others = $res->guests->where('is_primary', false);
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">{{ $res->room->room_number }}</div>
                            <div class="text-xs text-gray-500">{{ $res->room->floor->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $primary ? $primary->full_name : 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $primary ? $primary->document_number : '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $res->check_in_at->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $res->check_out_at->format('H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($others->count() > 0)
                            <div class="flex -space-x-1 overflow-hidden">
                                @foreach($others as $other)
                                <div class="inline-block h-6 w-6 rounded-full bg-blue-200 ring-2 ring-white flex items-center justify-center text-[8px] font-bold text-blue-700 uppercase" title="{{ $other->full_name }}">
                                    {{ substr($other->first_name, 0, 1) }}{{ substr($other->last_name, 0, 1) }}
                                </div>
                                @endforeach
                            </div>
                            @else
                            <span class="text-xs text-gray-400">Ninguno</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay registros</h3>
                            <p class="mt-1 text-sm text-gray-500">No se encontraron reservaciones para esta fecha.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
