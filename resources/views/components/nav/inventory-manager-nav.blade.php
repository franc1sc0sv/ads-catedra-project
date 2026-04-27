@php
    $links = [
        ['label' => 'Dashboard',    'route' => 'inventory-manager.dashboard'],
        ['label' => 'Catálogo',     'route' => 'inventory-manager.catalogo.index'],
        ['label' => 'Alertas',      'route' => 'inventory-manager.alertas.index'],
        ['label' => 'Movimientos',  'route' => 'inventory-manager.movimientos.index'],
        ['label' => 'Proveedores',  'route' => 'inventory-manager.proveedores.index'],
        ['label' => 'Pedidos',      'route' => 'inventory-manager.pedidos.index'],
    ];
@endphp

<nav class="bg-neutralDark shadow-md" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <span class="text-white font-bold text-lg">{{ config('app.name') }}</span>
                <x-ui.badge variant="yellow">Inventario</x-ui.badge>
            </div>

            <div class="hidden md:flex items-center gap-1">
                @foreach ($links as $link)
                    @if (\Illuminate\Support\Facades\Route::has($link['route']))
                        <a href="{{ route($link['route']) }}"
                           class="px-3 py-2 rounded-md text-sm text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                            {{ $link['label'] }}
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-md text-sm text-white/40 cursor-not-allowed disabled" aria-disabled="true">
                            {{ $link['label'] }}
                        </span>
                    @endif
                @endforeach
            </div>

            <div class="hidden md:flex items-center gap-4">
                <span class="text-white/80 text-sm">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-white/70 hover:text-white text-sm transition-colors">
                        Cerrar sesión
                    </button>
                </form>
            </div>

            <button @click="open = !open" class="md:hidden text-white/80" aria-label="Abrir menú">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak class="md:hidden bg-neutralDark/95 px-4 pb-4 space-y-1">
        @foreach ($links as $link)
            @if (\Illuminate\Support\Facades\Route::has($link['route']))
                <a href="{{ route($link['route']) }}" class="block px-3 py-2 rounded-md text-sm text-white/80 hover:bg-white/10">{{ $link['label'] }}</a>
            @else
                <span class="block px-3 py-2 rounded-md text-sm text-white/40 disabled">{{ $link['label'] }}</span>
            @endif
        @endforeach
        <p class="text-white/70 text-sm py-2">{{ auth()->user()->name }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-white/80 text-sm hover:text-white">Cerrar sesión</button>
        </form>
    </div>
</nav>
