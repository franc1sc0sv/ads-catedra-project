<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Se cambió el título para que use Pharma System por defecto --}}
    <title>Pharma System — @yield('title', 'Inicio')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Scripts y Estilos --}}
    <script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
    @vite(['resources/tailwind/app.css'])
</head>
<body class="min-h-screen bg-gray-50 font-sans" x-data>
    @auth
        @php
            // Lógica para cargar el menú según el rol del usuario
            $navComponent = match (auth()->user()->role->value) {
                'administrator'     => 'nav.administrator-nav',
                'salesperson'       => 'nav.salesperson-nav',
                'inventory_manager' => 'nav.inventory-manager-nav',
                'pharmacist'        => 'nav.pharmacist-nav',
                default             => 'nav.salesperson-nav', // Rol por defecto
            };
        @endphp
        
        {{-- Componente de navegación dinámico --}}
        <x-dynamic-component :component="$navComponent" />
    @endauth

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Mensajes de Alerta (Éxito/Error) --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 text-sm font-bold rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 text-sm font-bold rounded shadow-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- Contenido de las páginas --}}
        @yield('content')
    </main>
</body>
</html>