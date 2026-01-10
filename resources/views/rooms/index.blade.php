@extends('layouts.app')

@section('title', 'Gestión de Habitaciones')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="roomManagement()">
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Gestión de Habitaciones</h2>
            <p class="mt-1 text-sm text-gray-500">Añade, elimina o mueve habitaciones entre pisos (Drag & Drop).</p>
        </div>
    </div>

    <!-- Alert for success/error (optional, if you want local alerts) -->
    <div x-show="message.text" x-transition class="mb-4 p-4 rounded-md" :class="message.type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'" style="display: none;">
        <span x-text="message.text"></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulario -->
        <div class="bg-white rounded-2xl shadow-sm border-2 border-gray-100 p-6 md:p-8 h-fit">
            <div class="mb-6">
                <h3 class="text-xl font-black text-[#1e3a8a] uppercase tracking-tight">Añadir Nueva Habitación</h3>
                <p class="text-xs text-gray-500 mt-1">Completa los datos para crear una habitación</p>
            </div>
            
            <form action="{{ route('rooms.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <!-- Floor Selection -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <label for="floor_id" class="flex items-center text-sm font-bold text-gray-700 mb-2">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Seleccionar Piso
                        </label>
                        <select name="floor_id" id="floor_id" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                            @foreach($floors as $floor)
                            <option value="{{ $floor->id }}">{{ $floor->name }} (Piso {{ $floor->floor_number }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-2">📍 Elige en qué piso estará ubicada la habitación</p>
                    </div>

                    <!-- Room Number -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <label for="room_number" class="flex items-center text-sm font-bold text-gray-700 mb-2">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                            </svg>
                            Número de Habitación
                        </label>
                        <input type="number" name="room_number" id="room_number" required 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base font-bold text-gray-900" 
                               placeholder="Ej. 101, 102, 201...">
                        <p class="text-xs text-gray-500 mt-2">🔢 Ingresa el número que identificará esta habitación</p>
                    </div>

                    <!-- Room Type -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <label for="type" class="flex items-center text-sm font-bold text-gray-700 mb-2">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Tipo de Habitación
                        </label>
                        <select name="type" id="type" required class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-base">
                            <option value="Solo">Solo (1 persona)</option>
                            <option value="Doble">Doble (2 personas)</option>
                            <option value="Triple">Triple (3 personas)</option>
                            <option value="Matrimonial">Matrimonial (pareja)</option>
                            <option value="Familiar">Familiar (familia)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">🛏️ Define la capacidad y tipo de la habitación</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex justify-center items-center py-3 px-4 border border-transparent shadow-lg text-base font-black rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-105 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            CREAR HABITACIÓN
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Listado por Pisos -->
        <div class="lg:col-span-2 space-y-6">
            @forelse($floors as $floor)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">{{ $floor->name }}</h3>
                    <span class="text-xs text-gray-400">Arrastra para mover</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4 sortable-container" data-floor-id="{{ $floor->id }}">
                        @forelse($floor->rooms as $room)
                        <div class="relative group p-4 border rounded-lg text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-move room-item" data-id="{{ $room->id }}">
                            <span class="block text-lg font-bold text-gray-800">{{ $room->room_number }}</span>
                            <span class="block text-[10px] font-semibold text-blue-600 uppercase mb-1">{{ $room->type }}</span>
                            <span class="text-xs text-gray-500 uppercase">{{ $room->status_label }}</span>
                            
                            <!-- Botón editar flotante -->
                            <button @click.stop="openEditModal({{ $room->id }}, {{ $room->room_number }}, '{{ $room->type }}')" class="absolute -top-2 -left-2 block md:hidden md:group-hover:block bg-blue-500 text-white rounded-full p-1 hover:bg-blue-600 shadow-sm z-10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>

                            <!-- Botón eliminar flotante -->
                            <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="absolute -top-2 -right-2 block md:hidden md:group-hover:block" onsubmit="return confirm('¿Eliminar habitación?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-600 shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </form>
                        </div>
                        @empty
                        <p class="col-span-full text-sm text-gray-500 text-center py-4 empty-message">No hay habitaciones en este piso.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                <p class="text-gray-500">Debes crear al menos un piso antes de añadir habitaciones.</p>
            </div>
            @endforelse
            
            <div x-show="isSaving" class="fixed bottom-8 right-8 bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg flex items-center space-x-2" style="display: none;">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Guardando cambios...</span>
            </div>
        </div>
    </div>

    <!-- Modal Editar Habitación -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-bold mb-4">Editar Habitación</h3>
            <form :action="'/rooms/' + editRoom.id" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Número de Habitación</label>
                        <input type="number" name="room_number" x-model="editRoom.room_number" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo de Habitación</label>
                        <select name="type" x-model="editRoom.type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="Solo">Solo</option>
                            <option value="Doble">Doble</option>
                            <option value="Triple">Triple</option>
                            <option value="Matrimonial">Matrimonial</option>
                            <option value="Familiar">Familiar</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function roomManagement() {
    return {
        isSaving: false,
        message: { text: '', type: '' },
        showEditModal: false,
        editRoom: { id: null, room_number: '', type: '' },
        
        init() {
            const containers = document.querySelectorAll('.sortable-container');
            containers.forEach(container => {
                new Sortable(container, {
                    group: 'rooms',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        this.saveOrder();
                    }
                });
            });
        },

        async saveOrder() {
            this.isSaving = true;
            const rooms = [];
            
            document.querySelectorAll('.sortable-container').forEach(container => {
                const floorId = container.dataset.floorId;
                const items = container.querySelectorAll('.room-item');
                
                items.forEach((item, index) => {
                    rooms.push({
                        id: item.dataset.id,
                        floor_id: floorId,
                        position: index
                    });
                });
            });

            try {
                const response = await fetch('{{ route("rooms.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ rooms })
                });

                if (response.ok) {
                    this.message = { text: 'Orden actualizado correctamente.', type: 'success' };
                } else {
                    this.message = { text: 'Error al actualizar el orden.', type: 'error' };
                }
            } catch (error) {
                console.error('Error:', error);
                this.message = { text: 'Error de red al actualizar.', type: 'error' };
            } finally {
                this.isSaving = false;
                setTimeout(() => this.message.text = '', 3000);
            }
        },

        openEditModal(id, number, type) {
            this.editRoom = { id, room_number: number, type };
            this.showEditModal = true;
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
