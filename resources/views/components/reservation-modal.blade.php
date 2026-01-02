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
            <h3 class="text-xl font-semibold text-gray-900">
                <span x-text="isEditing ? 'Editar Reserva' : 'Nueva Reserva'"></span>
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
                <!-- Duration -->
                <div class="mb-6">
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
                <div class="mb-6">
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
                        class="px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition-colors"
                    >
                        <span x-text="isEditing ? 'Actualizar' : 'Guardar'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
