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

        init() {
            // Update room status every 30 seconds
            setInterval(() => this.updateRoomStatus(), 30000);
            
            // Update countdown every second
            setInterval(() => this.updateCountdowns(), 1000);

            // Listen for room click events
            window.addEventListener('open-reservation-modal', (event) => {
                this.openModal(event.detail.roomId, event.detail.hasReservation);
            });
        },

        openModal(roomId, hasReservation = false) {
            this.selectedRoom = roomId;
            this.isEditing = hasReservation;
            
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error submitting form:', error);
            }
        },

        async checkout() {
            if (!confirm('¿Estás seguro de realizar el check-out?')) return;

            try {
                const response = await fetch(`/reservations/${this.reservation.id}/checkout`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error during checkout:', error);
            }
        },

        async updateRoomStatus() {
            try {
                const response = await fetch('/api/rooms/status');
                const rooms = await response.json();
                
                rooms.forEach(room => {
                    const roomElement = document.querySelector(`[data-room-id="${room.id}"]`);
                    if (roomElement && room.reservation) {
                        const timeElement = roomElement.querySelector('.remaining-time');
                        const progressBar = roomElement.querySelector('.progress-bar');
                        
                        if (timeElement) {
                            timeElement.textContent = room.reservation.remaining_time;
                        }
                        
                        if (progressBar) {
                            progressBar.style.width = room.reservation.progress + '%';
                        }

                        // Update status class
                        roomElement.className = roomElement.className.replace(
                            /bg-(sky|green|red)-\d+/g, 
                            room.status === 'expired' ? 'bg-red-100' : 'bg-green-100'
                        );
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
                        room.className = room.className.replace(/bg-green-\d+/, 'bg-red-100');
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
