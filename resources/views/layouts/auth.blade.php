<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ setting('nombre_farmacia', config('app.name')) }} — @yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <x-ui._alpine-bootstrap />
    <script src="https://unpkg.com/alpinejs@3.14.1/dist/cdn.min.js" defer></script>
    @vite(['resources/tailwind/app.css'])
</head>
<body class="min-h-screen bg-primary flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-white tracking-wide">{{ setting('nombre_farmacia', config('app.name')) }}</h1>
            @if ($address = setting('direccion_farmacia'))
                <p class="text-white/70 text-sm tracking-wide mt-1">{{ $address }}</p>
            @endif
        </div>
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @yield('content')
        </div>
    </div>
</body>
</html>
