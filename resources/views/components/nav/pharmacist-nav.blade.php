@php
    $links = [
        ['label' => 'Dashboard',       'route' => 'pharmacist.dashboard'],
        ['label' => 'Cola de recetas', 'route' => 'pharmacist.queue'], // Cambiado para que coincida con tu web.php
        ['label' => 'Historial',       'route' => 'pharmacist.history'], 
    ];
@endphp

<nav class="bg-primary shadow-md" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <span class="text-white font-bold text-lg">{{ setting('nombre_farmacia', config('app.name')) }}</span>
                <x-ui.badge variant="green">Farmacia</x-ui.badge>
            </div>

            <div class="hidden md:flex items-center gap-1">
                @foreach ($links as $link)
                    @if (\Illuminate\Support\Facades\Route::has($link['route']))
                        <a href="{{ route($link['route']) }}"
                           class="px-3 py-2 rounded-md text-sm transition-colors {{ request()->routeIs($link['route']) ? 'bg-white/20 text-white' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                            {{ $link['label'] }}
                        </a>
                    @else
                        <span class="px-3 py-2 rounded-md text-sm text-white/30 cursor-not-allowed" title="Ruta no definida">
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

    {{-- Menú móvil --}}
    <div x-show="open" x-cloak class="md:hidden bg-primary/95 px-4 pb-4 space-y-1">
        @foreach ($links as $link)
            @if (\Illuminate\Support\Facades\Route::has($link['route']))
                <a href="{{ route($link['route']) }}" 
                   class="block px-3 py-2 rounded-md text-sm {{ request()->routeIs($link['route']) ? 'bg-white/20 text-white' : 'text-white/80' }}">
                   {{ $link['label'] }}
                </a>
            @else
                <span class="block px-3 py-2 rounded-md text-sm text-white/40 cursor-not-allowed">{{ $link['label'] }}</span>
            @endif
        @endforeach
        <div class="border-t border-white/10 mt-2 pt-2">
            <p class="text-white/70 text-sm px-3 mb-2">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-3 py-2 text-white/80 text-sm hover:text-white">Cerrar sesión</button>
            </form>
        </div>
    </div>
</nav>