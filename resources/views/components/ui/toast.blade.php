<div
    x-data="uiToast()"
    x-on:toast.window="push($event.detail)"
    class="fixed bottom-4 right-4 z-[60] flex flex-col gap-2 pointer-events-none"
    aria-live="polite"
    aria-atomic="true"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-show="true"
            x-transition.opacity.duration.200ms
            :class="variantClass(t.variant)"
            class="pointer-events-auto rounded-lg shadow-lg px-4 py-3 text-sm min-w-[260px] max-w-sm flex items-start gap-3"
            role="status"
        >
            <span class="flex-1 leading-snug" x-text="t.message"></span>
            <button
                type="button"
                @click="dismiss(t.id)"
                class="opacity-80 hover:opacity-100 leading-none text-base"
                aria-label="Cerrar"
            >×</button>
        </div>
    </template>
</div>
