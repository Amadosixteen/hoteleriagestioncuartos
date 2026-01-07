@extends('layouts.app')

@section('title', 'Reporte de Caja')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="cajaReport()" x-init="init()">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-6">
        <div class="mb-2 md:mb-0">
            <h1 class="text-3xl md:text-4xl font-black text-[#1e3a8a] tracking-tight uppercase">REPORTE DE CAJA</h1>
        </div>
        
        <!-- Total Card -->
        <div class="bg-[#f97316] rounded-2xl p-6 text-white shadow-xl min-w-[300px] flex-shrink-0 transform hover:scale-105 transition-all">
            <div class="flex justify-between items-start mb-4">
                <span class="text-xs font-bold uppercase tracking-widest opacity-80">VENTAS TOTALES DEL PERIODO</span>
                <span class="bg-white/20 px-2 py-0.5 rounded text-[10px] font-black" x-text="stats.reservations_count">0</span>
            </div>
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-3xl font-black tracking-tighter" x-text="getFormattedTotal()">S/ 0.00</span>
                    <span class="text-[10px] uppercase font-bold opacity-60 mt-1" x-text="stats.period_label">---</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar: Filter -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border-2 border-gray-100 p-4 shadow-sm">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Moneda</label>
                <div class="flex space-x-2">
                    <button @click="toggleCurrency('Dólares')" 
                            :class="currency === 'Dólares' ? 'bg-[#15803d] text-white' : 'bg-gray-100 text-gray-400'"
                            class="flex-1 py-2 rounded-lg text-xs font-black uppercase transition-all shadow-sm">
                        $ USD
                    </button>
                    <button @click="toggleCurrency('Soles')" 
                            :class="currency === 'Soles' ? 'bg-[#eab308] text-white' : 'bg-gray-100 text-gray-400'"
                            class="flex-1 py-2 rounded-lg text-xs font-black uppercase transition-all shadow-sm">
                        S/ PEN
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl border-2 border-gray-100 p-4 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-50 px-1">
                    <span class="text-sm font-black text-[#1e3a8a] uppercase tracking-wider">Fechas</span>
                    <button id="date-picker-trigger" class="p-1 hover:bg-gray-100 rounded-full transition-colors text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <input type="text" id="hidden-date-picker" class="hidden">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <button @click="selectToday()" 
                            class="col-span-2 py-2 bg-[#1e40af] text-white rounded-xl text-xs font-black uppercase mb-2 hover:bg-blue-700 transition-colors shadow-md">
                        Hoy
                    </button>
                    
                    @foreach(['Enero', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Dic'] as $index => $mes)
                    <button @click="selectMonth({{ $index + 1 }})" 
                            :class="currentMonth == {{ $index + 1 }} ? 'bg-[#1e40af] text-white shadow-md' : 'bg-gray-50 text-gray-500 hover:bg-blue-50'"
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
            <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-gray-50 flex flex-col min-h-[500px]">
                <div class="text-center mb-6">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] flex items-center justify-center">
                        PROGRESO DE VENTAS
                        <span class="ml-2">📈</span>
                    </h3>
                    <div class="text-6xl font-black text-[#1e3a8a] my-2 tabular-nums" x-text="stats.reservations_count">0</div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase" x-text="stats.period_label">---</p>
                </div>
                
                <div class="flex-1 relative w-full bg-gray-50/50 rounded-2xl p-4 overflow-hidden border border-gray-100 shadow-inner">
                    <canvas id="salesLineChart" x-ref="canvas" style="width: 100%; height: 100%; display: block;"></canvas>
                </div>
            </div>

            <!-- Top 5 List -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-50">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 border-b border-gray-50 pb-4 text-center">Top 5 Cuartos</h3>
                
                <div class="space-y-6">
                    <template x-for="room in stats.top_rooms" :key="room.number">
                        <div class="flex items-center justify-between group bg-gray-50/50 p-4 rounded-2xl hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-[#1e40af] text-white rounded-xl flex items-center justify-center font-black text-lg shadow-sm" x-text="room.number"></div>
                                <span class="text-xs font-black text-gray-400 uppercase tracking-tighter">Habitación</span>
                            </div>
                            <span class="text-xl font-black text-gray-800 tabular-nums" x-text="formatValue(room.price)"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Categories Card -->
            <div class="lg:col-span-3 bg-white rounded-3xl p-8 shadow-sm border border-gray-50">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8 border-b border-gray-50 pb-4 text-center">Ventas por categoría</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <template x-for="cat in stats.sales_by_category" :key="cat.name">
                        <div class="flex flex-col space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest" x-text="cat.name">---</span>
                                <span class="text-[10px] font-black text-blue-600" x-text="cat.percentage + '%'">0%</span>
                            </div>
                            <div class="bg-gray-100 rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="bg-[#1e40af] h-full transition-all duration-1000" :style="`width: ${cat.percentage}%`"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="isLoading" class="fixed inset-0 bg-white/80 backdrop-blur-md z-[100] flex items-center justify-center" x-transition>
        <div class="flex flex-col items-center bg-white p-8 rounded-3xl shadow-2xl border border-gray-100">
            <div class="w-16 h-16 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-6"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1e3a8a] animate-pulse">Sincronizando Sistema...</p>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar { background: #fff; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
    .flatpickr-day.selected { background: #2563eb !important; border-color: #2563eb !important; }
    #salesLineChart { image-rendering: -webkit-optimize-contrast; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
// Atomic Global Reference for the Chart
var _CajaGlobalChartInstance = null;

function cajaReport() {
    return {
        isLoading: false,
        currency: 'Soles',
        exchangeRate: 3.85,
        currentMonth: new Date().getMonth() + 1,
        stats: {
            total_sales: 0,
            period_label: 'CARGANDO...',
            chart_data: { labels: [], values: [] },
            top_rooms: [],
            sales_by_category: [],
            reservations_count: 0
        },

        async init() {
            const picker = flatpickr("#hidden-date-picker", {
                locale: 'es', dateFormat: "Y-m-d", disableMobile: true,
                onChange: (dates, str) => this.fetchData({ date: str })
            });

            document.getElementById('date-picker-trigger').onclick = () => picker.open();

            window.onresize = () => {
                if (_CajaGlobalChartInstance) _CajaGlobalChartInstance.resize();
            };

            await this.fetchData();
        },

        async selectMonth(m) { this.currentMonth = m; await this.fetchData({ month: m }); },
        async selectToday() { await this.fetchData({ date: new Date().toISOString().split('T')[0] }); },

        async fetchData(params = {}) {
            this.isLoading = true;
            try {
                const query = new URLSearchParams(params).toString();
                const response = await fetch(`{{ route('caja.data') }}?${query}`);
                this.stats = await response.json();
                
                // Allow DOM to settle before drawing
                Alpine.nextTick(() => {
                    setTimeout(() => this.drawChart(), 250);
                });
            } catch (e) {
                console.error("Fetch failed", e);
            } finally {
                this.isLoading = false;
            }
        },

        formatValue(val) {
            let n = parseFloat(val);
            if (isNaN(n)) n = 0;
            if (this.currency === 'Dólares') {
                return '$ ' + (n / this.exchangeRate).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
            return 'S/ ' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        getFormattedTotal() { return this.formatValue(this.stats.total_sales); },

        toggleCurrency(c) {
            this.currency = c;
            this.drawChart();
        },

        drawChart() {
            const canvas = document.getElementById('salesLineChart');
            if (!canvas || canvas.offsetWidth === 0) {
                // If container is not ready, wait and retry once
                setTimeout(() => this.drawChart(), 300);
                return;
            }

            if (_CajaGlobalChartInstance) {
                _CajaGlobalChartInstance.destroy();
                _CajaGlobalChartInstance = null;
            }

            const data = this.stats.chart_data;
            if (!data || !data.values || data.values.length === 0) return;

            const values = data.values.map(v => {
                let n = parseFloat(v);
                return this.currency === 'Dólares' ? (n / this.exchangeRate) : n;
            });

            try {
                const ctx = canvas.getContext('2d');
                _CajaGlobalChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            data: values,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.08)',
                            borderWidth: 4,
                            pointRadius: 6,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#2563eb',
                            pointBorderWidth: 3,
                            tension: 0.35,
                            fill: true,
                            spanGaps: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 800 },
                        devicePixelRatio: window.devicePixelRatio || 2,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { color: '#f8fafc' },
                                ticks: { 
                                    font: { weight: 'bold', size: 10 },
                                    callback: (v) => (this.currency === 'Soles' ? 'S/' : '$') + v.toLocaleString()
                                }
                            },
                            x: { 
                                grid: { display: false },
                                ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
                            }
                        }
                    }
                });
            } catch (err) {
                console.error("Draw crash", err);
            }
        }
    };
}
</script>
@endpush
@endsection
