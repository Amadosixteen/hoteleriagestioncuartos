<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 leading-tight">
                        Panel de Vendedor
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Gestiona tus clientes y revisa tus comisiones.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                        {{ $sellerName }}
                    </span>
                </div>
            </div>

            <!-- Stats Logic is handled in Controller/Model, but let's calculate simpler stats here for display if not passed -->
            @php
                $totalClients = $tenants->count();
                $activeClients = $tenants->filter(function($t) { 
                    return $t->users->where('is_active', true)->filter(function($u) { return $u->hasActiveSubscription(); })->count() > 0; 
                })->count();
                // 35.90 price * 40% commission = 14.36 per active client
                $monthlyCommission = $activeClients * 35.90 * 0.40; 
            @endphp

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Clients -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Clientes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalClients }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Subscriptions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-50 text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Suscripciones Activas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $activeClients }}</p>
                        </div>
                    </div>
                </div>

                <!-- Monthly Commission -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-6 border border-gray-100">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-50 text-indigo-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Comisión Mensual (Est.)</p>
                            <p class="text-2xl font-bold text-gray-900">S/ {{ number_format($monthlyCommission, 2) }}</p>
                            <p class="text-xs text-gray-400">40% de suscripciones activas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clients Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Mis Hoteles y Suscripciones</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hotel / Cliente</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimiento</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comisión</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($tenants as $tenant)
                                @php 
                                    $user = $tenant->users->first(); 
                                    $statusColor = 'bg-gray-100 text-gray-800'; // Default undefined
                                    $statusText = 'Indefinido';
                                    
                                    if ($user) {
                                        if (!$user->is_active) {
                                            $statusColor = 'bg-red-100 text-red-800';
                                            $statusText = 'Baneado';
                                        } elseif (!$user->hasActiveSubscription()) {
                                            $statusColor = 'bg-yellow-100 text-yellow-800';
                                            $statusText = 'Vencido';
                                        } else {
                                            $statusColor = 'bg-green-100 text-green-800';
                                            $statusText = 'Activo'; 
                                            if ($user->subscription_type === 'trial') {
                                                $statusText = 'Prueba Gratis';
                                                $statusColor = 'bg-blue-100 text-blue-800';
                                            }
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $tenant->name }}</div>
                                        @if($user)
                                            <div class="text-xs text-gray-500 flex items-center gap-1.5">
                                                <span>{{ $user->email }}</span>
                                                @if($tenant->phone)
                                                    <span class="text-gray-300">•</span>
                                                    <span class="text-indigo-500 font-medium">{{ $tenant->phone }}</span>
                                                @endif
                                                @if($tenant->location)
                                                    <span class="text-gray-300">•</span>
                                                    <a href="{{ $tenant->location }}" target="_blank" class="text-indigo-600 hover:text-indigo-800" title="Ver ubicación">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-xs text-red-500">Sin usuario asignado</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user && $user->subscription_expires_at)
                                            <div class="text-sm text-gray-900">{{ $user->subscription_expires_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->days_remaining }} días restantes</div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user && $user->hasActiveSubscription() && $user->subscription_type !== 'trial')
                                            <span class="text-sm font-bold text-green-600">+ S/ 14.36</span>
                                        @else
                                            <span class="text-sm text-gray-400">S/ 0.00</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No tienes clientes asignados aún.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
