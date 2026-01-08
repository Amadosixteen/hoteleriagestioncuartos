@extends('layouts.app')

@section('title', 'Dashboard - Gestión de Cuartos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="dashboardApp()">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-900">Gestión de Cuartos</h2>
        
        <!-- Filtros -->
        <div class="flex flex-wrap items-center gap-4">
            <!-- Filtro de Tipo (Existente) -->
            <div class="flex flex-wrap gap-2 bg-white p-1 rounded-xl border border-gray-100 shadow-sm h-fit">
                <button @click="filterType = 'all'" 
                    :class="filterType === 'all' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-wider">
                    Todos
                </button>
                <template x-for="type in ['Solo', 'Doble', 'Triple', 'Matrimonial', 'Familiar']">
                    <button @click="filterType = type" 
                        :class="filterType === type ? 'bg-blue-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-wider"
                        x-text="type">
                    </button>
                </template>
            </div>

            <!-- Filtro de Precio (Nuevo) -->
            <div class="flex items-center gap-2 bg-white p-1 rounded-xl border border-gray-100 shadow-sm h-fit">
                <button @click="sortByPrice = (sortByPrice === 'desc' ? null : 'desc')"
                    :class="sortByPrice === 'desc' ? 'bg-green-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                    <span>Mayor a Menor</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Si NO hay orden por precio, mostramos por pisos (Original) -->
    <template x-if="!sortByPrice">
        <div>
            @foreach($floors as $floor)
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $floor->name }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-10 gap-3">
                    @foreach($floor->rooms as $room)
                        <div x-show="filterType === 'all' || filterType === '{{ $room->type }}'" x-transition>
                            <x-room-card :room="$room" />
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </template>

    <!-- Si HAY orden por precio, mostramos todo junto ordenado -->
    <template x-if="sortByPrice">
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Habitaciones (Ordenadas por Precio)</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-10 gap-3">
                @php
                    $allRooms = $floors->flatMap->rooms->sortByDesc('price');
                @endphp
                @foreach($allRooms as $room)
                    <div x-show="filterType === 'all' || filterType === '{{ $room->type }}'" x-transition>
                        <x-room-card :room="$room" />
                    </div>
                @endforeach
            </div>
        </div>
    </template>

    <!-- Modal de Reserva -->
    <x-reservation-modal />

    <!-- Burbuja Flotante de WhatsApp Soporte -->
    <div class="fixed bottom-6 right-6 z-[60] group">
        <div class="absolute bottom-full right-0 mb-4 w-60 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none translate-y-2 group-hover:translate-y-0">
            <div class="bg-white border border-gray-100 shadow-2xl rounded-2xl p-4 scale-95 group-hover:scale-100 transition-transform origin-bottom-right">
                <p class="text-sm font-bold text-gray-800 mb-1">¿Necesitas ayuda?</p>
                <p class="text-xs text-gray-500 leading-relaxed">Conversa con nosotros si tienes dudas sobre el uso del sistema o algún problema técnico.</p>
                <div class="absolute bottom-[-6px] right-6 w-3 h-3 bg-white border-r border-b border-gray-100 rotate-45"></div>
            </div>
        </div>
        <a href="https://wa.me/51905562625?text=Hola%2C%20tengo%20una%20duda%20sobre%20el%20sistema%20de%20gesti%C3%B3n%20hotelera" 
           target="_blank" 
           class="flex items-center justify-center w-16 h-16 bg-[#25D366] text-white rounded-full shadow-2xl hover:bg-[#20ba59] transition-all duration-300 hover:scale-110 active:scale-95 animate-bounce-subtle">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.067 2.877 1.215 3.076.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.45L0 24l7.105-1.864a11.834 11.834 0 005.735 1.486c.002 0 .004 0 .006 0 6.55 0 11.885-5.336 11.888-11.892a11.83 11.83 0 00-3.411-8.412"/>
            </svg>
        </a>
    </div>

</div>

<style>
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    .animate-bounce-subtle {
        animation: bounce-subtle 3s infinite ease-in-out;
    }
    .status-available { background-color: #f0f9ff !important; border-color: #bae6fd !important; }
    .status-available:hover { background-color: #e0f2fe !important; }
    
    .status-occupied { background-color: #f0fdf4 !important; border-color: #bbf7d0 !important; }
    .status-occupied:hover { background-color: #dcfce7 !important; }
    
    .status-expired { background-color: #fee2e2 !important; border-color: #f87171 !important; border-width: 3px !important; }
    .status-expired:hover { background-color: #fecaca !important; }
    
    .status-cleaning { background-color: #fefce8 !important; border-color: #fef08a !important; }
    .status-cleaning:hover { background-color: #fef9c3 !important; }

    .btn-cleaning { background-color: #eab308 !important; color: white !important; }
    .btn-cleaning:hover { background-color: #ca8a04 !important; }
    
    .btn-available { background-color: #16a34a !important; color: white !important; }
    .btn-available:hover { background-color: #15803d !important; }
</style>
@endsection

@push('scripts')
<script>
function dashboardApp() {
    return {
        showModal: false,
        selectedRoom: null,
        selectedRoomType: '',
        selectedRoomPrice: 0,
        filterType: 'all',
        sortByPrice: null,
        reservation: null,
        guests: [{ 
            document_type: 'dni', 
            custom_document_type: '',
            document_number: '', 
            first_name: '', 
            last_name: '', 
            gender: '', 
            vehicle_plate: '' 
        }],
        duration_hours: 2,
        isEditing: false,
        isLoading: false,
        errorMessage: '',
        currentRoomStatus: 'available',
        editingOvertimeCharge: false,
        customOvertimeCharge: 0,

        init() {
            // Update room status every 30 seconds
            setInterval(() => this.updateRoomStatus(), 30000);
            
            // Update countdown every second
            setInterval(() => this.updateCountdowns(), 1000);

            // Listen for room click events
            window.addEventListener('open-reservation-modal', (event) => {
                this.selectedRoomType = event.detail.roomType;
                this.selectedRoomPrice = event.detail.roomPrice;
                this.openModal(event.detail.roomId, event.detail.hasReservation, event.detail.status);
            });
        },

        getOvertimeDisplay() {
            if (!this.reservation || !this.reservation.check_out_at) return '0h 0m';
            
            const checkoutAt = new Date(this.reservation.check_out_at);
            const now = new Date();
            const diff = now - checkoutAt;
            
            if (diff <= 0) return '0h 0m';
            
            const hours = Math.floor(diff / 3600000);
            const minutes = Math.floor((diff % 3600000) / 60000);
            return `${hours}h ${minutes}m`;
        },

        calculateOvertimeCharge() {
            if (!this.reservation || !this.reservation.check_out_at) return 0;
            
            const checkoutAt = new Date(this.reservation.check_out_at);
            const now = new Date();
            const diff = now - checkoutAt;
            
            if (diff <= 0) return 0;
            
            const overtimeHours = diff / 3600000; // Convert ms to hours
            const overtimeRate = {{ auth()->user()->tenant->overtime_rate_per_hour ?? 0 }};
            
            return overtimeHours * overtimeRate;
        },

        async applyOvertimeCharge() {
            if (!this.reservation || this.isLoading) return;
            
            if (!confirm('¿Estás seguro de aplicar el cobro de tiempo extra? Esta acción actualizará el total de la reserva.')) {
                return;
            }

            this.isLoading = true;
            this.errorMessage = '';

            try {
                const response = await fetch(`/reservations/${this.reservation.id}/apply-overtime`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        custom_charge: this.customOvertimeCharge
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    // Update reservation data
                    this.reservation.overtime_charge = data.overtime_charge;
                    this.reservation.overtime_hours = data.overtime_hours;
                    this.reservation.total_price = data.new_total;
                    
                    alert(`Cobro de tiempo extra aplicado correctamente:\n${data.overtime_hours.toFixed(2)} horas\nCobro: S/ ${data.overtime_charge.toFixed(2)}`);
                } else {
                    this.errorMessage = data.error || 'Error al aplicar el cobro de tiempo extra';
                }
            } catch (error) {
                console.error('Error applying overtime charge:', error);
                this.errorMessage = 'Ocurrió un error inesperado. Inténtalo de nuevo.';
            } finally {
                this.isLoading = false;
            }
        },

        openModal(roomId, hasReservation = false, status = 'available') {
            this.selectedRoom = roomId;
            this.isEditing = hasReservation;
            this.currentRoomStatus = status;
            this.editingOvertimeCharge = false;
            
            if (hasReservation) {
                this.loadReservation(roomId);
            } else {
                this.resetForm();
            }
            
            this.showModal = true;
            
            // Initialize custom overtime charge after a short delay to ensure reservation is loaded
            setTimeout(() => {
                this.customOvertimeCharge = this.calculateOvertimeCharge();
            }, 100);
        },

        closeModal() {
            this.showModal = false;
            this.selectedRoom = null;
            this.reservation = null;
            this.errorMessage = '';
            this.isLoading = false;
            this.resetForm();
        },

        resetForm() {
            this.guests = [{ 
                document_type: 'dni', 
                custom_document_type: '',
                document_number: '', 
                first_name: '', 
                last_name: '', 
                gender: '', 
                vehicle_plate: '' 
            }];
            this.duration_hours = 2;
        },

        addGuest() {
            this.guests.push({ 
                document_type: 'dni', 
                custom_document_type: '',
                document_number: '', 
                first_name: '', 
                last_name: '', 
                gender: '', 
                vehicle_plate: '' 
            });
        },

        removeGuest(index) {
            if (this.guests.length > 1) {
                this.guests.splice(index, 1);
            }
        },

        async loadReservation(roomId) {
            const room = document.querySelector(`[data-room-id="${roomId}"]`);
            const reservationId = room?.dataset.reservationId;
            
            if (reservationId) {
                try {
                    const response = await fetch(`/reservations/${reservationId}`);
                    const data = await response.json();
                    this.reservation = data;
                    this.duration_hours = data.duration_hours;
                    this.guests = data.guests.map(g => ({
                        id: g.id,
                        document_type: g.document_type,
                        custom_document_type: g.custom_document_type || '',
                        document_number: g.document_number,
                        first_name: g.first_name,
                        last_name: g.last_name,
                        gender: g.gender || '',
                        vehicle_plate: g.vehicle_plate || ''
                    }));
                } catch (error) {
                    console.error('Error loading reservation:', error);
                }
            }
        },

        async submitForm() {
            this.isLoading = true;
            this.errorMessage = '';

            const formData = {
                room_id: this.selectedRoom,
                duration_hours: this.duration_hours,
                guests: this.guests
            };

            const url = this.isEditing 
                ? `/reservations/${this.reservation.id}` 
                : '/reservations';
            
            const method = this.isEditing ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.reload();
                } else {
                    this.errorMessage = data.message || 'Error al procesar la reserva';
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        this.errorMessage = firstError;
                    }
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                this.errorMessage = 'Ocurrió un error inesperado. Inténtalo de nuevo.';
            } finally {
                this.isLoading = false;
            }
        },

        async checkout() {
            if (!confirm('¿Estás seguro de realizar el check-out?')) return;

            try {
                const response = await fetch(`/reservations/${this.reservation.id}/checkout`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error during checkout:', error);
            }
        },

        async toggleCleaning() {
            if (this.isLoading) return;
            
            this.isLoading = true;
            try {
                const response = await fetch(`/rooms/${this.selectedRoom}/toggle-cleaning`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    const data = await response.json();
                    alert(data.message || 'Error al cambiar estado de limpieza');
                }
            } catch (error) {
                console.error('Error toggling cleaning:', error);
            } finally {
                this.isLoading = false;
            }
        },

        async updateRoomStatus() {
            try {
                const response = await fetch('/api/rooms/status');
                const rooms = await response.json();
                
                rooms.forEach(room => {
                    const roomElement = document.querySelector(`[data-room-id="${room.id}"]`);
                    if (roomElement) {
                        // Update status class
                        roomElement.classList.remove('status-available', 'status-occupied', 'status-expired', 'status-cleaning');
                        roomElement.classList.add(`status-${room.status}`);

                        // Update label if not occupied
                        const statusLabel = roomElement.querySelector('.status-label');
                        if (statusLabel) {
                            if (room.status === 'cleaning') statusLabel.textContent = 'Limpieza';
                            else if (room.status === 'expired') statusLabel.textContent = 'Ocupado (Vencido)';
                            else if (room.status === 'occupied') statusLabel.textContent = 'Ocupado';
                            else statusLabel.textContent = 'Disponible';
                        }

                        if (room.reservation) {
                            const timeElement = roomElement.querySelector('.remaining-time');
                            const progressBar = roomElement.querySelector('.progress-bar');
                            
                            if (timeElement) {
                                timeElement.textContent = room.reservation.remaining_time;
                            }
                            
                            if (progressBar) {
                                progressBar.style.width = room.reservation.progress + '%';
                            }
                            
                            // Ensure data-reservation-id exists
                            roomElement.setAttribute('data-reservation-id', room.reservation.id);
                        } else {
                            // If no reservation, remove the attribute to stop countdowns
                            roomElement.removeAttribute('data-reservation-id');
                        }
                    }
                });
            } catch (error) {
                console.error('Error updating room status:', error);
            }
        },

        updateCountdowns() {
            document.querySelectorAll('[data-reservation-id]').forEach(room => {
                const timeElement = room.querySelector('.remaining-time');
                const progressBar = room.querySelector('.progress-bar');
                const overtimeBadge = room.querySelector('.overtime-badge');
                
                if (timeElement && timeElement.dataset.checkoutAt) {
                    const checkoutAt = new Date(timeElement.dataset.checkoutAt);
                    const now = new Date();
                    const diff = checkoutAt - now;

                    if (diff <= 0) {
                        timeElement.textContent = 'Tiempo vencido';
                        if (progressBar) progressBar.style.width = '100%';
                        room.classList.remove('status-occupied', 'status-cleaning', 'status-available');
                        room.classList.add('status-expired');
                        
                        // Update overtime badge
                        if (overtimeBadge) {
                            const overtime = Math.abs(diff);
                            const hours = Math.floor(overtime / 3600000);
                            const minutes = Math.floor((overtime % 3600000) / 60000);
                            overtimeBadge.textContent = `+${hours}h ${minutes}m`;
                        }
                    } else {
                        const hours = Math.floor(diff / 3600000);
                        const minutes = Math.floor((diff % 3600000) / 60000);
                        const seconds = Math.floor((diff % 60000) / 1000);
                        timeElement.textContent = `${hours}h ${minutes}m ${seconds}s`;
                    }
                }
            });
        }
    }
}
</script>
@endpush
