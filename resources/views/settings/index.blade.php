@extends('layouts.app')

@section('title', 'Configuración - Sistema de Gestión Hotelera')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Configuración del Hotel</h2>
        <p class="mt-2 text-sm text-gray-600">Administra las configuraciones generales de tu hotel</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-900">&times;</button>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Overtime Rate Configuration -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center mb-4">
            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-900">Tarifa de Tiempo Extra</h3>
        </div>
        
        <p class="text-sm text-gray-600 mb-6">
            Configura el costo por hora adicional cuando los huéspedes se quedan más tiempo del reservado. 
            Esta tarifa se aplicará automáticamente al hacer checkout de habitaciones vencidas.
        </p>

        <form action="{{ route('settings.update-overtime-rate') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="overtime_rate_per_hour" class="block text-sm font-medium text-gray-700 mb-2">
                    Tarifa por Hora Extra (S/)
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">S/</span>
                    <input 
                        type="number" 
                        step="0.01"
                        min="0"
                        max="9999.99"
                        name="overtime_rate_per_hour" 
                        id="overtime_rate_per_hour"
                        value="{{ old('overtime_rate_per_hour', $tenant->overtime_rate_per_hour ?? 0) }}"
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg font-semibold"
                        placeholder="0.00"
                        required
                    >
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    Ejemplo: Si configuras S/ 10.00, cada hora extra se cobrará S/ 10.00
                </p>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    <strong>Tarifa actual:</strong> 
                    <span class="text-lg font-bold text-blue-600">S/ {{ number_format($tenant->overtime_rate_per_hour ?? 0, 2) }}</span>
                </div>
                <button 
                    type="submit"
                    class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg"
                >
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <!-- Info Card -->
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-2">¿Cómo funciona el cobro de tiempo extra?</p>
                <ul class="list-disc list-inside space-y-1 text-blue-700">
                    <li>Cuando una reserva vence, el sistema comienza a contar el tiempo extra automáticamente</li>
                    <li>Al abrir una habitación vencida, verás el tiempo extra transcurrido y el monto calculado</li>
                    <li>Puedes aplicar el cobro de tiempo extra antes de hacer checkout</li>
                    <li>El cobro se sumará al total de la reserva y se reflejará en todos los reportes</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
