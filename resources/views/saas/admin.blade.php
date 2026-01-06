@extends('layouts.app')

@section('title', 'Administración SaaS - Hotelería')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ showHotelModal: false, showSellerModal: false }">
    
    <!-- Botones Superiores -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900">Gestión SaaS</h2>
            <p class="text-gray-500">Administra hoteles, suscripciones y vendedores.</p>
        </div>
        <div class="flex space-x-3">
            <button @click="showSellerModal = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-all font-bold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Nuevo Vendedor
            </button>
            <button @click="showHotelModal = true" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-200 font-bold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Registrar Hotel
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <!-- Tabla de Hoteles -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100 mb-12">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-700">
            <h3 class="text-xl font-bold text-white">Hoteles y Suscripciones</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario / Hotel</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vendedor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vencimiento</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr class="{{ $user->isSuperAdmin() ? 'bg-blue-50/50' : '' }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-500 flex items-center gap-1.5">
                                <span>{{ $user->email }}</span>
                                @if($user->tenant && $user->tenant->phone)
                                    <span class="text-gray-300">•</span>
                                    <span class="text-indigo-500 font-medium">{{ $user->tenant->phone }}</span>
                                @endif
                                @if($user->tenant && $user->tenant->location)
                                    <span class="text-gray-300">•</span>
                                    <a href="{{ $user->tenant->location }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 transition-colors" title="Ver ubicación en el mapa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </a>
                                @endif
                            </div>
                            <div class="mt-1">
                                <span class="text-[10px] text-indigo-600 font-bold uppercase tracking-tight">{{ $user->tenant ? $user->tenant->name : 'Sin Hotel' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 italic">
                            @if($user->isSuperAdmin())
                                <span class="text-indigo-600 font-bold not-italic">Dueño del Software</span>
                            @else
                                {{ $user->tenant && $user->tenant->seller ? $user->tenant->seller->full_name : 'Directo' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->isSuperAdmin())
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-indigo-600 text-white uppercase shadow-sm">SISTEMA</span>
                            @else
                                @switch($user->status_label)
                                    @case('Activo')
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Activo</span>
                                        @break
                                    @case('Vencido')
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-orange-100 text-orange-700 uppercase">Vencido</span>
                                        @break
                                    @case('Baneado')
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 uppercase">Baneado</span>
                                        @break
                                @endswitch
                                <div class="mt-1">
                                    <span class="text-[9px] font-bold uppercase tracking-wider {{ $user->subscription_type === 'trial' ? 'text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded' : 'text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded' }}">
                                        {{ $user->subscription_type === 'trial' ? 'Prueba' : 'Mensual' }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->isSuperAdmin())
                                <div class="text-sm font-bold text-indigo-600">Ilimitado</div>
                                <div class="text-[10px] text-indigo-400 uppercase tracking-tighter">Acceso Perpetuo</div>
                            @else
                                <div class="text-sm {{ ($user->days_remaining <= 5 && $user->hasActiveSubscription()) ? 'text-red-600 font-bold' : ($user->status_label === 'Vencido' ? 'text-orange-600 font-medium' : 'text-gray-900') }}">
                                    {{ $user->subscription_expires_at ? $user->subscription_expires_at->format('d/m/Y') : 'N/A' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    @if($user->status_label === 'Vencido')
                                        <span class="text-orange-600 font-bold">Expirado</span>
                                    @elseif($user->days_remaining > 0)
                                        Quedan {{ $user->days_remaining }} días
                                    @else
                                        -
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if(!$user->isSuperAdmin())
                            <form action="{{ route('saas.renew', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm font-bold text-xs uppercase tracking-widest">
                                    +30 Días
                                </button>
                            </form>
                            @else
                            <span class="text-gray-400 italic text-xs">Dueño del SaaS</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Analítica de Vendedores -->
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-900">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h3 class="text-xl font-bold text-white">Analítica de Vendedores</h3>
                    <p class="text-gray-400 text-sm">Comisiones (40% de 35.90) de hoteles activos.</p>
                </div>
                <!-- Cálculo del Total General -->
                @php
                    $totalCommissions = $sellers->sum(function($seller) {
                        return $seller->active_commissions;
                    });
                @endphp
                <div class="mt-4 sm:mt-0 bg-gray-800 rounded-xl px-4 py-2 border border-gray-700">
                    <span class="text-gray-400 text-xs font-medium uppercase tracking-wider block">Total a Pagar</span>
                    <span class="text-2xl font-black text-green-400">S/ {{ number_format($totalCommissions, 2) }}</span>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($sellers as $seller)
                <div class="p-5 border border-gray-100 rounded-2xl bg-gray-50/50 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">{{ $seller->full_name }}</h4>
                            <p class="text-xs text-gray-500">DNI: {{ $seller->dni }}</p>
                        </div>
                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $seller->active_clients_count }} Activos
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Comisión Mensual:</span>
                            <span class="text-xl font-black text-green-600">S/ {{ number_format($seller->active_commissions, 2) }}</span>
                        </div>
                        
                        <!-- Barra de pago (simulada por mes de 30 días) -->
                        <div>
                            <div class="flex justify-between text-[10px] text-gray-500 mb-1">
                                <span>Ciclo de pago</span>
                                <span>30 días</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-indigo-600 h-full animate-pulse" style="width: 75%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($sellers->isEmpty())
                <p class="text-center text-gray-500 py-8">No hay vendedores registrados aún.</p>
            @endif
        </div>
    </div>

    <!-- Modal Nuevo Hotel -->
    <div x-show="showHotelModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showHotelModal = false"><div class="absolute inset-0 bg-gray-900 opacity-75"></div></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('saas.hotel.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <h3 class="text-2xl font-black text-gray-900 mb-6">Nuevo Hotel</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Nombre del Hotel</label>
                                <input type="text" name="hotel_name" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Teléfono (WhatsApp)</label>
                                <input type="text" name="phone" placeholder="Ej: +51 905 562 625" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Ubicación (Google Maps)</label>
                                <input type="text" name="location" placeholder="Pegar enlace de Google Maps" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Email (Gmail obligatorio)</label>
                                <input type="email" name="email" required pattern=".+@gmail\.com" placeholder="ejemplo@gmail.com" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-[10px] text-gray-400 mt-1">Solo se permiten correos @gmail.com para el acceso.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Vendedor</label>
                                <select name="seller_id" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Venta Directa (Sin Vendedor)</option>
                                    @foreach($sellers as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Inicio</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex border rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="subscription_type" value="trial" checked class="mt-1 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-900">3 Días</span>
                                            <span class="block text-xs text-gray-500">Prueba Gratis</span>
                                        </div>
                                    </label>
                                    <label class="relative flex border rounded-xl p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="radio" name="subscription_type" value="monthly" class="mt-1 text-blue-600 focus:ring-blue-500">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-900">30 Días</span>
                                            <span class="block text-xs text-gray-500">Mes Completo</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 sm:px-8 flex justify-end space-x-3">
                        <button type="button" @click="showHotelModal = false" class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-gray-900 transition-colors">Cancelar</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-lg font-bold">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Nuevo Vendedor -->
    <div x-show="showSellerModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" @click="showSellerModal = false"><div class="absolute inset-0 bg-gray-900 opacity-75"></div></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;
            <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('saas.seller.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4 sm:p-8">
                        <h3 class="text-2xl font-black text-gray-900 mb-6">Nuevo Vendedor</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">DNI</label>
                                <input type="text" name="dni" required maxlength="8" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500" placeholder="8 dígitos">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Email (Gmail)</label>
                                <input type="email" name="email" required pattern=".+@gmail\.com" placeholder="ejemplo@gmail.com" class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-[10px] text-gray-400 mt-1">Se creará una cuenta de acceso para este vendedor.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Nombres</label>
                                <input type="text" name="names" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Apellidos</label>
                                <input type="text" name="surnames" required class="mt-1 block w-full rounded-xl border-gray-200 bg-gray-50 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 sm:px-8 flex justify-end space-x-3">
                        <button type="button" @click="showSellerModal = false" class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-gray-900 transition-colors">Cancelar</button>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg font-bold">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
