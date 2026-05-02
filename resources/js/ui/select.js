function uiSelectFactory({ name, value = null, options = [], searchable = false, required = false, disabled = false, hasError = false, placeholder = 'Seleccionar...' } = {}) {
    return {
        name,
        options: Array.isArray(options) ? options : [],
        searchable: !!searchable,
        required: !!required,
        disabled: !!disabled,
        hasError: !!hasError,
        placeholder: placeholder || 'Seleccionar...',
        open: false,
        query: '',
        highlight: 0,
        selected: null,
        typeAheadBuffer: '',
        typeAheadTimer: null,
        _initialValue: value,

        init() {
            try {
                this.selected = this.options.find(o => String(o.value) === String(this._initialValue)) ?? null;
                this.$watch('open', (isOpen) => {
                    if (isOpen) {
                        this.query = '';
                        this.highlight = Math.max(0, this.filtered.findIndex(o => o === this.selected));
                        this.$nextTick(() => {
                            if (this.searchable) this.$refs.search?.focus();
                        });
                    }
                });
            } catch (e) {
                console.error('[ui-select] init failed', e);
            }
        },

        get filtered() {
            if (!this.searchable || !this.query) return this.options;
            const q = this.query.toLowerCase();
            return this.options.filter(o => String(o.label).toLowerCase().includes(q));
        },

        toggle() {
            if (this.disabled) return;
            this.open = !this.open;
        },

        close() {
            this.open = false;
        },

        pick(opt) {
            this.selected = opt;
            this.open = false;
            this.$nextTick(() => {
                const native = this.$refs.native;
                if (native) {
                    native.value = opt?.value ?? '';
                    native.dispatchEvent(new Event('change', { bubbles: true }));
                    native.dispatchEvent(new Event('input', { bubbles: true }));
                }
                this.$refs.trigger?.focus();
            });
        },

        clear() {
            this.selected = null;
            const native = this.$refs.native;
            if (native) {
                native.value = '';
                native.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },

        onTriggerKey(e) {
            if (this.disabled) return;
            if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) {
                e.preventDefault();
                this.open = true;
            }
        },

        onPanelKey(e) {
            const items = this.filtered;
            if (e.key === 'Escape') {
                e.preventDefault();
                this.close();
                this.$refs.trigger?.focus();
                return;
            }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.highlight = (this.highlight + 1) % Math.max(items.length, 1);
                this.scrollToHighlight();
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.highlight = (this.highlight - 1 + items.length) % Math.max(items.length, 1);
                this.scrollToHighlight();
                return;
            }
            if (e.key === 'Home') {
                e.preventDefault();
                this.highlight = 0;
                this.scrollToHighlight();
                return;
            }
            if (e.key === 'End') {
                e.preventDefault();
                this.highlight = items.length - 1;
                this.scrollToHighlight();
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                if (items[this.highlight]) this.pick(items[this.highlight]);
                return;
            }
            if (!this.searchable && e.key.length === 1 && /\S/.test(e.key)) {
                this.typeAheadBuffer += e.key.toLowerCase();
                clearTimeout(this.typeAheadTimer);
                this.typeAheadTimer = setTimeout(() => { this.typeAheadBuffer = ''; }, 500);
                const idx = items.findIndex(o => String(o.label).toLowerCase().startsWith(this.typeAheadBuffer));
                if (idx >= 0) {
                    this.highlight = idx;
                    this.scrollToHighlight();
                }
            }
        },

        scrollToHighlight() {
            this.$nextTick(() => {
                const list = this.$refs.list;
                if (!list) return;
                const el = list.children[this.highlight];
                if (el && el.scrollIntoView) el.scrollIntoView({ block: 'nearest' });
            });
        },
    };
}

window.uiSelect = uiSelectFactory;

document.addEventListener('alpine:init', () => {
    if (window.Alpine && typeof window.Alpine.data === 'function') {
        window.Alpine.data('uiSelect', uiSelectFactory);
    }
});
