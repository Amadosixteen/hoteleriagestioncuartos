@props(['room'])

@php
    $reservation = $room->activeReservation;
    $hasReservation = $reservation !== null;
    $primaryGuest = $hasReservation ? $reservation->primaryGuest : null;
    
    // Determine status color
    $statusClasses = [
        'available' => 'status-available',
        'occupied' => 'status-occupied',
        'expired' => 'status-expired',
        'cleaning' => 'status-cleaning',
    ];
    
    $colorClass = $statusClasses[$room->status] ?? $statusClasses['available'];
@endphp

<div 
    class="relative border-2 rounded-lg p-3 cursor-pointer transition-all duration-200 {{ $colorClass }}"
    data-room-id="{{ $room->id }}"
    @if($hasReservation) 
        data-reservation-id="{{ $reservation->id }}"
    @endif
    @click="window.dispatchEvent(new CustomEvent('open-reservation-modal', { detail: { roomId: {{ $room->id }}, roomType: '{{ $room->type }}', roomPrice: {{ $room->price }}, hasReservation: {{ $hasReservation ? 'true' : 'false' }}, status: '{{ $room->status }}' } }))"
>
    <!-- Overtime Badge (for occupied/expired rooms) -->
    @if($hasReservation)
    <div class="absolute top-1 left-1.5 px-1.5 py-0.5 bg-red-600 rounded-md text-[8px] font-black text-white shadow-md border border-red-700 tabular-nums overtime-badge transition-opacity duration-300 {{ $room->status === 'expired' ? 'opacity-100' : 'opacity-0' }}"
         data-checkout-at="{{ $reservation->check_out_at->toIso8601String() }}">
        +0h 0m
    </div>
    @endif

    <!-- Room Price Badge -->
    <div class="absolute top-1 right-1.5 px-1.5 py-0.5 bg-white/40 rounded-md text-[8px] font-black text-gray-400/80 shadow-sm border border-gray-100/50 tabular-nums">
        S/ {{ number_format($room->price, 0) }}
    </div>

    <!-- Room Number & Type -->
    <div class="text-center mb-1">
        <span class="text-lg font-bold text-gray-800">{{ $room->room_number }}</span>
        <div class="text-[9px] font-black text-blue-500 uppercase tracking-tighter">{{ $room->type }}</div>
    </div>

    @if($hasReservation)
        <!-- Guest Name -->
        <div class="text-xs text-gray-700 font-medium mb-1 truncate text-center">
            {{ $primaryGuest ? $primaryGuest->full_name : 'N/A' }}
        </div>

        <!-- Vehicle Icon -->
        @if($reservation->has_vehicle)
        <div class="flex justify-center mb-2">
            <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
            </svg>
        </div>
        @endif

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 rounded-full h-1.5 mb-1">
            <div 
                class="progress-bar bg-blue-600 h-1.5 rounded-full transition-all duration-300" 
                style="width: {{ $reservation->getProgressPercentage() }}%"
            ></div>
        </div>

        <!-- Remaining Time -->
        <div 
            class="remaining-time text-xs text-center text-gray-600 font-mono"
            data-checkout-at="{{ $reservation->check_out_at->toIso8601String() }}"
        >
            {{ $reservation->getFormattedRemainingTime() }}
        </div>
    @else
        <!-- Available/Cleaning/Expired Status -->
        <div class="status-label text-xs text-center text-gray-600 font-medium">
            {{ $room->status_label }}
        </div>
    @endif
</div>
