@extends('layouts.app')

@section('title', 'Reporte de Caja')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="cajaReport()" x-init="init()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-12">
        <div class="mb-4 md:mb-0">
            <h1 class="text-4xl font-black text-[#1e3a8a] tracking-tight">REPORTE DE CAJA</h1>
        </div>
        
        <!-- Total Card -->
        <div class="bg-[#f97316] rounded-2xl p-6 text-white shadow-xl min-w-[300px] transform hover:scale-105 transition-transform">
            <div class="flex justify-between items-start mb-2">
                <span class="text-sm font-bold uppercase tracking-wider">TOTAL DE VENTAS</span>
                <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-black" x-text="stats.reservations_count"></span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="text-3xl font-black tracking-tighter" x-text="getFormattedTotal()"></span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar: Filter -->
        <div class="lg:col-span-2 space-y-6">
            <div>
                <label class="block text-sm font-black text-gray-500 uppercase tracking-widest mb-3">Tipo</label>
                <div class="flex space-x-2">
                    <button @click="toggleCurrency('Dólares')" 
                            :class="currency === 'Dólares' ? 'bg-[#15803d] text-white' : 'bg-gray-100 text-gray-400'"
                            class="px-4 py-2 rounded-lg text-xs font-black uppercase transition-all shadow-sm">
                        Dólares
                    </button>
                    <button @click="toggleCurrency('Soles')" 
                            :class="currency === 'Soles' ? 'bg-[#eab308] text-white' : 'bg-gray-100 text-gray-400'"
                            class="px-4 py-2 rounded-lg text-xs font-black uppercase transition-all shadow-sm">
                        Soles
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl border-2 border-gray-100 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-50">
                    <span class="text-lg font-black text-[#1e3a8a]">Fechas</span>
                    <button id="date-picker-trigger" class="p-1 hover:bg-gray-100 rounded-full transition-colors">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <input type="text" id="hidden-date-picker" class="hidden">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <button @click="selectToday()" 
                            class="col-span-2 py-2 bg-[#1e40af] text-white rounded-lg text-xs font-black uppercase mb-2 hover:bg-blue-700 transition-colors">
                        Hoy
                    </button>
                    
                    @foreach(['Enero', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'] as $index => $mes)
                    <button @click="selectMonth({{ $index + 1 }})" 
                            :class="currentMonth == {{ $index + 1 }} ? 'bg-[#1e40af] text-white' : 'bg-[#1e40af]/10 text-blue-600 hover:bg-blue-50'"
                            class="py-2.5 rounded-lg text-[10px] font-black uppercase transition-all">
                        {{ $mes }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Line Chart Section -->
            <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-gray-50 flex flex-col">
                <div class="text-center mb-8 w-full">
                    <h3 class="text-lg font-black text-gray-400 uppercase tracking-widest flex items-center justify-center">
                        Ventas Durante el Periodo
                        <span class="ml-2 text-yellow-400">🪙</span>
                    </h3>
                    <div class="text-6xl font-black text-[#1e3a8a] my-4" x-text="stats.reservations_count">0</div>
                </div>
                
                <div class="w-full min-h-[320px] h-[320px] relative" x-ignore style="display: block;">
                    <canvas id="salesLineChart" style="display: block; width: 100%; height: 100%;"></canvas>
                </div>
                
                <div class="mt-4 text-2xl font-black text-gray-800 uppercase tracking-widest" x-text="stats.period_label">ENERO</div>
            </div>

            <!-- Top 5 List -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-50">
                <h3 class="text-sm font-black text-[#1e3a8a]/60 uppercase tracking-widest mb-8">Top 5 Cuartos mas cotizados</h3>
                
                <div class="space-y-6">
                    <template x-for="room in stats.top_rooms" :key="room.number">
                        <div class="flex items-center justify-between group">
                            <span class="text-2xl font-black text-[#1e3a8a] group-hover:text-blue-600 transition-colors" x-text="room.number"></span>
                            <span class="text-xl font-black text-gray-800" x-text="formatValue(room.price)"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Categories Card -->
            <div class="lg:col-span-3 bg-white rounded-3xl p-8 shadow-sm border border-gray-50">
                <h3 class="text-sm font-black text-[#1e3a8a]/60 uppercase tracking-widest mb-8">Ventas por categoría</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <template x-for="cat in stats.sales_by_category" :key="cat.name">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-black text-gray-700 w-24" x-text="cat.name">Solo</span>
                                <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden relative">
                                    <div class="bg-[#1e40af] h-full transition-all duration-1000" :style="`width: ${cat.percentage}%`"></div>
                                    <span class="absolute right-2 top-0.5 text-[10px] font-black" 
                                          :class="cat.percentage > 20 ? 'text-white' : 'text-gray-500'"
                                          x-text="cat.percentage + '%'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="isLoading" class="fixed inset-0 bg-white/60 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="flex flex-col items-center">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-xs font-black uppercase tracking-widest text-[#1e3a8a]">Actualizando Datos...</p>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        background: #fff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        font-family: inherit;
    }
    .flatpickr-day.selected {
        background: #2563eb !important;
        border-color: #2563eb !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
function cajaReport() {
    return {
        isLoading: false,
        currency: 'Soles',
        exchangeRate: 3.85,
        currentMonth: new Date().getMonth() + 1,
        stats: {
            total_sales: 0,
            period_label: '',
            chart_data: { labels: [], values: [] },
            top_rooms: [],
            sales_by_category: [],
            reservations_count: 0
        },
        charts: {
            sales: null
        },

        async init() {
            const picker = flatpickr("#hidden-date-picker", {
                locale: 'es',
                dateFormat: "Y-m-d",
                disableMobile: true,
                onChange: (selectedDates, dateStr) => {
                    this.fetchData({ date: dateStr });
                }
            });

            document.getElementById('date-picker-trigger').addEventListener('click', () => {
                picker.open();
            });

            window.addEventListener('resize', () => {
                if (this.charts.sales) {
                    this.charts.sales.resize();
                }
            });

            await this.fetchData();
        },

        async selectMonth(month) {
            this.currentMonth = month;
            await this.fetchData({ month });
        },

        async selectToday() {
            const today = new Date().toISOString().split('T')[0];
            await this.fetchData({ date: today });
        },

        async fetchData(params = {}) {
            this.isLoading = true;
            try {
                const query = new URLSearchParams(params).toString();
                const response = await fetch(`{{ route('caja.data') }}?${query}`);
                const data = await response.json();
                
                this.stats = data;
                // Wait for Alpine to render and DOM to stabilize
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.renderCharts();
                    }, 50);
                });
            } catch (error) {
                console.error("Error fetching data:", error);
            } finally {
                this.isLoading = false;
            }
        },

        formatValue(val) {
            let value = parseFloat(val);
            if (this.currency === 'Dólares') {
                value = value / this.exchangeRate;
                return '$ ' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return 'S/ ' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        getFormattedTotal() {
            return this.formatValue(this.stats.total_sales);
        },

        toggleCurrency(newCurrency) {
            this.currency = newCurrency;
            this.renderCharts(); // Re-render chart with new values
        },

        renderCharts(retry = 0) {
            const canvas = document.getElementById('salesLineChart');
            if (!canvas) return;

            // Robustness: ensure dimensions are non-zero before initializing
            if (canvas.offsetWidth === 0 || canvas.offsetHeight === 0) {
                if (retry < 5) {
                    setTimeout(() => this.renderCharts(retry + 1), 200);
                }
                return;
            }

            const ctx = canvas.getContext('2d');
            if (!ctx) return;
            
            if (this.charts.sales) {
                this.charts.sales.destroy();
                this.charts.sales = null;
            }

            if (!this.stats.chart_data || !this.stats.chart_data.values) return;

            const chartValues = this.stats.chart_data.values.map(v => {
                let val = parseFloat(v || 0);
                return this.currency === 'Dólares' ? (val / this.exchangeRate) : val;
            });
            
            this.charts.sales = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: this.stats.chart_data.labels || [],
                    datasets: [{
                        label: 'Ventas (' + (this.currency === 'Soles' ? 'S/' : '$') + ')',
                        data: chartValues,
                        borderColor: '#2563eb',
                        backgroundColor: '#2563eb20',
                        borderWidth: 4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 4,
                        pointRadius: 6,
                        pointHoverRadius: 9,
                        tension: 0.3,
                        fill: true,
                        spanGaps: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    devicePixelRatio: window.devicePixelRatio || 2,
                    animation: {
                        duration: 600
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(30, 58, 138, 0.9)',
                            titleFont: { weight: 'bold' },
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: { 
                                font: { weight: 'bold' },
                                callback: (value) => (this.currency === 'Soles' ? 'S/ ' : '$ ') + value
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { weight: 'black', family: 'Inter' },
                                color: '#1e3a8a'
                            }
                        }
                    }
                }
            });
        }
    }
}
</script>
@endpush
@endsection
