@extends('layouts.app')

@section('title', 'Analíticas del Hotel')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-6">
        <div class="mb-2 md:mb-0">
            <h1 class="text-3xl md:text-4xl font-black text-[#1e3a8a] tracking-tight uppercase">ANALÍTICAS DEL HOTEL</h1>
            <p class="mt-1 text-sm text-gray-500">Métricas avanzadas y rendimiento de tu hotel.</p>
        </div>
        
        <!-- Highlight Card: Overall Occupancy -->
        <div class="bg-[#f97316] rounded-2xl p-6 text-white shadow-xl min-w-[300px] flex-shrink-0 transform hover:scale-105 transition-all">
            <div class="flex justify-between items-start mb-4">
                <span class="text-xs font-bold uppercase tracking-widest opacity-80">OCUPACIÓN TOTAL HOY</span>
                <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-black">{{ $occupiedRooms }}/{{ $totalRooms }}</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-black tracking-tighter">{{ round($overallOccupancy, 1) }}%</span>
                    <span class="text-[10px] uppercase font-bold opacity-60 mt-1">{{ $occupiedRooms }} de {{ $totalRooms }} habitaciones</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border-2 border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Pisos Operativos</h3>
                    <p class="text-4xl font-black text-[#1e3a8a]">{{ $occupancyByFloor->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Configurados en el hotel actual</p>
                </div>
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#1e40af]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border-2 border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Clientes Frecuentes</h3>
                    <p class="text-4xl font-black text-[#1e3a8a]">{{ $frequentGuests->count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Huéspedes recurrentes detectados</p>
                </div>
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-[#1e40af]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Floor Occupancy Chart -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-50">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 text-center">Tasa de Ocupación por Piso</h3>
            <div class="bg-gray-50/50 rounded-2xl p-4 border border-gray-100 shadow-inner" style="height: 300px;">
                <canvas id="floorOccupancyChart"></canvas>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-50">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6 text-center">Tendencia de Reservas (Últimos 30 días)</h3>
            <div class="bg-gray-50/50 rounded-2xl p-4 border border-gray-100 shadow-inner" style="height: 300px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Frequent Guests Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-50 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">Top 10 Clientes Frecuentes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">Huésped</th>
                            <th class="px-6 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-wider">DNI</th>
                            <th class="px-6 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-wider">Visitas</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($frequentGuests as $guest)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $guest->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $guest->document_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-blue-50 text-[#1e40af]">
                                    {{ $guest->count }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400">No hay datos suficientes</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hotel Usage -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-50">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 text-center">Uso del Hotel</h3>
            <p class="text-xs text-gray-500 text-center mb-8">Estadísticas basadas en las reservaciones registradas en el sistema.</p>
            <div class="space-y-6">
                @foreach($occupancyByFloor as $item)
                <div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="font-black text-gray-700 uppercase text-xs tracking-wide">{{ $item['name'] }}</span>
                        <span class="font-black text-[#1e40af] text-xs">{{ $item['rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden shadow-inner">
                        <div class="bg-[#1e40af] h-3 rounded-full transition-all duration-1000" style="width: {{ $item['rate'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración compartida
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        }
    };

    // Gráfico de Pisos
    const floorCtx = document.getElementById('floorOccupancyChart').getContext('2d');
    new Chart(floorCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($occupancyByFloor->pluck('name')) !!},
            datasets: [{
                label: 'Ocupación %',
                data: {!! json_encode($occupancyByFloor->pluck('rate')) !!},
                backgroundColor: '#1e40af',
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: { 
                    beginAtZero: true, 
                    max: 100,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Gráfico de Tendencia
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($trend->pluck('date')) !!},
            datasets: [{
                label: 'Reservas diarias',
                data: {!! json_encode($trend->pluck('count')) !!},
                borderColor: '#1e40af',
                backgroundColor: 'rgba(30, 64, 175, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#1e40af',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush
