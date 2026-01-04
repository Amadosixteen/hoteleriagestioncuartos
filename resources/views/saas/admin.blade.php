@extends('layouts.app')

@section('title', 'Administración SaaS - Hotelería')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-700">
            <h2 class="text-xl font-bold text-white">Panel de Gestión SaaS (Global)</h2>
            <p class="text-blue-100 text-sm">Gestiona todos los hoteles y sus suscripciones activas.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Hotel / Empresa</th>
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
                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $user->tenant ? $user->tenant->name : 'Sin Hotel' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->is_active)
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700">Activo</span>
                            @else
                                <span class="px-2 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700">Baneado</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm {{ $user->days_remaining <= 5 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                {{ $user->subscription_expires_at ? $user->subscription_expires_at->format('d/m/Y H:i') : 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-400">Quedan {{ $user->days_remaining }} días</div>
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
</div>
@endsection
