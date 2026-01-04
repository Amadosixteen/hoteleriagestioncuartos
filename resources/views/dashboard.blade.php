@extends('layouts.app')

@section('title', 'Dashboard - Gestión de Cuartos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="dashboardApp()">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">Gestión de Cuartos</h2>

    @foreach($floors as $floor)
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $floor->name }}</h3>
        
        <!-- Grid de cuartos (10 columnas) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-10 gap-3">
            @foreach($floor->rooms as $room)
                <x-room-card :room="$room" />
            @endforeach
        </div>
    </div>
    @endforeach

    <!-- Modal de Reserva -->
    <x-reservation-modal />
</div>

<style>
    .status-available { background-color: #f0f9ff !important; border-color: #bae6fd !important; }
    .status-available:hover { background-color: #e0f2fe !important; }
    
    .status-occupied { background-color: #f0fdf4 !important; border-color: #bbf7d0 !important; }
    .status-occupied:hover { background-color: #dcfce7 !important; }
    
    .status-expired { background-color: #fef2f2 !important; border-color: #fecaca !important; }
    .status-expired:hover { background-color: #fee2e2 !important; }
    
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

        init() {
            // Update room status every 30 seconds
            setInterval(() => this.updateRoomStatus(), 30000);
            
            // Update countdown every second
            setInterval(() => this.updateCountdowns(), 1000);

            // Listen for room click events
            window.addEventListener('open-reservation-modal', (event) => {
                this.openModal(event.detail.roomId, event.detail.hasReservation, event.detail.status);
            });
        },

        openModal(roomId, hasReservation = false, status = 'available') {
            this.selectedRoom = roomId;
            this.isEditing = hasReservation;
            this.currentRoomStatus = status;
            
            if (hasReservation) {
                this.loadReservation(roomId);
            } else {
                this.resetForm();
            }
            
            this.showModal = true;
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
                
                if (timeElement && timeElement.dataset.checkoutAt) {
                    const checkoutAt = new Date(timeElement.dataset.checkoutAt);
                    const now = new Date();
                    const diff = checkoutAt - now;

                    if (diff <= 0) {
                        timeElement.textContent = 'Tiempo vencido';
                        if (progressBar) progressBar.style.width = '100%';
                        room.classList.remove('status-occupied', 'status-cleaning', 'status-available');
                        room.classList.add('status-expired');
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
