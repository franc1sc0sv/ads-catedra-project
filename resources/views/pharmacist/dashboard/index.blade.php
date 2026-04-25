@extends('layouts.app')

@section('title', 'Pharmacy Dashboard')

@section('nav')
    <x-nav.pharmacist-nav />
@endsection

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Pharmacy Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Manage prescriptions and medication dispensing.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-ui.card title="Today's Prescriptions">
            <p class="text-3xl font-bold text-primary">—</p>
            <p class="text-xs text-gray-400 mt-1">Dispensed today</p>
        </x-ui.card>
        <x-ui.card title="Pending Review">
            <p class="text-3xl font-bold text-accent">—</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting verification</p>
        </x-ui.card>
        <x-ui.card title="Drug Interactions">
            <p class="text-3xl font-bold text-green-500">0</p>
            <p class="text-xs text-gray-400 mt-1">Flagged today</p>
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
                    <span class="bg-secondary/10 text-secondary text-xs font-semibold px-2 py-1 rounded-full">
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
