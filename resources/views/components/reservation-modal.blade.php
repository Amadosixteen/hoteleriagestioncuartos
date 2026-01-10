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
                <div class="flex items-center gap-2">
                    <div class="flex flex-col items-start">
                        <span class="text-[10px] text-gray-500 font-medium" x-show="showCustomPriceInput || (isEditing && parseFloat(selectedRoomPrice) !== parseFloat(reservation?.price))">Precio Estándar</span>
                        <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-bold" 
                              :class="(showCustomPriceInput || (isEditing && parseFloat(selectedRoomPrice) !== parseFloat(reservation?.price))) ? 'line-through opacity-50' : ''"
                              x-show="selectedRoomPrice" 
                              x-text="'S/ ' + parseFloat(selectedRoomPrice).toFixed(2)"></span>
                    </div>

                    <div x-show="showCustomPriceInput || (isEditing && parseFloat(selectedRoomPrice) !== parseFloat(reservation?.price))" class="flex flex-col items-start border-l pl-2 border-gray-200">
                        <span class="text-[10px] text-green-700 font-bold">Nuevo Precio</span>
                        <div x-show="!isEditing || showCustomPriceInput" class="relative">
                            <span class="absolute left-2 top-1/2 -translate-y-1/2 text-green-700 text-xs font-bold">S/</span>
                            <input type="number" 
                                   step="0.01" 
                                   x-model="customPrice" 
                                   @input="if(isEditing && reservation) reservation.price = customPrice"
                                   class="w-24 pl-6 pr-2 py-0.5 border border-green-300 rounded-full text-xs font-bold text-green-700 focus:ring-2 focus:ring-green-500"
                                   placeholder="0.00">
                        </div>
                        <span x-show="isEditing && !showCustomPriceInput" 
                              class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full font-black"
                              x-text="'S/ ' + parseFloat(reservation?.price || 0).toFixed(2)"></span>
                    </div>

                    <button type="button" 
                            x-show="!showCustomPriceInput"
                            @click="showCustomPriceInput = true; customPrice = (isEditing && reservation ? reservation.price : selectedRoomPrice)"
                            class="text-[10px] text-blue-600 hover:text-blue-800 underline font-medium">
                        ✏️ (Otro precio)
                    </button>
                    <button type="button" 
                            x-show="showCustomPriceInput"
                            @click="showCustomPriceInput = false; if(!isEditing) customPrice = null"
                            class="text-[10px] text-red-600 hover:text-red-800 underline font-medium">
                        Cancelar
                    </button>
                </div>
                <!-- Overtime Badge for Expired Rooms -->
                <span x-show="(currentRoomStatus === 'expired' || isTimeExpired()) && reservation" 
                      class="text-xs px-3 py-1 bg-red-600 text-white rounded-full font-black shadow-md animate-pulse"
                      x-text="'⚠️ Tiempo extra: ' + getOvertimeDisplay()">
                </span>
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

                <!-- Overtime Charge Section (for expired reservations) -->
                <!-- Overtime Charge Section (for expired reservations) -->
                <template x-if="(currentRoomStatus === 'expired' || isTimeExpired()) && isEditing">
                    <div>
                        <div x-show="!hasOvertimeCharge()" class="mb-6 bg-red-50 border-2 border-red-300 rounded-xl p-5">
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
                                    <p class="text-2xl font-black text-red-600" x-text="getOvertimeDisplay()">0h 0m</p>
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
                                    <div x-show="!editingOvertimeCharge">
                                        <p class="text-2xl font-black text-red-600" x-text="formatMoney(customOvertimeCharge)">S/ 0.00</p>
                                        <p class="text-[10px] text-gray-500 mt-1" x-show="isOvertimeEdited()">
                                            (Editado manualmente)
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-0.5">
                                            Tarifa: <span x-text="formatMoney(window.OVERTIME_RATE || 0)">S/ 0.00</span> / hora
                                        </p>
                                    </div>
                                    <div x-show="editingOvertimeCharge" class="relative">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-sm">S/</span>
                                        <input 
                                            type="number" 
                                            step="0.01" 
                                            min="0"
                                            x-model.number="customOvertimeCharge"
                                            :placeholder="calculateOvertimeCharge().toFixed(2)"
                                            @blur="editingOvertimeCharge = false"
                                            @click.stop
                                            class="w-full pl-8 pr-2 py-1 border border-blue-300 rounded text-xl font-black text-red-600 focus:ring-2 focus:ring-blue-500"
                                        >
                                    </div>
                                </div>
                            </div>

                            <button 
                                type="button"
                                @click="applyOvertimeCharge()"
                                :disabled="isLoading"
                                class="w-full px-4 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span x-text="isLoading ? 'Aplicando...' : 'Aplicar Cobro de Tiempo Extra'">Aplicar Cobro de Tiempo Extra</span>
                            </button>
                        </div>

                        <!-- Overtime Already Applied Notice -->
                        <div x-show="hasOvertimeCharge()" class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-semibold text-green-900">Cobro de tiempo extra ya aplicado</p>
                                    <p class="text-xs text-green-700">
                                        <span x-text="getOvertimeHours()">0</span> horas - 
                                        <strong x-text="formatMoney(getOvertimeChargeAmount())">S/ 0.00</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

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
                        x-show="!isEditing || currentRoomStatus === 'expired' || isTimeExpired()"
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
