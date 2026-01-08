<template>
  <div 
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    @click.self="close()"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
          <span>{{ isEditing ? 'Editar Reserva' : 'Nueva Reserva' }}</span>
          <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full" v-if="room">{{ room.type }}</span>
          <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-bold" v-if="room">S/ {{ formatPrice(room.price) }}</span>
          <!-- Overtime Badge for Expired Rooms -->
          <span v-if="room && room.status === 'expired' && reservation" 
                class="text-xs px-3 py-1 bg-red-600 text-white rounded-full font-black shadow-md animate-pulse">
            ⚠️ Tiempo extra: {{ overtimeDisplay }}
          </span>
        </h3>
        <button @click="close()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="p-6">
        <form @submit.prevent="submitForm">
          <!-- Error Message -->
          <div v-if="errorMessage" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between">
            <span>{{ errorMessage }}</span>
            <button type="button" @click="errorMessage = ''" class="text-red-600 hover:text-red-900">&times;</button>
          </div>

          <!-- Overtime Charge Section (for expired reservations) -->
          <div v-if="room && room.status === 'expired' && isEditing && reservation">
            <div v-if="!hasOvertimeCharge" class="mb-6 bg-red-50 border-2 border-red-300 rounded-xl p-5">
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-2">
                  <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <h4 class="text-lg font-bold text-red-900">Tiempo Extra Detectado</h4>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-white rounded-lg p-3">
                  <p class="text-xs text-gray-600 mb-1">Tiempo Extra</p>
                  <p class="text-2xl font-black text-red-600">{{ overtimeDisplay }}</p>
                </div>
                <div class="bg-white rounded-lg p-3">
                  <p class="text-xs text-gray-600 mb-1 flex items-center justify-between">
                    <span>Cobro a Aplicar</span>
                    <button type="button" @click.stop="editingOvertimeCharge = !editingOvertimeCharge" class="text-blue-600 hover:text-blue-800">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                      </svg>
                    </button>
                  </p>
                  <div v-if="!editingOvertimeCharge">
                    <p class="text-2xl font-black text-red-600">S/ {{ formatPrice(customOvertimeCharge) }}</p>
                    <p class="text-[10px] text-gray-500 mt-1" v-if="isOvertimeEdited">
                      (Editado manualmente)
                    </p>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                      Tarifa: S/ {{ formatPrice(roomStore.overtimeRate) }} / hora
                    </p>
                  </div>
                  <div v-else class="relative">
                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm">S/</span>
                    <input 
                      type="number" 
                      step="0.01" 
                      min="0"
                      v-model.number="customOvertimeCharge"
                      @blur="editingOvertimeCharge = false"
                      class="w-full pl-8 pr-2 py-1 border border-blue-300 rounded text-xl font-black text-red-600 focus:ring-2 focus:ring-blue-500"
                    >
                  </div>
                </div>
              </div>

              <button 
                type="button"
                @click="applyOvertime"
                :disabled="isLoading"
                class="w-full px-4 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ isLoading ? 'Aplicando...' : 'Aplicar Cobro de Tiempo Extra' }}</span>
              </button>
            </div>

            <!-- Overtime Already Applied Notice -->
            <div v-if="hasOvertimeCharge" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
              <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                  <p class="text-sm font-semibold text-green-900">Cobro de tiempo extra ya aplicado</p>
                  <p class="text-xs text-green-700">
                    {{ reservation.overtime_hours }} horas - 
                    <strong>S/ {{ formatPrice(reservation.overtime_charge) }}</strong>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Duration -->
          <div class="mb-6" :class="room && room.status === 'cleaning' ? 'opacity-50 pointer-events-none' : ''">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Duración de estadía (horas)
            </label>
            <input 
              type="number" 
              v-model="form.duration_hours" 
              min="1" 
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
          </div>

          <!-- Guests -->
          <div class="mb-6" :class="room && room.status === 'cleaning' ? 'opacity-50 pointer-events-none' : ''">
            <div class="flex items-center justify-between mb-4">
              <label class="block text-sm font-medium text-gray-700">
                Huéspedes
              </label>
              <button 
                type="button"
                @click="addGuest"
                class="text-sm text-blue-600 hover:text-blue-800 font-medium"
              >
                + Agregar otro huésped
              </button>
            </div>

            <div v-for="(guest, index) in form.guests" :key="index" class="border border-gray-200 rounded-lg p-4 mb-4">
              <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-700">
                  Huésped {{ index + 1 }}
                  <span v-if="index === 0" class="text-xs text-blue-600">(Principal)</span>
                </span>
                <button 
                  v-if="form.guests.length > 1"
                  type="button"
                  @click="removeGuest(index)"
                  class="text-red-600 hover:text-red-800 text-sm"
                >
                  Eliminar
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Document Type -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Tipo de documento *
                  </label>
                  <select 
                    v-model="guest.document_type"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="dni">DNI</option>
                    <option value="carnet_extranjeria">Carnet de Extranjería</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>

                <!-- Custom Document Type -->
                <div v-if="guest.document_type === 'otro'">
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Especificar tipo *
                  </label>
                  <input 
                    type="text"
                    v-model="guest.custom_document_type"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                </div>

                <!-- Document Number -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Número de documento *
                  </label>
                  <input 
                    type="text"
                    v-model="guest.document_number"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                </div>

                <!-- First Name -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Nombres *
                  </label>
                  <input 
                    type="text"
                    v-model="guest.first_name"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                </div>

                <!-- Last Name -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Primer apellido *
                  </label>
                  <input 
                    type="text"
                    v-model="guest.last_name"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                </div>

                <!-- Gender -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Sexo (opcional)
                  </label>
                  <select 
                    v-model="guest.gender"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="">Seleccionar</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>

                <!-- Vehicle Plate -->
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Placa de vehículo (opcional)
                  </label>
                  <input 
                    type="text"
                    v-model="guest.vehicle_plate"
                    placeholder="Ej: ABC-123"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
            <button 
              type="button"
              @click="close()"
              class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors"
            >
              Cancelar
            </button>

            <!-- Cleaning Toggle Button -->
            <button 
              type="button"
              v-if="room && (!isEditing || room.status === 'expired')"
              @click="toggleCleaning"
              :disabled="isLoading"
              class="px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
              :class="room.status === 'cleaning' ? 'btn-available' : 'btn-cleaning'"
            >
              <span>{{ room.status === 'cleaning' ? 'Terminar Limpieza' : 'Poner en Limpieza' }}</span>
            </button>

            <button 
              v-if="isEditing"
              type="button"
              @click="checkout"
              :disabled="isLoading"
              class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg font-medium transition-colors"
            >
              Check-out
            </button>

            <button 
              type="submit"
              v-if="room && room.status !== 'cleaning'"
              :disabled="isLoading"
              class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed min-w-[100px]"
            >
              <span>{{ isLoading ? 'Procesando...' : (isEditing ? 'Actualizar' : 'Guardar') }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useRoomStore } from '@/stores/roomStore';
import axios from 'axios';

const props = defineProps({
  show: Boolean,
  room: Object
});

const emit = defineEmits(['close', 'updated']);

const roomStore = useRoomStore();

const isLoading = ref(false);
const errorMessage = ref('');
const isEditing = ref(false);
const reservation = ref(null);
const editingOvertimeCharge = ref(false);
const customOvertimeCharge = ref(0);

const form = ref({
  duration_hours: 2,
  guests: [
    {
      document_type: 'dni',
      custom_document_type: '',
      document_number: '',
      first_name: '',
      last_name: '',
      gender: '',
      vehicle_plate: ''
    }
  ]
});

// Watch for room changes to load reservation data or reset form
watch(() => props.room, (newRoom) => {
  if (newRoom) {
    if (newRoom.active_reservation) {
      isEditing.value = true;
      loadReservation(newRoom.active_reservation.id);
    } else {
      isEditing.value = false;
      reservation.value = null;
      resetForm();
    }
  }
}, { immediate: true });

const resetForm = () => {
  form.value = {
    duration_hours: 2,
    guests: [
      {
        document_type: 'dni',
        custom_document_type: '',
        document_number: '',
        first_name: '',
        last_name: '',
        gender: '',
        vehicle_plate: ''
      }
    ]
  };
  errorMessage.value = '';
};

const loadReservation = async (id) => {
  isLoading.value = true;
  try {
    const response = await axios.get(`/reservations/${id}`);
    reservation.value = response.data;
    form.value.duration_hours = response.data.duration_hours;
    form.value.guests = response.data.guests.map(g => ({
      ...g,
      custom_document_type: g.custom_document_type || '',
      gender: g.gender || '',
      vehicle_plate: g.vehicle_plate || ''
    }));
    
    calculateInitialOvertimeCharge();
  } catch (error) {
    console.error('Error loading reservation:', error);
  } finally {
    isLoading.value = false;
  }
};

const addGuest = () => {
  form.value.guests.push({
    document_type: 'dni',
    custom_document_type: '',
    document_number: '',
    first_name: '',
    last_name: '',
    gender: '',
    vehicle_plate: ''
  });
};

const removeGuest = (index) => {
  form.value.guests.splice(index, 1);
};

const close = () => {
  emit('close');
};

const submitForm = async () => {
  isLoading.value = true;
  errorMessage.value = '';

  const url = isEditing.value 
    ? `/reservations/${reservation.value.id}` 
    : '/reservations';
  
  const method = isEditing.value ? 'put' : 'post';
  const payload = {
    ...form.value,
    room_id: props.room.id
  };

  try {
    await axios({ method, url, data: payload });
    roomStore.fetchDashboardData();
    close();
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Error al procesar la reserva';
    if (error.response?.data?.errors) {
      const firstError = Object.values(error.response.data.errors)[0][0];
      errorMessage.value = firstError;
    }
  } finally {
    isLoading.value = false;
  }
};

const checkout = async () => {
  if (!confirm('¿Estás seguro de realizar el check-out?')) return;
  isLoading.value = true;
  try {
    await axios.post(`/reservations/${reservation.value.id}/checkout`);
    roomStore.fetchDashboardData();
    close();
  } catch (error) {
    console.error('Error during checkout:', error);
  } finally {
    isLoading.value = false;
  }
};

const toggleCleaning = async () => {
  isLoading.value = true;
  try {
    await axios.post(`/rooms/${props.room.id}/toggle-cleaning`);
    roomStore.fetchDashboardData();
    close();
  } catch (error) {
    console.error('Error toggling cleaning:', error);
  } finally {
    isLoading.value = false;
  }
};

// Overtime Logic
const hasOvertimeCharge = computed(() => reservation.value?.overtime_charge > 0);

const overtimeDisplay = computed(() => {
  if (!reservation.value) return '0h 0m';
  const end = new Date(reservation.value.check_out_at).getTime();
  const now = new Date().getTime();
  const diff = now - end;
  if (diff <= 0) return '0h 0m';
  const hours = Math.floor(diff / 3600000);
  const minutes = Math.floor((diff % 3600000) / 60000);
  return `${hours}h ${minutes}m`;
});

const calculateInitialOvertimeCharge = () => {
  if (!reservation.value) return;
  const end = new Date(reservation.value.check_out_at).getTime();
  const now = new Date().getTime();
  const diff = now - end;
  if (diff <= 0) {
    customOvertimeCharge.value = 0;
    return;
  }
  const overtimeHours = diff / 3600000;
  customOvertimeCharge.value = Math.round((overtimeHours * roomStore.overtimeRate) * 100) / 100;
};

const isOvertimeEdited = computed(() => {
  if (!reservation.value) return false;
  const end = new Date(reservation.value.check_out_at).getTime();
  const now = new Date().getTime();
  const diff = now - end;
  const calculated = Math.max(0, (diff / 3600000) * roomStore.overtimeRate);
  return Math.abs(customOvertimeCharge.value - calculated) > 0.01;
});

const applyOvertime = async () => {
  if (!confirm('¿Estás seguro de aplicar el cobro de tiempo extra?')) return;
  isLoading.value = true;
  try {
    const response = await axios.post(`/reservations/${reservation.value.id}/apply-overtime`, {
      custom_charge: customOvertimeCharge.value
    });
    reservation.value.overtime_charge = response.data.overtime_charge;
    reservation.value.overtime_hours = response.data.overtime_hours;
    reservation.value.total_price = response.data.new_total;
    alert('Cobro aplicado correctamente');
  } catch (error) {
    errorMessage.value = 'Error al aplicar el cobro de tiempo extra';
  } finally {
    isLoading.value = false;
  }
};

const formatPrice = (price) => parseFloat(price).toFixed(2);
</script>

<style scoped>
.btn-cleaning { background-color: #eab308; color: white; }
.btn-cleaning:hover { background-color: #ca8a04; }
.btn-available { background-color: #16a34a; color: white; }
.btn-available:hover { background-color: #15803d; }
</style>
