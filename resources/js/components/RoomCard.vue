<template>
  <div 
    class="relative border-2 rounded-lg p-3 cursor-pointer transition-all duration-200"
    :class="statusClass"
    @click="$emit('open-reservation', room)"
  >
    <!-- Overtime Badge -->
    <div 
      v-if="hasActiveReservation"
      class="absolute top-1 left-1.5 px-1.5 py-0.5 bg-red-600 rounded-md text-[8px] font-black text-white shadow-md border border-red-700 tabular-nums overtime-badge transition-opacity duration-300"
      :class="isExpired ? 'opacity-100' : 'opacity-0'"
    >
      +{{ overtimeDisplay }}
    </div>

    <!-- Room Price Badge -->
    <div class="absolute top-1 right-1.5 px-1.5 py-0.5 bg-white/40 rounded-md text-[8px] font-black text-gray-400/80 shadow-sm border border-gray-100/50 tabular-nums">
      S/ {{ formatPrice(room.price) }}
    </div>

    <!-- Room Number & Type -->
    <div class="text-center mb-1">
      <span class="text-lg font-bold text-gray-800">{{ room.room_number }}</span>
      <div class="text-[9px] font-black text-blue-500 uppercase tracking-tighter">{{ room.type }}</div>
    </div>

    <template v-if="hasActiveReservation">
      <!-- Guest Name -->
      <div class="text-xs text-gray-700 font-medium mb-1 truncate text-center">
        {{ primaryGuestName }}
      </div>

      <!-- Vehicle Icon -->
      <div v-if="activeReservation.has_vehicle" class="flex justify-center mb-2">
        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
          <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
          <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z"/>
        </svg>
      </div>

      <!-- Progress Bar -->
      <div class="w-full bg-gray-200 rounded-full h-1.5 mb-1">
        <div 
          class="bg-blue-600 h-1.5 rounded-full transition-all duration-300" 
          :style="{ width: progressPercentage + '%' }"
        ></div>
      </div>

      <!-- Remaining Time -->
      <div class="remaining-time text-xs text-center text-gray-600 font-mono">
        {{ remainingTimeDisplay }}
      </div>
    </template>

    <template v-else>
      <!-- Status Label -->
      <div class="status-label text-xs text-center text-gray-600 font-medium capitalize">
        {{ statusLabel }}
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoomStore } from '@/stores/roomStore';

const props = defineProps({
  room: {
    type: Object,
    required: true
  }
});

defineEmits(['open-reservation']);

const roomStore = useRoomStore();

const activeReservation = computed(() => props.room.active_reservation);
const hasActiveReservation = computed(() => !!activeReservation.value);

const statusClass = computed(() => {
  const classes = {
    'available': 'status-available',
    'occupied': 'status-occupied',
    'expired': 'status-expired',
    'cleaning': 'status-cleaning',
  };
  return classes[props.room.status] || classes.available;
});

const isExpired = computed(() => props.room.status === 'expired');

const primaryGuestName = computed(() => {
  if (!activeReservation.value) return 'N/A';
  const guest = activeReservation.value.primary_guest;
  return guest ? `${guest.first_name} ${guest.last_name}` : 'N/A';
});

const progressPercentage = computed(() => {
  if (!activeReservation.value) return 0;
  const start = new Date(activeReservation.value.created_at).getTime();
  const end = new Date(activeReservation.value.check_out_at).getTime();
  const now = roomStore.currentTime.getTime();
  
  if (now >= end) return 100;
  if (now <= start) return 0;
  
  return ((now - start) / (end - start)) * 100;
});

const remainingTimeDisplay = computed(() => {
  if (!activeReservation.value) return '';
  const end = new Date(activeReservation.value.check_out_at).getTime();
  const now = roomStore.currentTime.getTime();
  const diff = end - now;

  if (diff <= 0) return 'Tiempo vencido';

  const hours = Math.floor(diff / 3600000);
  const minutes = Math.floor((diff % 3600000) / 60000);
  const seconds = Math.floor((diff % 60000) / 1000);
  
  return `${hours}h ${minutes}m ${seconds}s`;
});

const overtimeDisplay = computed(() => {
  if (!activeReservation.value) return '0h 0m';
  const end = new Date(activeReservation.value.check_out_at).getTime();
  const now = roomStore.currentTime.getTime();
  const diff = now - end;

  if (diff <= 0) return '0h 0m';

  const hours = Math.floor(diff / 3600000);
  const minutes = Math.floor((diff % 3600000) / 60000);
  return `${hours}h ${minutes}m`;
});

const statusLabel = computed(() => {
  const labels = {
    'available': 'Disponible',
    'occupied': 'Ocupado',
    'expired': 'Ocupado (Vencido)',
    'cleaning': 'En Limpieza',
  };
  return labels[props.room.status] || 'Disponible';
});

const formatPrice = (price) => {
  return parseFloat(price).toFixed(0);
};
</script>

<style scoped>
.status-available { background-color: #f0f9ff; border-color: #bae6fd; }
.status-available:hover { background-color: #e0f2fe; }

.status-occupied { background-color: #f0fdf4; border-color: #bbf7d0; }
.status-occupied:hover { background-color: #dcfce7; }

.status-expired { background-color: #fee2e2; border-color: #f87171; border-width: 3px; }
.status-expired:hover { background-color: #fecaca; }

.status-cleaning { background-color: #fefce8; border-color: #fef08a; }
.status-cleaning:hover { background-color: #fef9c3; }
</style>
