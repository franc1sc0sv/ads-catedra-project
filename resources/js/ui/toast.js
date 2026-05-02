function uiToastFactory() {
    return {
        toasts: [],
        seq: 0,

        push(detail = {}) {
            const id = ++this.seq;
            const toast = {
                id,
                message: detail.message ?? '',
                variant: detail.variant ?? 'info',
                duration: detail.duration ?? 3000,
            };
            this.toasts.push(toast);
            if (toast.duration > 0) {
                setTimeout(() => this.dismiss(id), toast.duration);
            }
        },

        dismiss(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },

        variantClass(variant) {
            return {
                success: 'bg-secondary text-white',
                warning: 'bg-accent text-neutralDark',
                danger: 'bg-coral text-white',
                info: 'bg-primary text-white',
            }[variant] ?? 'bg-primary text-white';
        },
    };
}

window.uiToast = uiToastFactory;

document.addEventListener('alpine:init', () => {
    if (window.Alpine && typeof window.Alpine.data === 'function') {
        window.Alpine.data('uiToast', uiToastFactory);
    }
});
