@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('nav')
    <x-nav.inventory-nav />
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Inventory Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Monitor stock levels and manage product inventory.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-ui.card title="Total Products">
            <p class="text-3xl font-bold text-primary">—</p>
            <p class="text-xs text-gray-400 mt-1">SKUs in system</p>
        </x-ui.card>
        <x-ui.card title="Low Stock Alerts">
            <p class="text-3xl font-bold text-coral">—</p>
            <p class="text-xs text-gray-400 mt-1">Below minimum level</p>
        </x-ui.card>
        <x-ui.card title="Pending Restocks">
            <p class="text-3xl font-bold text-accent">—</p>
            <p class="text-xs text-gray-400 mt-1">Orders in transit</p>
        </x-ui.card>
    </div>

    <x-ui.card title="Account Info">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-400">Name</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ auth()->user()->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Email</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ auth()->user()->email }}</dd>
            </div>
            <div>
                <dt class="text-gray-400">Role</dt>
                <dd class="mt-0.5">
                    <span class="bg-accent/20 text-neutralDark text-xs font-semibold px-2 py-1 rounded-full">
                        {{ auth()->user()->role->label() }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-gray-400">Member since</dt>
                <dd class="font-medium text-gray-900 mt-0.5">{{ auth()->user()->created_at->format('M d, Y') }}</dd>
            </div>
        </dl>
    </x-ui.card>
@endsection
