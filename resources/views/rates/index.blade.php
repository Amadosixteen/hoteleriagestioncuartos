@extends('layouts.app')

@section('title', 'Tarifas de Habitaciones')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="rateManager()">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-black leading-tight text-gray-900 sm:text-3xl">Gestión de Tarifas</h2>
            <p class="mt-1 text-sm text-gray-500 uppercase tracking-widest font-bold">Configuración de precios y planes tarifarios</p>
        </div>
    </div>

    <!-- Alertas Globales -->
    <template x-if="notification.show">
        <div x-show="notification.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="mb-6 p-4 rounded-xl border flex items-center justify-between"
             :class="notification.type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="notification.type === 'success'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    <path x-show="notification.type === 'error'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <span class="font-bold text-sm" x-text="notification.message"></span>
            </div>
            <button @click="notification.show = false" class="text-current opacity-50 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </template>

    <div class="space-y-8">
        <!-- SECCIÓN 1: ACTUALIZACIÓN MASIVA (POR TIPO) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-black text-gray-900">Actualización por Tipo</h3>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-tighter">Aplica un precio estándar a todas las habitaciones de una categoría</p>
                </div>
                <div class="flex items-center space-x-2">
                     <span class="flex h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                     <span class="text-[10px] font-black text-blue-600 uppercase">Proceso Masivo</span>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach(['Solo', 'Doble', 'Triple', 'Matrimonial', 'Familiar'] as $type)
                <div class="bg-white border-2 border-gray-100 rounded-2xl p-5 hover:border-blue-200 transition-all group flex flex-col justify-between h-full shadow-sm hover:shadow-md">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $type }}</span>
                        <h4 class="text-xl font-black text-gray-800 mb-4">{{ $type }}</h4>
                    </div>
                    <div class="space-y-3">
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-gray-400 font-bold">S/</span>
                            <input type="number" 
                                   step="0.01" 
                                   x-model="bulkPrices['{{ $type }}']" 
                                   class="w-full pl-8 pr-4 py-2 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-gray-800" 
                                   placeholder="0.00">
                        </div>
                        <button @click="updateBulk('{{ $type }}')" 
                                :disabled="isLoading"
                                class="w-full py-2 bg-gray-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-600 transition-all disabled:opacity-50">
                            Aplicar
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- SECCIÓN 2: LISTADO DETALLADO -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-black text-gray-900">Ajuste Individual</h3>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-tighter">Refina los precios habitación por habitación</p>
            </div>
            
            <div class="p-0 overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Habitación</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Piso</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Precio Actual</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($floors as $floor)
                            @foreach($floor->rooms as $room)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-lg font-black text-gray-900">{{ $room->room_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter border {{ $room->type == 'Familiar' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'bg-blue-100 text-blue-700 border-blue-200' }}">
                                        {{ $room->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 font-medium">
                                    {{ $floor->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-gray-400 font-bold text-sm">S/</span>
                                        <input type="number" 
                                               step="0.01" 
                                               id="price-{{ $room->id }}"
                                               value="{{ number_format($room->price, 2, '.', '') }}" 
                                               class="w-24 bg-transparent border-none focus:ring-0 font-black text-gray-900 text-lg p-0"
                                               @change="updateIndividual({{ $room->id }}, $el.value)">
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded-md uppercase">Editable</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación Soft -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-auto">
                {{ $floors->links() }}
            </div>
        </div>
    </div>

    <!-- Cargando Overlay -->
    <div x-show="isLoading" 
         class="fixed inset-0 bg-white/50 backdrop-blur-sm z-50 flex items-center justify-center"
         x-transition:enter="transition opacity-0"
         x-transition:enter-end="opacity-100"
         style="display: none;">
        <div class="flex flex-col items-center">
            <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-sm font-black text-gray-900 uppercase tracking-widest">Sincronizando Precios...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function rateManager() {
    return {
        isLoading: false,
        notification: { show: false, message: '', type: 'success' },
        bulkPrices: {
            'Solo': '',
            'Doble': '',
            'Triple': '',
            'Matrimonial': '',
            'Familiar': ''
        },

        async updateBulk(type) {
            const price = this.bulkPrices[type];
            if (!price || price < 0) return;

            if (!confirm(`¿Estás seguro de actualizar TODAS las habitaciones de tipo ${type} a S/ ${price}?`)) return;

            await this.sendUpdate({
                update_type: 'bulk_type',
                type: type,
                price: price
            });
            
            // Recargar para ver los cambios en la tabla
            window.location.reload();
        },

        async updateIndividual(roomId, price) {
            if (price < 0) return;
            
            await this.sendUpdate({
                update_type: 'individual',
                room_id: roomId,
                price: price
            });
        },

        async sendUpdate(payload) {
            this.isLoading = true;
            try {
                const response = await fetch('{{ route('rates.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                
                if (data.success) {
                    this.showNotification(data.message, 'success');
                } else {
                    this.showNotification('Error al actualizar precio', 'error');
                }
            } catch (error) {
                this.showNotification('Error de conexión', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        showNotification(message, type) {
            this.notification = { show: true, message, type };
            if (type === 'success') {
                setTimeout(() => this.notification.show = false, 3000);
            }
        }
    }
}
</script>
@endpush
@endsection
