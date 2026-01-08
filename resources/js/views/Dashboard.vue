<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
      <h2 class="text-2xl font-bold text-gray-900">Gestión de Cuartos (Vue)</h2>
      
      <!-- Filtros -->
      <div class="flex flex-wrap items-center gap-4">
        <!-- Filtro de Tipo -->
        <div class="flex flex-wrap gap-2 bg-white p-1 rounded-xl border border-gray-100 shadow-sm h-fit">
          <button 
            @click="roomStore.setFilterType('all')" 
            :class="roomStore.filterType === 'all' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
            class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-wider"
          >
            Todos
          </button>
          <button 
            v-for="type in ['Solo', 'Doble', 'Triple', 'Matrimonial', 'Familiar']" 
            :key="type"
            @click="roomStore.setFilterType(type)" 
            :class="roomStore.filterType === type ? 'bg-blue-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
            class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-wider"
          >
            {{ type }}
          </button>
        </div>

        <!-- Filtro de Precio -->
        <div class="flex items-center gap-2 bg-white p-1 rounded-xl border border-gray-100 shadow-sm h-fit">
          <button 
            @click="roomStore.toggleSortByPrice()"
            :class="roomStore.sortByPrice === 'desc' ? 'bg-green-600 text-white shadow-md' : 'text-gray-500 hover:bg-gray-50'"
            class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-wider flex items-center gap-2"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
            <span>Mayor a Menor</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Si NO hay orden por precio, mostramos por pisos -->
    <div v-if="!roomStore.sortByPrice">
      <div v-for="floor in roomStore.floors" :key="floor.id" class="mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ floor.name }}</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-10 gap-3">
          <RoomCard 
            v-for="room in floor.rooms" 
            :key="room.id" 
            :room="room"
            v-show="roomStore.filterType === 'all' || roomStore.filterType === room.type"
            @open-reservation="handleOpenReservation"
          />
        </div>
      </div>
    </div>

    <!-- Si HAY orden por precio, mostramos todo junto ordenado -->
    <div v-else class="mb-8">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">Habitaciones (Ordenadas por Precio)</h3>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 lg:grid-cols-10 gap-3">
        <RoomCard 
          v-for="room in roomStore.filteredRooms" 
          :key="room.id" 
          :room="room"
          @open-reservation="handleOpenReservation"
        />
      </div>
    </div>

    <!-- Dashboard loading -->
    <div v-if="roomStore.isLoading" class="fixed inset-0 bg-white bg-opacity-50 flex items-center justify-center z-50">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <!-- Modal de Reserva -->
    <ReservationModal 
      :show="showModal" 
      :room="selectedRoom" 
      @close="showModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoomStore } from '@/stores/roomStore';
import RoomCard from '@/components/RoomCard.vue';
import ReservationModal from '@/components/ReservationModal.vue';

const roomStore = useRoomStore();
const showModal = ref(false);
const selectedRoom = ref(null);

onMounted(() => {
  roomStore.fetchDashboardData();
  
  // Update current time every second for countdowns
  const timerInterval = setInterval(() => {
    roomStore.tick();
  }, 1000);

  // Real-time status updates every 30 seconds
  const statusInterval = setInterval(() => {
    roomStore.updateRoomStatus();
  }, 30000);

  onUnmounted(() => {
    clearInterval(timerInterval);
    clearInterval(statusInterval);
  });
});

const handleOpenReservation = (room) => {
  selectedRoom.value = room;
  showModal.value = true;
};
</script>
