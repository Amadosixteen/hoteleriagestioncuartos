@extends('layouts.app')

@section('title', 'Gestionar Hoteles')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Seleccionar Hotel</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($tenants as $tenant)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between {{ auth()->user()->tenant_id == $tenant->id ? 'ring-2 ring-blue-500' : '' }}">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">{{ $tenant->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">Slug: {{ $tenant->slug }}</p>
                
                @if(auth()->user()->tenant_id == $tenant->id)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-2">
                    Actual
                </span>
                @endif
            </div>

            <div class="mt-6">
                <form action="{{ route('hotels.switch', $tenant->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ auth()->user()->tenant_id == $tenant->id ? 'opacity-50 cursor-not-allowed' : '' }}" {{ auth()->user()->tenant_id == $tenant->id ? 'disabled' : '' }}>
                        {{ auth()->user()->tenant_id == $tenant->id ? 'Seleccionado' : 'Cambiar a este Hotel' }}
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
