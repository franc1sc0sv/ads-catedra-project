<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('nombre_farmacia', config('app.name')) }} — @yield('title', 'FarmaSys')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
    @vite(['resources/tailwind/app.css'])
</head>
<body class="min-h-screen bg-gray-50 font-sans" x-data>
    @auth
        @php
            $navComponent = match (auth()->user()->role->value) {
                'administrator'     => 'nav.administrator-nav',
                'salesperson'       => 'nav.salesperson-nav',
                'inventory_manager' => 'nav.inventory-manager-nav',
                'pharmacist'        => 'nav.pharmacist-nav',
            };
        @endphp
        <x-dynamic-component :component="$navComponent" />
    @endauth

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>
</body>
</html>
