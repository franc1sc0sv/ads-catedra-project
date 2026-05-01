@props([
    'placeholder' => 'Buscar cliente por nombre o DUI…',
    'name' => 'customer_id',
])

<div
    x-data="clientesBusqueda()"
    x-init="init()"
    @click.outside="closeDropdown()"
    class="relative w-full"
>
    <input type="hidden" :name="@js($name)" :value="selected?.id ?? ''">

    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
        Cliente
    </label>

    <div class="relative">
        <input
            type="text"
            x-model="query"
            @input.debounce.300ms="performSearch()"
            @focus="openDropdown()"
            :placeholder="selected ? selected.name + ' (' + selected.identification + ')' : @js($placeholder)"
            class="w-full px-4 py-3 pr-10 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        >
        <template x-if="selected">
            <button
                type="button"
                @click="clearSelected()"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"
                aria-label="Limpiar selección"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </template>
    </div>

    <div
        x-show="dropdownOpen"
        x-cloak
        class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-lg max-h-72 overflow-y-auto"
    >
        <template x-if="loading">
            <div class="px-4 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest">Buscando…</div>
        </template>

        <template x-if="!loading && results.length > 0">
            <ul class="divide-y divide-gray-50">
                <template x-for="result in results" :key="result.id">
                    <li>
                        <button
                            type="button"
                            @click="selectResult(result)"
                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 transition-colors flex items-center justify-between"
                        >
                            <div>
                                <p class="text-sm font-bold text-gray-900" x-text="result.name"></p>
                                <p class="text-xs font-mono text-gray-500" x-text="result.identification"></p>
                            </div>
                            <template x-if="result.is_frequent">
                                <span class="px-2 py-1 bg-amber-100 text-amber-600 text-[9px] font-black uppercase rounded">Frecuente ★</span>
                            </template>
                        </button>
                    </li>
                </template>
            </ul>
        </template>

        <template x-if="!loading && results.length === 0">
            <div class="px-4 py-3 text-center">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-2">Sin resultados</p>
                <button
                    type="button"
                    @click="openModal()"
                    class="text-xs font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest"
                >+ Crear cliente</button>
            </div>
        </template>
    </div>

    <div
        x-show="modalOpen"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        @keydown.escape.window="modalOpen = false"
    >
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.outside="modalOpen = false">
            <h3 class="text-lg font-black text-gray-900 mb-4">Crear cliente</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nombre</label>
                    <input type="text" x-model="form.name" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Teléfono</label>
                    <input type="text" x-model="form.phone" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">DUI / Identificación</label>
                    <input type="text" x-model="form.identification" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                </div>

                <template x-if="modalError">
                    <p class="text-xs font-bold text-red-600" x-text="modalError"></p>
                </template>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" @click="modalOpen = false" class="px-4 py-2 text-xs font-black uppercase tracking-widest text-gray-500 hover:text-gray-700">Cancelar</button>
                <button type="button" @click="submitQuickCreate()" :disabled="modalSubmitting" class="px-4 py-2 bg-indigo-600 text-white text-xs font-black uppercase tracking-widest rounded-lg hover:bg-indigo-700 disabled:opacity-50">Guardar</button>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    function clientesBusqueda() {
        return {
            query: '',
            results: [],
            loading: false,
            dropdownOpen: false,
            selected: null,
            modalOpen: false,
            modalSubmitting: false,
            modalError: null,
            form: { name: '', phone: '', identification: '' },
            searchUrl: @js(route('clientes.buscar')),
            quickCreateUrl: @js(route('clientes.quick-create')),
            csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',

            init() {},

            openDropdown() {
                this.dropdownOpen = true;
                if (this.results.length === 0) {
                    this.performSearch();
                }
            },

            closeDropdown() {
                this.dropdownOpen = false;
            },

            async performSearch() {
                this.loading = true;
                this.dropdownOpen = true;

                try {
                    const url = new URL(this.searchUrl, window.location.origin);
                    url.searchParams.set('q', this.query ?? '');

                    const response = await fetch(url.toString(), {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });

                    this.results = response.ok ? await response.json() : [];
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },

            selectResult(result) {
                this.selected = result;
                this.query = '';
                this.dropdownOpen = false;
                this.$dispatch('cliente-seleccionado', { id: result.id, nombre: result.name, frecuente: result.is_frequent });
            },

            clearSelected() {
                this.selected = null;
                this.$dispatch('cliente-seleccionado', null);
            },

            openModal() {
                this.form = { name: this.query, phone: '', identification: '' };
                this.modalError = null;
                this.modalOpen = true;
            },

            async submitQuickCreate() {
                this.modalSubmitting = true;
                this.modalError = null;

                try {
                    const response = await fetch(this.quickCreateUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf,
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(this.form),
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        this.modalError = data?.errors?.identificacion_duplicada?.[0]
                            ?? 'Datos inválidos. Verifica los campos.';
                        return;
                    }

                    if (!response.ok) {
                        this.modalError = 'No se pudo crear el cliente.';
                        return;
                    }

                    const created = await response.json();
                    this.modalOpen = false;
                    this.selectResult({
                        id: created.id,
                        name: created.name,
                        identification: created.identification,
                        is_frequent: created.is_frequent,
                    });
                } finally {
                    this.modalSubmitting = false;
                }
            },
        };
    }
</script>
@endpush
@endonce
