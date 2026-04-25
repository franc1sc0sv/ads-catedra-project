<nav class="bg-secondary shadow-md" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <span class="text-white font-bold text-lg">{{ config('app.name') }}</span>
                <span class="bg-white text-secondary text-xs font-semibold px-2 py-0.5 rounded-full">Sales</span>
            </div>
            <div class="hidden md:flex items-center gap-6">
                <span class="text-white/80 text-sm">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-white/70 hover:text-white text-sm transition-colors">Sign out</button>
                </form>
            </div>
            <button @click="open = !open" class="md:hidden text-white/80">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
    <div x-show="open" class="md:hidden bg-secondary/95 px-4 pb-3">
        <p class="text-white/70 text-sm py-2">{{ auth()->user()->name }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-white/70 text-sm">Sign out</button>
        </form>
    </div>
</nav>
