@extends('layouts.app')

@section('title', 'Analíticas del Hotel')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 sm:text-3xl">Dashboard de Analíticas</h2>
        <p class="mt-1 text-sm text-gray-500">Métricas avanzadas y rendimiento de tu hotel.</p>
    </div>

    <!-- Resumen de Tarjetas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Ocupación Total Hoy</h3>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ round($overallOccupancy, 1) }}%</p>
            <p class="text-xs text-gray-400 mt-1">{{ $occupiedRooms }} de {{ $totalRooms }} habitaciones</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pisos Operativos</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $occupancyByFloor->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Configurados en el hotel actual</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Clientes Frecuentes</h3>
            <p class="text-3xl font-bold text-gray-800 mt-2">{{ $frequentGuests->count() }}</p>
            <p class="text-xs text-gray-400 mt-1">Huéspedes recurrentes detectados</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Gráfico de Ocupación por Piso -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tasa de Ocupación por Piso</h3>
            <div class="h-64">
                <canvas id="floorOccupancyChart"></canvas>
            </div>
        </div>

        <!-- Gráfico de Tendencia (30 días) -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tendencia de Reservas (Últimos 30 días)</h3>
            <div class="h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Mejores Clientes Frequent -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Mejores Clientes Frecuentes</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Huésped</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">DNI</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Visitas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($frequentGuests as $guest)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $guest->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $guest->document_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600 font-bold">{{ $guest->count }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No hay datos suficientes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Ocupación por Cuarto (Top 10 más usados) -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Uso del Hotel</h3>
            <p class="text-sm text-gray-500">Estadísticas basadas en las reservaciones registradas en el sistema.</p>
            <div class="mt-8 space-y-4">
                @foreach($occupancyByFloor as $item)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">{{ $item['name'] }}</span>
                        <span class="text-gray-500">{{ $item['rate'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $item['rate'] }}%"></div>
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
                backgroundColor: '#1d4ed8', // blue-700
                borderRadius: 4
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: { beginAtZero: true, max: 100 }
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
                borderColor: '#1d4ed8',
                backgroundColor: 'rgba(29, 78, 216, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointBackgroundColor: '#1d4ed8'
            }]
        },
        options: commonOptions
    });
</script>
@endpush
