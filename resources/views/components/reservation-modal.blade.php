<!-- Modal Overlay -->
<div 
    x-show="showModal" 
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
    @click.self="closeModal()"
>
    <!-- Modal Content -->
    <div 
        class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
    >
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                <span x-text="isEditing ? 'Editar Reserva' : 'Nueva Reserva'"></span>
                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full" x-show="selectedRoomType" x-text="selectedRoomType"></span>
                <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-bold" x-show="selectedRoomPrice" x-text="'S/ ' + parseFloat(selectedRoomPrice).toFixed(2)"></span>
            </h3>
            <button @click="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form @submit.prevent="submitForm()">
                <!-- Error Message -->
                <div x-show="errorMessage" class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm flex items-center justify-between">
                    <span x-text="errorMessage"></span>
                    <button type="button" @click="errorMessage = ''" class="text-red-600 hover:text-red-900">&times;</button>
                </div>

                <!-- Duration -->
                <div class="mb-6" :class="currentRoomStatus === 'cleaning' ? 'opacity-50 pointer-events-none' : ''">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Duración de estadía (horas)
                    </label>
                    <input 
                        type="number" 
                        x-model="duration_hours" 
                        min="1" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Guests -->
                <div class="mb-6" :class="currentRoomStatus === 'cleaning' ? 'opacity-50 pointer-events-none' : ''">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Huéspedes
                        </label>
                        <button 
                            type="button"
                            @click="addGuest()"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                        >
                            + Agregar otro huésped
                        </button>
                    </div>

                    <template x-for="(guest, index) in guests" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700">
                                    Huésped <span x-text="index + 1"></span>
                                    <span x-show="index === 0" class="text-xs text-blue-600">(Principal)</span>
                                </span>
                                <button 
                                    x-show="guests.length > 1"
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
                                        x-model="guest.document_type"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="dni">DNI</option>
                                        <option value="carnet_extranjeria">Carnet de Extranjería</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>

                                <!-- Custom Document Type (if "otro" selected) -->
                                <div x-show="guest.document_type === 'otro'">
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Especificar tipo de documento *
                                    </label>
                                    <input 
                                        type="text"
                                        x-model="guest.custom_document_type"
                                        :required="guest.document_type === 'otro'"
                                        placeholder="Ej: Pasaporte"
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
                                        x-model="guest.document_number"
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
                                        x-model="guest.first_name"
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
                                        x-model="guest.last_name"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <!-- Gender (Optional) -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Sexo (opcional)
                                    </label>
                                    <select 
                                        x-model="guest.gender"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Seleccionar</option>
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>

                                <!-- Vehicle Plate (Optional) -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">
                                        Placa de vehículo (opcional)
                                    </label>
                                    <input 
                                        type="text"
                                        x-model="guest.vehicle_plate"
                                        placeholder="Ej: ABC-123"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button 
                        type="button"
                        @click="closeModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors"
                    >
                        Cancelar
                    </button>

                    <!-- Cleaning Toggle Button -->
                    <button 
                        type="button"
                        x-show="!isEditing || currentRoomStatus === 'expired'"
                        @click="toggleCleaning()"
                        :disabled="isLoading"
                        class="px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                        :class="currentRoomStatus === 'cleaning' ? 'btn-available' : 'btn-cleaning'"
                    >
                        <span x-text="currentRoomStatus === 'cleaning' ? 'Terminar Limpieza' : 'Poner en Limpieza'"></span>
                    </button>


                    <button 
                        x-show="isEditing"
                        type="button"
                        @click="checkout()"
                        class="px-4 py-2 text-white bg-red-600 hover:bg-red-700 rounded-lg font-medium transition-colors"
                    >
                        Check-out
                    </button>

                    <button 
                        type="submit"
                        x-show="currentRoomStatus !== 'cleaning'"
                        :disabled="isLoading"
                        class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed min-w-[100px]"
                    >
                        <span x-show="!isLoading" x-text="isEditing ? 'Actualizar' : 'Guardar'"></span>
                        <span x-show="isLoading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Procesando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
